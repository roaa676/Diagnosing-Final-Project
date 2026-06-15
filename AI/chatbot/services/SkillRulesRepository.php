<?php

namespace App\Services\Chatbot;

/**
 * SkillRulesRepository
 *
 * Loads skill_rules.json and score_rules.json from the AI/chatbot/rules/ directory.
 * Provides structured access to skill explanations, exercise recommendations,
 * and score band definitions used by the chatbot.
 *
 * Integration path: copy to backend/app/Services/Chatbot/SkillRulesRepository.php
 */
class SkillRulesRepository
{
    private array $skillRules;
    private array $scoreRules;

    private string $skillRulesPath;
    private string $scoreRulesPath;

    public function __construct()
    {
        // Paths are relative to the Laravel base_path()
        // Adjust if the AI folder is outside the Laravel root
        $this->skillRulesPath = base_path('../AI/chatbot/rules/skill_rules.json');
        $this->scoreRulesPath = base_path('../AI/chatbot/rules/score_rules.json');

        $this->skillRules = $this->loadJson($this->skillRulesPath);
        $this->scoreRules = $this->loadJson($this->scoreRulesPath);
    }

    // ─────────────────────────────────────────────
    // Skill Rules
    // ─────────────────────────────────────────────

    /**
     * Return the full rule definition for a given skill_id.
     */
    public function getSkillRule(string $skillId): ?array
    {
        foreach ($this->skillRules['skills'] ?? [] as $skill) {
            if ($skill['skill_id'] === $skillId) {
                return $skill;
            }
        }
        return null;
    }

    /**
     * Return the Arabic name of a skill.
     */
    public function getSkillArabicName(string $skillId): string
    {
        $skill = $this->getSkillRule($skillId);
        return $skill['arabic_name'] ?? $skillId;
    }

    /**
     * Return the low-score explanation for a skill.
     */
    public function getSkillLowScoreExplanation(string $skillId): string
    {
        $skill = $this->getSkillRule($skillId);
        return $skill['low_score_explanation'] ?? 'يحتاج الطفل إلى تمارين إضافية في هذه المهارة.';
    }

    /**
     * Return exercises for a specific skill at a given training level (1, 2, or 3).
     */
    public function getExercisesForSkill(string $skillId, int $level): array
    {
        $skill = $this->getSkillRule($skillId);
        if (!$skill) {
            return [];
        }

        $levelKey = 'level_' . $level;
        return $skill['exercises'][$levelKey]['activities'] ?? [];
    }

    /**
     * Return a formatted Arabic list of exercise names for a skill and level.
     */
    public function getExerciseListArabic(string $skillId, int $level): string
    {
        $exercises = $this->getExercisesForSkill($skillId, $level);
        if (empty($exercises)) {
            return 'لا توجد تمارين محددة لهذا المستوى.';
        }

        $lines = [];
        foreach ($exercises as $exercise) {
            $lines[] = '- ' . ($exercise['name'] ?? '') . ': ' . ($exercise['description'] ?? '');
        }
        return implode("\n", $lines);
    }

    /**
     * Return all skills for a given difficulty category (dyslexia / dyscalculia).
     */
    public function getSkillsByCategory(string $category): array
    {
        return array_filter(
            $this->skillRules['skills'] ?? [],
            fn($skill) => ($skill['category'] ?? '') === $category
        );
    }

    /**
     * Return parent tips for a skill.
     */
    public function getParentTips(string $skillId): array
    {
        $skill = $this->getSkillRule($skillId);
        return $skill['parent_tips'] ?? [];
    }

    // ─────────────────────────────────────────────
    // Score Rules
    // ─────────────────────────────────────────────

    /**
     * Return the score band definition for a numeric score (0-100).
     */
    public function getScoreBand(int $score): array
    {
        foreach ($this->scoreRules['score_bands'] ?? [] as $band) {
            $min = $band['range']['min'];
            $max = $band['range']['max'];
            if ($score >= $min && $score <= $max) {
                return $band;
            }
        }

        // Default: return the last band (good performance)
        return end($this->scoreRules['score_bands']);
    }

    /**
     * Return the Arabic label for a score.
     */
    public function getScoreArabicLabel(int $score): string
    {
        return $this->getScoreBand($score)['arabic_label'] ?? 'غير محدد';
    }

    /**
     * Return the recommended training level for a score.
     */
    public function getRecommendedTrainingLevel(int $score): int
    {
        return $this->getScoreBand($score)['recommended_training_level'] ?? 1;
    }

    /**
     * Return the chatbot message for a score band.
     */
    public function getScoreChatbotMessage(int $score): string
    {
        return $this->getScoreBand($score)['chatbot_message'] ?? '';
    }

    /**
     * Return whether specialist referral is recommended for this score.
     */
    public function isSpecialistReferralRecommended(int $score): bool
    {
        return $this->getScoreBand($score)['specialist_referral_recommended'] ?? false;
    }

    /**
     * Map a risk_level string (from GameResult model) to a chatbot severity.
     */
    public function mapRiskLevelToSeverity(string $riskLevel): string
    {
        $mapping = $this->scoreRules['risk_level_to_chatbot_severity']['mapping'] ?? [];
        return $mapping[$riskLevel] ?? 'unknown';
    }

    /**
     * Return the mandatory disclaimer text (Arabic).
     */
    public function getMandatoryDisclaimer(): string
    {
        return $this->scoreRules['mandatory_disclaimer']
            ?? 'هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.';
    }

    /**
     * Return the list of triggers that recommend specialist referral.
     */
    public function getSpecialistReferralTriggers(): array
    {
        return $this->scoreRules['specialist_referral_triggers'] ?? [];
    }

    // ─────────────────────────────────────────────
    // Q&A Dataset
    // ─────────────────────────────────────────────

    /**
     * Return all intents from qa_dataset.json.
     * Each intent has: id, arabic_label, keywords[], response
     */
    public function getQADataset(): array
    {
        $path = base_path('../AI/chatbot/rules/qa_dataset.json');
        return $this->loadJson($path)['intents'] ?? [];
    }

    // ─────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────

    private function loadJson(string $path): array
    {
        if (!file_exists($path)) {
            // Silently return empty; log a warning in production
            return [];
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?? [];
    }
}
