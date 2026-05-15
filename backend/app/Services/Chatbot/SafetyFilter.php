<?php

namespace App\Services\Chatbot;

/**
 * SafetyFilter
 *
 * Validates chatbot responses before delivering them to parents.
 * Detects forbidden medical claims, ensures the disclaimer is present,
 * and sanitizes language that may frighten or mislead parents.
 *
 * Integration path: copy to backend/app/Services/Chatbot/SafetyFilter.php
 */
class SafetyFilter
{
    /**
     * Phrases that indicate a definitive diagnosis — never allowed in a response.
     */
    private array $forbiddenDiagnosisPhrases = [
        'مصاب بعسر القراءة',
        'مصاب بعسر الحساب',
        'مصاب بـ',
        'يعاني من عسر القراءة المؤكد',
        'تشخيص مؤكد',
        'لديه ديسلكسيا',
        'طفلك مريض',
        'هذا مرض',
        'اضطراب مؤكد',
        'diagnosed with',
    ];

    /**
     * Phrases that unnecessarily frighten parents — should be softened.
     */
    private array $frighteningPhrases = [
        'الحالة خطيرة',
        'خطر شديد',
        'مقلق جداً',
        'يجب التدخل الفوري',
        'حالة طارئة',
        'لا أمل',
        'لن يتحسن',
    ];

    /**
     * Medication-related phrases — never allowed.
     */
    private array $medicationPhrases = [
        'دواء',
        'علاج دوائي',
        'أدوية',
        'ريتالين',
        'كونسرتا',
        'medication',
        'drug',
        'pill',
    ];

    /**
     * The disclaimer that must be present in every result-explanation response.
     */
    private string $mandatoryDisclaimer = 'هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.';

    // ─────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────

    /**
     * Run the full safety check on a chatbot response.
     *
     * Returns an array:
     *  - 'safe'    => bool
     *  - 'response' => string  (possibly modified)
     *  - 'violations' => array (list of detected issues)
     */
    public function check(string $response, bool $requireDisclaimer = true): array
    {
        $violations = [];

        // 1. Check for forbidden diagnosis phrases
        foreach ($this->forbiddenDiagnosisPhrases as $phrase) {
            if ($this->contains($response, $phrase)) {
                $violations[] = "تشخيص محظور: \"{$phrase}\"";
                $response = $this->softenDiagnosis($response, $phrase);
            }
        }

        // 2. Check for frightening phrases
        foreach ($this->frighteningPhrases as $phrase) {
            if ($this->contains($response, $phrase)) {
                $violations[] = "عبارة مخيفة: \"{$phrase}\"";
                $response = $this->softenFrightening($response, $phrase);
            }
        }

        // 3. Check for medication references
        foreach ($this->medicationPhrases as $phrase) {
            if ($this->contains($response, $phrase)) {
                $violations[] = "إشارة إلى أدوية: \"{$phrase}\"";
                $response = $this->removeMedicationReference($response);
            }
        }

        // 4. Ensure disclaimer is present in result-explanation responses
        if ($requireDisclaimer && !$this->contains($response, 'نتيجة مبدئية')) {
            $response .= "\n\n*" . $this->mandatoryDisclaimer . "*";
        }

        return [
            'safe'       => empty($violations),
            'response'   => $response,
            'violations' => $violations,
        ];
    }

    /**
     * Quick boolean check — returns true if the response passes all safety rules.
     */
    public function isSafe(string $response): bool
    {
        return $this->check($response, false)['safe'];
    }

    /**
     * Returns a sanitized version of the response, regardless of violations.
     */
    public function sanitize(string $response, bool $requireDisclaimer = true): string
    {
        return $this->check($response, $requireDisclaimer)['response'];
    }

    // ─────────────────────────────────────────────
    // Fallback response builder
    // ─────────────────────────────────────────────

