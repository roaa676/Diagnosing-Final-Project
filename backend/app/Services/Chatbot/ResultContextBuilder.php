<?php

namespace App\Services\Chatbot;

use App\Models\Child;
use App\Models\GameResult;

/**
 * ResultContextBuilder
 *
 * Converts a child's database records (GameResult, Questionnaire) into a clean,
 * structured JSON context that can be injected into the chatbot prompt.
 *
 * Integration path: copy to backend/app/Services/Chatbot/ResultContextBuilder.php
 */
class ResultContextBuilder
{
    private SkillRulesRepository $skillRules;

    /**
     * Maps game_type strings (from GameResult) to skill_ids (from skill_rules.json).
     * Extend this map as new game types are added to the system.
     */
    private array $gameTypeToSkillId = [
        'visual_discrimination'   => 'dyslexia_visual_discrimination',
        'structural_similarity'   => 'dyslexia_structural_similarity',
        'mirroring'               => 'dyslexia_mirroring',
        'magnitude_comparison'    => 'dyscalculia_quantity_comparison',
        'number_sequence'         => 'dyscalculia_number_sequence',
        'number_reversal'         => 'dyscalculia_number_reversal',
    ];

    public function __construct(SkillRulesRepository $skillRules)
    {
        $this->skillRules = $skillRules;
    }

    /**
     * Build the full context array for a child, ready to be JSON-encoded
     * and injected into the chatbot prompt.
     *
     * @param int $childId
     * @param int $authenticatedUserId  Used to verify ownership
     * @return array|null  Returns null if child not found or unauthorized
     */
    public function buildContext(int $childId, int $authenticatedUserId): ?array
    {
        $child = Child::where('id', $childId)
                      ->where('user_id', $authenticatedUserId)
                      ->first();

        if (!$child) {
            return null;
        }

        // Fetch all game results for this child, ordered by newest first
        $gameResults = GameResult::where('child_id', $childId)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        // Latest questionnaire result
        $latestQuestionnaire = $child->questionnaires()->latest()->first();

        // Build the skills array from game results
        $skills = $this->buildSkillsArray($gameResults);

        // Determine overall summary
        $overallSummary = $this->buildOverallSummary(
            $gameResults,
            $latestQuestionnaire,
            $skills
        );

        return [
            'child' => [
                'age'          => $child->age,
                'name_alias'   => 'الطفل',
                'session_date' => now()->format('Y-m-d'),
            ],
            'overall_summary'         => $overallSummary,
            'skills'                  => $skills,
            'chatbot_context_notes'   => $this->buildContextNotes($skills),
        ];
    }

    /**
     * Build a context for a specific single game result (result_id).
     */
    public function buildContextForResult(int $childId, int $resultId, int $authenticatedUserId): ?array
    {
        $child = Child::where('id', $childId)
                      ->where('user_id', $authenticatedUserId)
                      ->first();

        if (!$child) {
            return null;
        }

        $gameResult = GameResult::where('id', $resultId)
                                ->where('child_id', $childId)
                                ->first();

        if (!$gameResult) {
            return null;
        }

        $skills = $this->buildSkillsArray(collect([$gameResult]));

        return [
            'child' => [
                'age'          => $child->age,
                'name_alias'   => 'الطفل',
                'session_date' => $gameResult->created_at->format('Y-m-d'),
            ],
            'overall_summary' => $this->buildSingleResultSummary($gameResult),
            'skills'          => $skills,
            'chatbot_context_notes' => $this->buildContextNotes($skills),
        ];
    }

    // ─────────────────────────────────────────────
    // Private builders
    // ─────────────────────────────────────────────

    private function buildSkillsArray($gameResults): array
    {
        $skills = [];

        foreach ($gameResults as $result) {
            $skillId = $this->gameTypeToSkillId[$result->game_type] ?? null;
            if (!$skillId) {
                continue;
            }

            // Convert z_score or raw_score to a 0-100 score for the chatbot
            $score = $this->normalizeScore($result);

            $band           = $this->skillRules->getScoreBand($score);
            $trainingLevel  = $this->skillRules->getRecommendedTrainingLevel($score);
            $exercises      = $this->skillRules->getExercisesForSkill($skillId, $trainingLevel);

            $skills[] = [
                'skill_id'                  => $skillId,
                'arabic_name'               => $this->skillRules->getSkillArabicName($skillId),
                'score'                     => $score,
                'level'                     => $band['id'],
                'arabic_level_label'        => $band['arabic_label'],
                'risk_level_raw'            => $result->risk_level,
                'z_score'                   => $result->z_score,
                'mistake_examples'          => $this->getMistakeExamples($skillId, $band['id']),
                'recommended_training_level' => $trainingLevel,
                'training_level_label'      => $band['id'] !== 'good_performance'
                    ? "المستوى {$trainingLevel} — " . ($band['arabic_label'] ?? '')
                    : 'صيانة وتطوير',
                'exercises_preview'         => array_slice(
                    array_column($exercises, 'name'),
                    0,
                    4
                ),
            ];
        }

        return $skills;
    }

