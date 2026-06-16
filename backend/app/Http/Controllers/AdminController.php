<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Child;
use App\Models\Questionnaire;
use App\Models\GameResult; // ضفنا ده عشان نجيب نتيجة اللعبة
use App\Models\LearningDifficulty; // لو حبيت تعرض إحصائيات الصعوبات

class AdminController extends Controller
{
    /**
     * جلب إحصائيات لوحة التحكم (مدمجة مع مصفوفة القرار الذكية)
     */
    public function getStats()
    {
        // 1. إحصائيات عامة
        $totalParents = User::count();
        $totalChildren = Child::count();
        $highRiskCount = Questionnaire::where('risk_level', 'High Risk')->count();
        $riskRate = $totalChildren > 0 ? round(($highRiskCount / $totalChildren) * 100, 2) : 0;

        // 2. تحليل مصفوفة القرار (Decision Matrix Analytics)
        $confirmedCases = 0;
        $unnoticedCases = 0; 
        $attentionNeededCases = 0;

        $childrenIds = Child::pluck('id');
        foreach ($childrenIds as $childId) {
            $latestQ = Questionnaire::where('child_id', $childId)->latest()->first();
            $latestG = GameResult::where('child_id', $childId)->latest()->first();

            if ($latestQ && $latestG) {
                $q_is_high = in_array($latestQ->risk_level, ['High Risk', 'Moderate Risk']);
                $g_is_high = in_array($latestG->risk_level, ['High Risk', 'Moderate Risk']);

                if ($q_is_high && $g_is_high) $confirmedCases++;
                elseif (!$q_is_high && $g_is_high) $unnoticedCases++;
                elseif ($q_is_high && !$g_is_high) $attentionNeededCases++;
            }
        }

        // 3. آخر 5 عمليات تقييم (استبيان)
        $latestAssessments = Questionnaire::with('child')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'child_name' => $item->child->name ?? 'غير معروف',
                    'risk_level' => $item->risk_level,
                    'score'      => $item->total_risk_score,
                    'date'       => $item->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_parents'        => $totalParents,
                    'total_children'       => $totalChildren,
                    'high_risk_cases'      => $highRiskCount,
                    'risk_rate_percentage' => $riskRate . '%',
                ],
                'decision_matrix' => [
                    'confirmed_cases'  => $confirmedCases, // صعوبة مؤكدة (لعبة واستبيان)
                    'unnoticed_cases'  => $unnoticedCases, // مشكلة غير ملحوظة (لعبة فقط)
                    'attention_needed' => $attentionNeededCases, // تشتت انتباه (استبيان فقط)
                ],
                'latest_assessments' => $latestAssessments
            ]
        ]);
    }

    /**
     * جلب قائمة بكل الأطفال مع حالة آخر تقييم (لجدول الإدارة)
     */
    public function getAllChildrenWithStatus()
    {   
        // جلب كل الأطفال مع بيانات الأب
        $children = Child::with(['user', 'latestQuestionnaire'])->get();

        $data = $children->map(function($child) {
            // هنجيب نتيجة اللعبة كمان عشان الجدول يكون كامل
            $latestGame = GameResult::where('child_id', $child->id)->latest()->first();

            return [
                'child_id'             => $child->id,
                'child_name'           => $child->name,
                'parent_name'          => $child->user->name ?? 'غير معروف',
                'age'                  => $child->age,
                'questionnaire_risk'   => $child->latestQuestionnaire->risk_level ?? 'لم يتم التقييم',
                'game_risk'            => $latestGame->risk_level ?? 'لم يلعب بعد',
                'last_score'           => $child->latestQuestionnaire->total_risk_score ?? 0,
                'last_assessment_date' => $child->latestQuestionnaire ? $child->latestQuestionnaire->created_at->format('Y-m-d') : '---',
            ];
        });

        return response()->json([
            'status'         => 'success',
            'total_children' => $children->count(),
            'data'           => $data
        ]);
    }
}