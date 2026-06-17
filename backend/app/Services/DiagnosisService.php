<?php

namespace App\Services;

use App\Models\AgeNorm;

class DiagnosisService
{
    public function getRiskLevel(int $totalScore, int $difficultyId): string
    {
        $questionsCount = \App\Models\Question::where('learning_difficulty_id', $difficultyId)->count();

        if ($questionsCount == 0) {
            $questionsCount = 8;
        }

        $percentage = ($totalScore / $questionsCount) * 100;

        if ($percentage >= 70) {
            return 'High Risk';
        } elseif ($percentage >= 40) {
            return 'Moderate Risk';
        }

        return 'Low Risk';
    }

    public function getRecommendation(string $level): string
    {
        $recommendations = [
            'High Risk' => 'الناتجة تشير لضرورة عرض الطفل على أخصائي تشخيص صعوبات تعلم لعمل اختبارات إكلينيكية.',
            'Moderate Risk' => 'يُنصح بمتابعة أداء الطفل وتكرار الأنشطة التعليمية المحفزة للمهارات الضعيفة.',
            'Low Risk' => 'أداء الطفل طبيعي، استمر في دعم مهاراته من خلال الألعاب التفاعلية.',
        ];

        return $recommendations[$level] ?? 'لا توجد توصيات متاحة حالياً.';
    }

    public function calculateGameZScore(int $childAge, string $gameType, float $childRawScore): array
    {
        $normalizedGameType = $this->normalizeGameType($gameType);

        $norm = AgeNorm::where('age', $childAge)
            ->where('test_type', $normalizedGameType)
            ->first();

        if (!$norm) {
            return [
                'z_score' => null,
                'risk_level' => 'No Norm Data',
            ];
        }

        $zScore = ($childRawScore - $norm->expected_raw_score) / $norm->standard_deviation;
        $zScore = round($zScore, 2);

        $risk = $this->evaluateRiskByGameType($zScore, $normalizedGameType);

        return [
            'z_score' => $zScore,
            'risk_level' => $risk,
        ];
    }

    private function normalizeGameType(string $gameType): string
    {
        // Keep legacy aliases in one place only.
        return match ($gameType) {
            'number_direction' => 'magnitude_comparison',
            default => $gameType,
        };
    }

    private function evaluateRiskByGameType(float $zScore, string $gameType): string
    {
        $riskLevel = 'Normal';

        switch ($gameType) {
            case 'visual_discrimination':
                if ($zScore <= -1.5) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -0.5) {
                    $riskLevel = 'Moderate Risk';
                }
                break;

            case 'magnitude_comparison':
                if ($zScore <= -2.0) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -1.0) {
                    $riskLevel = 'Moderate Risk';
                }
                break;

            default:
                if ($zScore <= -2.0) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -1.0) {
                    $riskLevel = 'Moderate Risk';
                }
                break;
        }

        return $riskLevel;
    }

    public function getFinalConclusion(?string $questionnaireRisk, ?string $gameRisk): string
    {
        if (!$questionnaireRisk || !$gameRisk || $gameRisk === 'No Norm Data') {
            return 'نحتاج إلى استكمال كل التقييمين (الاستبيان والألعاب) لإصدار تقرير شامل ودقيق.';
        }

        if ($questionnaireRisk === 'High Risk' && $gameRisk === 'High Risk') {
            return 'تطابق تام: ملاحظات الأهل تتوافق مع أداء الطفل الفعلي في الألعاب. هناك مؤشرات قوية جداً على وجود صعوبة تعلم. يُنصح بشدة بالتدخل المتخصص الفوري.';
        }

        if ($questionnaireRisk === 'High Risk' && $gameRisk === 'Normal') {
            return 'تباين: ملاحظات الأهل تشير لمشكلة، لكن أداء الطفل في الألعاب طبيعي. قد يكون تراجع الأداء بسبب التشتت أو البيئة المدرسية وليس صعوبة تعلم عضوية. يُنصح بالمتابعة.';
        }

        if ($questionnaireRisk === 'Low Risk' && $gameRisk === 'High Risk') {
            return 'انتباه: الأهل لا يلاحظون مشكلة، لكن أداء الطفل القياسي في الألعاب يشير لصعوبة محتملة في المعالجة (البصرية/الرقمية). يُنصح بإجراء اختبارات مدرسية إضافية.';
        }

        if (in_array($questionnaireRisk, ['Low Risk', 'Normal']) && in_array($gameRisk, ['Low Risk', 'Normal'])) {
            return 'مطمئن: لا توجد أي مؤشرات واضحة لصعوبات تعلم حالياً. أداء الطفل يتماشى مع فئته العمرية.';
        }

        return 'حالة متوسطة: يُنصح بزيادة الأنشطة التفاعلية ومراقبة تطور الطفل خلال 3 أشهر.';
    }
}