    private function buildOverallSummary($gameResults, $questionnaire, array $skills): array
    {
        $latestGame = $gameResults->first();

        // Determine main difficulty indicator from the worst performing skill
        $mainIndicator = $this->determineMainIndicator($skills);

        // Determine overall severity
        $highCount     = count(array_filter($skills, fn($s) => $s['level'] === 'high_difficulty_indicator'));
        $moderateCount = count(array_filter($skills, fn($s) => $s['level'] === 'moderate_difficulty_indicator'));

        $severity = 'low';
        if ($highCount > 0) {
            $severity = 'high';
        } elseif ($moderateCount > 0) {
            $severity = 'moderate';
        }

        return [
            'main_indicator'           => $mainIndicator,
            'severity'                 => $severity,
            'z_score_game'             => $latestGame?->z_score,
            'risk_level_game'          => $latestGame?->risk_level ?? 'لم يتم التقييم',
            'risk_level_questionnaire' => $questionnaire?->risk_level ?? 'لم يتم التقييم',
            'note'                     => $this->skillRules->getMandatoryDisclaimer(),
        ];
    }

    private function buildSingleResultSummary(GameResult $result): array
    {
        $severity = $this->skillRules->mapRiskLevelToSeverity($result->risk_level);

        return [
            'main_indicator'  => $this->gameTypeToSkillId[$result->game_type] ?? $result->game_type,
            'severity'        => $severity,
            'z_score_game'    => $result->z_score,
            'risk_level_game' => $result->risk_level,
            'note'            => $this->skillRules->getMandatoryDisclaimer(),
        ];
    }

    private function buildContextNotes(array $skills): array
    {
        $highSkills     = array_filter($skills, fn($s) => $s['level'] === 'high_difficulty_indicator');
        $moderateSkills = array_filter($skills, fn($s) => $s['level'] === 'moderate_difficulty_indicator');
        $goodSkills     = array_filter($skills, fn($s) => $s['level'] === 'good_performance');

        $primaryConcern   = !empty($highSkills) ? array_values($highSkills)[0]['skill_id'] : null;
        $secondaryConcern = !empty($moderateSkills) ? array_values($moderateSkills)[0]['skill_id'] : null;
        $strongSkill      = !empty($goodSkills) ? array_values($goodSkills)[0]['skill_id'] : null;

        return [
            'primary_concern'   => $primaryConcern,
            'secondary_concern' => $secondaryConcern,
            'strong_skill'      => $strongSkill,
            'recommended_focus' => $primaryConcern
                ? 'التركيز على تمارين ' . $this->skillRules->getSkillArabicName($primaryConcern) . ' كأولوية قصوى'
                : 'مواصلة التمارين الحالية للحفاظ على الأداء الجيد',
        ];
    }

    /**
     * Convert z_score / raw_score from GameResult into a 0-100 percentage score
     * that the chatbot's score bands can interpret.
     */
    private function normalizeScore(GameResult $result): int
    {
        // If z_score is available, convert it to a rough percentage
        if ($result->z_score !== null) {
            // z=-2 → ~10%, z=-1 → ~30%, z=0 → 50%, z=+1 → 70%, z=+2 → 90%
            $pct = 50 + ($result->z_score * 20);
            return max(0, min(100, (int) round($pct)));
        }

        // Fallback: map risk_level strings to representative scores
        return match ($result->risk_level) {
            'High Risk'     => 35,
            'Moderate Risk' => 60,
            'Normal'        => 80,
            'No Risk'       => 85,
            default         => 50,
        };
    }

    private function determineMainIndicator(array $skills): string
    {
        // First high-difficulty skill is the main indicator
        foreach ($skills as $skill) {
            if ($skill['level'] === 'high_difficulty_indicator') {
                return $skill['skill_id'];
            }
        }
        // Then moderate
        foreach ($skills as $skill) {
            if ($skill['level'] === 'moderate_difficulty_indicator') {
                return $skill['skill_id'];
            }
        }
        return 'no_significant_indicator';
    }

    /**
     * Return representative mistake examples based on skill and severity.
     * In a real scenario these would come from the child's actual answer log.
     */
    private function getMistakeExamples(string $skillId, string $level): array
    {
        if ($level === 'good_performance') {
            return [];
        }

        $exampleMap = [
            'dyslexia_mirroring'            => ['b/d', 'p/q', 'بطة/طبة', '٢/٦'],
            'dyslexia_visual_discrimination' => ['الخلط بين حروف متشابهة الشكل'],
            'dyslexia_structural_similarity' => ['ح/خ/ج', 'س/ش', 'ص/ض'],
            'dyscalculia_quantity_comparison' => ['صعوبة تحديد المجموعة الأكبر عند الفروق الصغيرة'],
            'dyscalculia_number_sequence'    => ['صعوبة إيجاد الرقم الناقص'],
            'dyscalculia_number_reversal'    => ['قراءة 12 كـ 21', 'قراءة 45 كـ 54'],
        ];

        return $exampleMap[$skillId] ?? [];
    }
}
