<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ChatbotService
 *
 * Orchestrates the full chatbot pipeline:
 *   1. Build the system prompt (from prompts/system_prompt_ar.md)
 *   2. Inject child context JSON into the user message
 *   3. Call the OpenAI Chat Completions API
 *   4. Run SafetyFilter on the response
 *   5. Return the final Arabic response to the controller
 *
 * Falls back to a rule-based Arabic response if:
 *   - OPENAI_API_KEY is missing
 *   - The API call fails
 *
 * Integration path: copy to backend/app/Services/Chatbot/ChatbotService.php
 *
 * Required .env keys:
 *   OPENAI_API_KEY=sk-...
 *   OPENAI_MODEL=gpt-4o           (optional, defaults to gpt-4o)
 *   OPENAI_MAX_TOKENS=1000        (optional)
 */
class ChatbotService
{
    private SkillRulesRepository $skillRules;
    private SafetyFilter $safetyFilter;

    private string $openAiKey;
    private string $model;
    private int $maxTokens;

    private string $systemPromptPath;

    public function __construct(
        SkillRulesRepository $skillRules,
        SafetyFilter $safetyFilter
    ) {
        $this->skillRules   = $skillRules;
        $this->safetyFilter = $safetyFilter;

        $this->openAiKey        = config('services.openai.key', '');
        $this->model            = config('services.openai.model', 'gpt-4o');
        $this->maxTokens        = (int) config('services.openai.max_tokens', 1000);
        $this->systemPromptPath = base_path('../AI/chatbot/prompts/system_prompt_ar.md');
    }

    // ─────────────────────────────────────────────
    // Public entry points (called by ChatbotController)
    // ─────────────────────────────────────────────

    /**
     * Handle a free-text question from a parent.
     *
     * @param string $message      The parent's Arabic question
     * @param array  $childContext The structured context built by ResultContextBuilder
     * @return array ['answer' => string]
     */
    public function ask(string $message, array $childContext = []): array
    {
        if (empty($this->openAiKey)) {
            return ['answer' => $this->buildRuleBasedAnswer($message, $childContext)];
        }

        $systemPrompt = $this->loadSystemPrompt();
        $userMessage  = $this->buildAskMessage($message, $childContext);
        $answer       = $this->callOpenAI($systemPrompt, $userMessage, requireDisclaimer: false)
                        ?? $this->buildRuleBasedAnswer($message, $childContext);

        return ['answer' => $answer];
    }

    /**
     * Generate an explanation of the child's full result for the parent.
     *
     * @param array $childContext  Built by ResultContextBuilder::buildContext()
     * @return array ['answer' => string]
     */
    public function explainResult(array $childContext): array
    {
        $fallback = fn() => $this->safetyFilter->buildFallbackResponse($childContext, $this->skillRules);

        if (empty($this->openAiKey)) {
            return ['answer' => $fallback()];
        }

        $systemPrompt      = $this->loadSystemPrompt();
        $explanationPrompt = $this->loadPromptFile(
            base_path('../AI/chatbot/prompts/parent_result_explanation_prompt.md')
        );
        $userMessage = $this->buildExplainResultMessage($childContext, $explanationPrompt);
        $answer      = $this->callOpenAI($systemPrompt, $userMessage, requireDisclaimer: true)
                       ?? $fallback();

        return ['answer' => $answer];
    }

    /**
     * Generate exercise recommendations based on the child's result.
     *
     * @param array $childContext  Built by ResultContextBuilder
     * @return array ['answer' => string, 'recommended_exercises' => array]
     */
    public function recommendExercises(array $childContext): array
    {
        $structuredExercises = $this->buildStructuredExerciseList($childContext);

        if (empty($this->openAiKey)) {
            return ['answer' => $this->buildRuleBasedExerciseAnswer($structuredExercises), 'recommended_exercises' => $structuredExercises];
        }

        $systemPrompt   = $this->loadSystemPrompt();
        $exercisePrompt = $this->loadPromptFile(
            base_path('../AI/chatbot/prompts/exercise_recommendation_prompt.md')
        );
        $userMessage = $this->buildExerciseMessage($childContext, $exercisePrompt);
        $answer      = $this->callOpenAI($systemPrompt, $userMessage, requireDisclaimer: false)
                       ?? $this->buildRuleBasedExerciseAnswer($structuredExercises);

        return ['answer' => $answer, 'recommended_exercises' => $structuredExercises];
    }

