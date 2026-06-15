<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Child;
use App\Services\DiagnosisService;

class QuestionnaireController extends Controller
{
    protected DiagnosisService $diagnosisService;

    // حقن السيرفس في الكنترولر عشان نستخدمها في كل الدوال
    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    // 1. استقبال وحفظ إجابات الاستبيان
    public function store(Request $request)
    {
        // 💡 تم إضافة learning_difficulty_id هنا عشان الـ Validation
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'learning_difficulty_id' => 'required|integer', 
            'q1_reading_aloud' => 'required|integer|in:0,1,2',
            'q2_confusing_letters' => 'required|integer|in:0,1,2',
            'q3_forgetting_instructions' => 'required|integer|in:0,1,2',
            'q4_avoiding_reading' => 'required|integer|in:0,1,2',
        ]);

        // الحماية: التأكد إن الطفل يخص المستخدم الحالي
        $child = Child::where('id', $request->child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        // حساب السكور الإجمالي
        $total_score = $request->q1_reading_aloud + 
                       $request->q2_confusing_letters + 
                       $request->q3_forgetting_instructions + 
                       $request->q4_avoiding_reading;

        // استخدام السيرفس لتحديد مستوى الخطر
        // 💡 تم تصليح السطر ده واستخدام learning_difficulty_id
        $risk_level = $this->diagnosisService->getRiskLevel($total_score, $request->learning_difficulty_id);

        // حفظ الاستبيان
        $questionnaire = Questionnaire::create([
            'child_id' => $child->id,
            'q1_reading_aloud' => $request->q1_reading_aloud,
            'q2_confusing_letters' => $request->q2_confusing_letters,
            'q3_forgetting_instructions' => $request->q3_forgetting_instructions,
            'q4_avoiding_reading' => $request->q4_avoiding_reading,
            'total_risk_score' => $total_score,
            'risk_level' => $risk_level,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ التقييم بنجاح',
            'risk_level' => $risk_level,
            'recommendation' => $this->diagnosisService->getRecommendation($risk_level),
            'data' => $questionnaire
        ], 201);
    }

    // 2. عرض نتائج الاستبيان لطفل معين
    public function showResults(Request $request, int $child_id)
    {
        $child = Child::where('id', $child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $latest = Questionnaire::where('child_id', $child_id)->latest()->first();

        if (!$latest) {
            return response()->json(['status' => 'error', 'message' => 'No assessment found'], 404);
        }

        $risk_level = $latest->risk_level;

        return response()->json([
            'status' => 'success',
            'child_name' => $child->name,
            'result' => [
                'score' => $latest->total_risk_score,
                'risk_level' => $risk_level,
                'recommendation' => $this->diagnosisService->getRecommendation($risk_level),
                'date' => $latest->created_at->format('Y-m-d')
            ]
        ], 200);
    }

    // 3. جلب تاريخ الاستبيانات للطفل
    public function getChildHistory(int $child_id, Request $request)
    {
        // التأكد إن الطفل يخص المستخدم الحالي
        $child = Child::where('id', $child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        // جلب كل التقييمات مرتبة من الأحدث للأقدم
        $history = Questionnaire::where('child_id', $child_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return response()->json([
            'status' => 'success',
            'child_name' => $child->name,
            'history_count' => $history->count(),
            'data' => $history
        ]);
    }
}