    /**
     * Build a safe rule-based fallback response when the AI API is unavailable.
     * Uses score data and skill rules to construct an Arabic response without LLM.
     */
    public function buildFallbackResponse(array $childContext, SkillRulesRepository $skillRules): string
    {
        $lines = [];
        $lines[] = 'لا يمكن تشغيل المساعد الذكي حاليًا بسبب مشكلة تقنية مؤقتة، لكن بناءً على نتيجة الطفل في منصة بوصلة:';
        $lines[] = '';

        $skills = $childContext['skills'] ?? [];

        if (empty($skills)) {
            $lines[] = 'لا تتوفر نتائج كافية حاليًا لتقديم توصيات. يرجى إكمال الاختبارات أولاً.';
        } else {
            foreach ($skills as $skill) {
                if ($skill['level'] === 'good_performance') {
                    $lines[] = "✅ **{$skill['arabic_name']}:** أداء جيد — واصل التمارين للحفاظ على المستوى.";
                } else {
                    $arabicLabel = $skill['arabic_level_label'];
                    $trainingLevel = $skill['recommended_training_level'];
                    $lines[] = "📌 **{$skill['arabic_name']}:** {$arabicLabel}";
                    $lines[] = "   المستوى الموصى به: المستوى {$trainingLevel}";

                    $exerciseList = $skillRules->getExerciseListArabic($skill['skill_id'], $trainingLevel);
                    if ($exerciseList) {
                        $lines[] = "   تمارين مقترحة:";
                        $lines[] = $exerciseList;
                    }
                    $lines[] = '';
                }
            }
        }

        $lines[] = '';
        $lines[] = 'ننصح بمتابعة تمارين بوصلة بانتظام ومراقبة الأداء خلال الجلسات القادمة.';
        $lines[] = '';
        $lines[] = '*' . $skillRules->getMandatoryDisclaimer() . '*';

        return \implode("\n", $lines);
    }

    // ─────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────

    private function contains(string $text, string $phrase): bool
    {
        return \stripos($text, $phrase) !== false;
    }

    private function softenDiagnosis(string $response, string $phrase): string
    {
        $replacements = [
            'مصاب بعسر القراءة'          => 'يُظهر بعض مؤشرات مرتبطة بصعوبة القراءة',
            'مصاب بعسر الحساب'           => 'يُظهر بعض مؤشرات مرتبطة بصعوبة الحساب',
            'مصاب بـ'                     => 'يُظهر مؤشرات قد تكون مرتبطة بـ',
            'يعاني من عسر القراءة المؤكد' => 'تظهر لديه بعض مؤشرات صعوبة القراءة',
            'تشخيص مؤكد'                  => 'مؤشر أولي',
            'لديه ديسلكسيا'               => 'تظهر لديه بعض مؤشرات مرتبطة بعسر القراءة',
            'طفلك مريض'                   => 'طفلك قد يحتاج دعمًا إضافيًا في بعض المهارات',
            'هذا مرض'                     => 'هذا مؤشر يستحق المتابعة',
            'اضطراب مؤكد'                 => 'مؤشر صعوبة',
        ];

        $safe = $replacements[$phrase] ?? 'مؤشر يستحق المتابعة';
        return \str_ireplace($phrase, $safe, $response);
    }

    private function softenFrightening(string $response, string $phrase): string
    {
        $replacements = [
            'الحالة خطيرة'      => 'الحالة تستدعي المتابعة',
            'خطر شديد'           => 'يستدعي الانتباه',
            'مقلق جداً'          => 'يستحق المتابعة',
            'يجب التدخل الفوري' => 'يُفضّل التواصل مع متخصص',
            'حالة طارئة'         => 'حالة تحتاج متابعة',
            'لا أمل'             => 'التحسن ممكن مع الممارسة المنتظمة',
            'لن يتحسن'           => 'يمكن أن يتحسن مع التدريب المناسب',
        ];

        $safe = $replacements[$phrase] ?? 'يستحق المتابعة';
        return \str_ireplace($phrase, $safe, $response);
    }

    private function removeMedicationReference(string $response): string
    {
        return $response . "\n\n*ملاحظة: لا يقدم مساعد بوصلة توصيات طبية أو دوائية. للحصول على رأي طبي، يرجى استشارة متخصص.*";
    }
}