    // ─────────────────────────────────────────────
    // OpenAI call
    // ─────────────────────────────────────────────

    private function callOpenAI(string $systemPrompt, string $userMessage, bool $requireDisclaimer): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openAiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $this->model,
                    'max_tokens'  => $this->maxTokens,
                    'temperature' => 0.4,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userMessage],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('[Chatbot] OpenAI API error: ' . $response->status() . ' ' . $response->body());
                return null;
            }

            $rawAnswer = $response->json('choices.0.message.content', '');

            if (empty(trim($rawAnswer))) {
                return null;
            }

            $safeResult = $this->safetyFilter->check($rawAnswer, $requireDisclaimer);

            if (!$safeResult['safe']) {
                Log::warning('[Chatbot] SafetyFilter violations: ' . \implode(', ', $safeResult['violations']));
            }

            return $safeResult['response'];

        } catch (\Throwable $e) {
            Log::error('[Chatbot] Exception during OpenAI call: ' . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────
    // Message builders
    // ─────────────────────────────────────────────

    private function buildAskMessage(string $question, array $childContext): string
    {
        $contextBlock = '';
        if (!empty($childContext)) {
            $contextBlock = "\n\n===== بيانات الطفل المتاحة =====\n"
                . json_encode($childContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                . "\n=================================\n";
        }

        return $contextBlock . "\nسؤال ولي الأمر: {$question}";
    }

    private function buildExplainResultMessage(array $childContext, string $promptTemplate): string
    {
        $contextJson = json_encode($childContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return str_replace('{CHILD_CONTEXT_JSON}', $contextJson, $promptTemplate)
            ?: "بيانات الطفل:\n{$contextJson}\n\nاشرح لولي الأمر نتيجة الطفل بالكامل باللغة العربية البسيطة الودية.";
    }

    private function buildExerciseMessage(array $childContext, string $promptTemplate): string
    {
        $contextJson = json_encode($childContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return str_replace('{CHILD_CONTEXT_JSON}', $contextJson, $promptTemplate)
            ?: "بيانات الطفل:\n{$contextJson}\n\nقدّم خطة تمارين منزلية مخصصة بالعربية.";
    }

    // ─────────────────────────────────────────────
    // Structured exercise list (rule-based, not LLM)
    // ─────────────────────────────────────────────

    private function buildStructuredExerciseList(array $childContext): array
    {
        $result = [];
        $skills = $childContext['skills'] ?? [];

        foreach ($skills as $skill) {
            if ($skill['level'] === 'good_performance') {
                continue;
            }

            $trainingLevel = $skill['recommended_training_level'];
            $exercises     = $this->skillRules->getExercisesForSkill(
                $skill['skill_id'],
                $trainingLevel
            );

            $result[] = [
                'skill_id'       => $skill['skill_id'],
                'arabic_name'    => $skill['arabic_name'],
                'training_level' => $trainingLevel,
                'exercises'      => $exercises,
            ];
        }

        return $result;
    }

    // ─────────────────────────────────────────────
    // Rule-based fallback answers (no API key)
    // ─────────────────────────────────────────────

    private function buildRuleBasedAnswer(string $message, array $childContext): string
    {
        $intents    = $this->skillRules->getQADataset();
        $bestScore  = 0;
        $bestIntent = null;

        foreach ($intents as $intent) {
            $score = 0;
            foreach ($intent['keywords'] as $keyword) {
                if (\stripos($message, $keyword) !== false) {
                    // Weight longer/more-specific keywords higher
                    $score += \mb_strlen($keyword) > 6 ? 2 : 1;
                }
            }
            if ($score > $bestScore) {
                $bestScore  = $score;
                $bestIntent = $intent;
            }
        }

        if ($bestScore >= 1 && $bestIntent !== null) {
            $answer = $bestIntent['response'];

            // For exercise-related intents, append child-specific exercises if context available
            if (
                \in_array($bestIntent['id'], ['exercises_request', 'home_routine', 'next_steps'], true)
                && !empty($childContext['skills'])
            ) {
                $answer .= "\n\n" . $this->buildHomeActivitiesAnswer($childContext);
            }

            return $answer;
        }

        // No intent matched — fall back to context-aware generic
        if (!empty($childContext['skills'])) {
            return $this->buildFallbackWithContext($childContext);
        }

        return "شكراً على سؤالك!\n\nمنصة بوصلة تساعدك على فهم أداء طفلك في مهارات القراءة والحساب.\n\nإذا أردت شرح نتيجة محددة أو توصية تمارين، يمكنك استخدام أقسام شرح النتيجة وتوصية التمارين في التطبيق.\n\nللأسئلة الطبية الدقيقة، يُنصح دائماً باستشارة متخصص في صعوبات التعلم.";
    }

    private function buildRuleBasedExerciseAnswer(array $structuredExercises): string
    {
        if (empty($structuredExercises)) {
            return "أداء الطفل جيد في جميع المهارات المقاسة. واصل التمارين الحالية للحفاظ على هذا المستوى.\n\n*هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.*";
        }

        $lines = ["أحسنت على اهتمامك بمتابعة طفلك! إليك خطة تمارين مقترحة بناءً على نتائج بوصلة:\n"];

        foreach ($structuredExercises as $item) {
            $lines[] = "**{$item['arabic_name']} — المستوى {$item['training_level']}:**";
            foreach (array_slice($item['exercises'], 0, 4) as $ex) {
                $lines[] = "- {$ex['name']}: {$ex['description']}";
            }
            $lines[] = '';
        }

        $lines[] = "**نصائح عامة:**";
        $lines[] = "- 10-15 دقيقة يومياً كافية، لا تُطوّل الجلسة";
        $lines[] = "- اجعل التمارين في شكل لعبة وليس واجباً";
        $lines[] = "- احتفل بكل إجابة صحيحة";
        $lines[] = '';
        $lines[] = "*هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.*";

        return \implode("\n", $lines);
    }

    private function buildHomeActivitiesAnswer(array $childContext): string
    {
        $lines = ["بناءً على نتائج طفلك، إليك تمارين منزلية مقترحة:\n"];
        foreach ($childContext['skills'] ?? [] as $skill) {
            if ($skill['level'] === 'good_performance') continue;
            $level     = $skill['recommended_training_level'];
            $exercises = $this->skillRules->getExercisesForSkill($skill['skill_id'], $level);
            $lines[]   = "**{$skill['arabic_name']}:**";
            foreach (array_slice($exercises, 0, 3) as $ex) {
                $lines[] = "- {$ex['name']}";
            }
            $lines[] = '';
        }
        $lines[] = "خصّص 10-15 دقيقة يومياً واجعل التمارين في شكل لعبة ممتعة.";
        return \implode("\n", $lines);
    }

    private function buildFallbackWithContext(array $childContext): string
    {
        $primary = $childContext['chatbot_context_notes']['primary_concern'] ?? null;
        $name    = $primary ? $this->skillRules->getSkillArabicName($primary) : null;

        $lines = ["بناءً على نتائج طفلك في بوصلة:"];
        if ($name) {
            $lines[] = "\nالمهارة التي تحتاج أكبر اهتمام: **{$name}**";
            $lines[] = $this->skillRules->getSkillLowScoreExplanation($primary);
            $lines[] = "\nتمارين مقترحة:";
            $exercises = $this->skillRules->getExercisesForSkill($primary, 1);
            foreach (array_slice($exercises, 0, 3) as $ex) {
                $lines[] = "- {$ex['name']}";
            }
        }
        $lines[] = "\n*هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.*";
        return \implode("\n", $lines);
    }

    // ─────────────────────────────────────────────
    // Prompt loading
    // ─────────────────────────────────────────────

    private function loadSystemPrompt(): string
    {
        return $this->loadPromptFile($this->systemPromptPath);
    }

    private function loadPromptFile(string $path): string
    {
        if (!file_exists($path)) {
            Log::warning("[Chatbot] Prompt file not found: {$path}");
            return '';
        }
        return file_get_contents($path);
    }
}
