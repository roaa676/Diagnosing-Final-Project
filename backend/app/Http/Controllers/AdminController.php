<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Child;
use App\Models\Questionnaire;

class AdminController extends Controller
{
    /**
     * جلب إحصائيات لوحة التحكم (تم تغيير الاسم ليتوافق مع الرابط في Postman)
     */
    public function getStats()
    {
        // 1. إجمالي عدد أولياء الأمور (المستخدمين)
        $totalParents = User::count();

        // 2. إجمالي عدد الأطفال المسجلين
        $totalChildren = Child::count();

        // 3. عدد حالات الخطر المرتفع (High Risk)
        $highRiskCount = Questionnaire::where('risk_level', 'High Risk')->count();

        // 4. نسبة الحالات المصابة (تقريبية)
        $riskRate = $totalChildren > 0 ? round(($highRiskCount / $totalChildren) * 100, 2) : 0;

        // 5. آخر 5 عمليات تقييم حصلت (مع اسم الطفل)
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
                'total_parents'        => $totalParents,
                'total_children'       => $totalChildren,
                'high_risk_cases'      => $highRiskCount,
                'risk_rate_percentage' => $riskRate . '%',
                'latest_assessments'   => $latestAssessments
            ]
        ]);
    }

    /**
     * جلب قائمة بكل الأطفال مع حالة آخر تقييم (لجدول الإدارة)
     */
    public function getAllChildrenWithStatus()
    {   
        // جلب كل الأطفال مع بيانات الأب وآخر تقييم
        $children = Child::with(['user', 'latestQuestionnaire'])->get();

        $data = $children->map(function($child) {
            return [
                'child_id'             => $child->id,
                'child_name'           => $child->name,
                'parent_name'          => $child->user->name ?? 'غير معروف',
                'age'                  => $child->age,
                'last_risk_level'      => $child->latestQuestionnaire->risk_level ?? 'لم يتم التقييم',
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