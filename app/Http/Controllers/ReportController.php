<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Services\DiagnosisService;

class ReportController extends Controller
{
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    public function getComprehensiveReport(int $child_id, Request $request)
    {
        // 1. جلب بيانات الطفل (مع التأكد من الأمان)
        $child = Child::where('id', $child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // 2. جلب آخر استبيان وآخر لعبة (Eager Loading)
        $latestQuestionnaire = $child->questionnaires()->latest()->first();
        $latestGame = $child->gameResults()->latest()->first();

        // 3. تحليل النتائج
        $qRisk = $latestQuestionnaire ? $latestQuestionnaire->risk_level : null;
        $gRisk = $latestGame ? $latestGame->risk_level : null;

        $finalConclusion = $this->diagnosisService->getFinalConclusion($qRisk, $gRisk);

        // 4. إرسال التقرير النهائي
        return response()->json([
            'status' => 'success',
            'child_info' => [
                'name' => $child->name,
                'age' => $child->age . ' سنوات',
            ],
            'assessments' => [
                'parent_questionnaire' => [
                    'risk_level' => $qRisk ?? 'لم يتم التقييم',
                    'date' => $latestQuestionnaire ? $latestQuestionnaire->created_at->format('Y-m-d') : null
                ],
                'game_performance' => [
                    'game_type' => $latestGame ? $latestGame->game_type : 'لم يلعب بعد',
                    'risk_level' => $gRisk ?? 'لم يتم التقييم',
                    'z_score' => $latestGame ? $latestGame->z_score : null,
                    'date' => $latestGame ? $latestGame->created_at->format('Y-m-d') : null
                ]
            ],
            'final_conclusion' => $finalConclusion
        ], 200);
    }
}
