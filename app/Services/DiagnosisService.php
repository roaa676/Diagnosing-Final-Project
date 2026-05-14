<?php

namespace App\Services;

use App\Models\AgeNorm;

class DiagnosisService
{
    // ==========================================
    // 1. دوال الاستبيان (عشان الكنترولر القديم ميضربش)
    // ==========================================
    
public function getRiskLevel(int $totalScore, int $difficultyId): string
{
    // حساب عدد الأسئلة الفعلي المسجل لهذه الصعوبة في الداتا بيز
    $questionsCount = \App\Models\Question::where('learning_difficulty_id', $difficultyId)->count();
    
    // تأمين في حالة عدم وجود أسئلة (افتراض 8 أسئلة)
    if ($questionsCount == 0) $questionsCount = 8; 

    $percentage = ($totalScore / $questionsCount) * 100;

    if ($percentage >= 70) {
        return 'High Risk';
    } elseif ($percentage >= 40) {
        return 'Moderate Risk';
    } else {
        return 'Low Risk';
    }
}

    public function getRecommendation(string $level): string
    {
        $recommendations = [
            'High Risk' => 'النتيجة تشير لضرورة عرض الطفل على أخصائي تشخيص صعوبات تعلم لعمل اختبارات إكلينيكية.',
            'Moderate Risk' => 'يُنصح بمتابعة أداء الطفل وتكرار الأنشطة التعليمية المحفزة للمهارات الضعيفة.',
            'Low Risk' => 'أداء الطفل طبيعي، استمر في دعم مهاراته من خلال الألعاب التفاعلية.'
        ];

        return $recommendations[$level] ?? 'لا توجد توصيات متاحة حالياً.';
    }

    // ==========================================
    // 2. دوال الألعاب (معايير Z-Score الطبية)
    // ==========================================

    public function calculateGameZScore(int $childAge, string $gameType, float $childRawScore): array
    {
        $norm = AgeNorm::where('age', $childAge)
                       ->where('test_type', $gameType)
                       ->first();

        if (!$norm) {
            return [
                'z_score' => null,
                'risk_level' => 'No Norm Data'
            ];
        }

        // حساب الـ Z-Score
        $zScore = ($childRawScore - $norm->expected_raw_score) / $norm->standard_deviation;
        $zScore = round($zScore, 2);

        // تقييم خطر اللعبة بناءً على نوع الصعوبة (هنا بنستدعي مسطرة الخطر المخصصة)
        $risk = $this->evaluateRiskByGameType($zScore, $gameType);

        return [
            'z_score' => $zScore,
            'risk_level' => $risk
        ];
    }

    /**
     * المسطرة الطبية: هنا نغير معيار الخطر بناءً على نوع الصعوبة
     */
    private function evaluateRiskByGameType(float $zScore, string $gameType): string
    {
        $riskLevel = 'Normal';

        switch ($gameType) {
            case 'visual_discrimination': 
                // مؤشر عسر القراءة: حساس جداً للانحراف
                // لو نزل عن -1.5 يعتبر خطر عالي
                if ($zScore <= -1.5) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -0.5) {
                    $riskLevel = 'Moderate Risk';
                }
                break;

            case 'magnitude_comparison': 
                // مؤشر عسر الحساب: بنعطي مساحة أكبر شوية
                // لازم ينزل عن -2.0 عشان نعتبره خطر عالي
                if ($zScore <= -2.0) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -1.0) {
                    $riskLevel = 'Moderate Risk';
                }
                break;
            
            default:
                // المسطرة الافتراضية لأي لعبة تانية
                if ($zScore <= -2.0) {
                    $riskLevel = 'High Risk';
                } elseif ($zScore <= -1.0) {
                    $riskLevel = 'Moderate Risk';
                }
                break;
        }

        return $riskLevel;
    }
    // ==========================================
    // 3. التقرير الشامل (الدمج النهائي)
    // ==========================================

    public function getFinalConclusion(?string $questionnaireRisk, ?string $gameRisk): string
    {
        // لو مفيش بيانات كافية
        if (!$questionnaireRisk || !$gameRisk || $gameRisk === 'No Norm Data') {
            return 'نحتاج إلى استكمال كلا التقييمين (الاستبيان والألعاب) لإصدار تقرير شامل ودقيق.';
        }

        // 1. تطابق في الخطر (الأسوأ)
        if ($questionnaireRisk === 'High Risk' && $gameRisk === 'High Risk') {
            return 'تطابق تام: ملاحظات الأهل تتوافق مع أداء الطفل الفعلي في الألعاب. هناك مؤشرات قوية جداً على وجود صعوبة تعلم. يُنصح بشدة بالتدخل المتخصص الفوري.';
        }
        
        // 2. الأهل قلقين بس اللعبة بتقول طبيعي
        if ($questionnaireRisk === 'High Risk' && $gameRisk === 'Normal') {
            return 'تباين: ملاحظات الأهل تشير لمشكلة، لكن أداء الطفل في الألعاب طبيعي. قد يكون تراجع أداء الطفل بسبب التشتت أو البيئة المدرسية وليس صعوبة تعلم عضوية. يُنصح بالمتابعة.';
        }
        
        // 3. الأهل مش واخدين بالهم بس اللعبة كشفت مشكلة
        if ($questionnaireRisk === 'Low Risk' && $gameRisk === 'High Risk') {
            return 'انتباه: الأهل لا يلاحظون مشكلة، لكن أداء الطفل القياسي في الألعاب يشير لصعوبة محتملة في المعالجة (البصرية/الرقمية). يُنصح بإجراء اختبارات مدرسية إضافية.';
        }

        // 4. تطابق في الأداء الطبيعي
        if (in_array($questionnaireRisk, ['Low Risk', 'Normal']) && in_array($gameRisk, ['Low Risk', 'Normal'])) {
            return 'مطمئن: لا توجد أي مؤشرات واضحة لصعوبات تعلم حالياً. أداء الطفل يتماشى مع فئته العمرية.';
        }

        return 'حالة متوسطة: يُنصح بزيادة الأنشطة التفاعلية ومراقبة تطور الطفل خلال 3 أشهر.';
    }
}