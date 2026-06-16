<?php

namespace App\Http\Controllers\Api; // لاحظ مسار الـ Api

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Child;
use App\Models\GameResult; // ضفنا ده عشان مصفوفة القرار
use App\Services\DiagnosisService;

class QuestionnaireController extends Controller
{
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    // 1. استقبال وحفظ إجابات الاستبيان بشكل ديناميكي
    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'learning_difficulty_id' => 'required|integer', 
            'answers' => 'required|array', // بنستقبل الإجابات كمصفوفة عشان تقبل الـ 8 أسئلة
            'answers.*' => 'required|integer' // قيم الإجابات
        ]);

        $child = Child::where('id', $request->child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        // حساب السكور الإجمالي بجمع كل الإجابات أياً كان عددها
        $total_score = array_sum($request->answers);

        // تحديد مستوى الخطر للاستبيان
        $risk_level = $this->diagnosisService->getRiskLevel($total_score, $request->learning_difficulty_id);

        // حفظ الاستبيان (لاحظ إننا بنحفظ الـ total والـ level بس أو ممكن تحفظ الـ answers كـ JSON لو ضفت الحقل ده في الداتا بيز)
        $questionnaire = Questionnaire::create([
            'child_id' => $child->id,
            'learning_difficulty_id' => $request->learning_difficulty_id,
            'total_risk_score' => $total_score,
            'risk_level' => $risk_level,
            // 'answers_json' => json_encode($request->answers) // لو حابب تحفظ تفاصيل إجابات ولي الأمر
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ التقييم المبدئي بنجاح',
            'risk_level' => $risk_level,
            'data' => $questionnaire
        ], 201);
    }

    // 2. مصفوفة القرار (التقرير الذكي اللي بيقارن الاستبيان باللعبة)
    public function generateSmartReport(Request $request, int $child_id, int $difficulty_id)
    {
        // حماية
        $child = Child::where('id', $child_id)->where('user_id', $request->user()->id)->first();
        if (!$child) return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);

        // جلب أحدث استبيان لولي الأمر
        $questionnaire = Questionnaire::where('child_id', $child_id)
                            ->where('learning_difficulty_id', $difficulty_id)
                            ->latest()->first();

        // جلب أحدث نتيجة تقييم عملي (لعبة) للطفل
        $gameAssessment = GameResult::where('child_id', $child_id)
                            ->where('learning_difficulty_id', $difficulty_id)
                            // ->where('game_type', 'assessment') // فك الكومنت لو عندك حقل بيميز اللعبة التشخيصية
                            ->latest()->first();

        if (!$questionnaire || !$gameAssessment) {
            return response()->json([
                'status' => 'error', 
                'message' => 'يجب إتمام استبيان ولي الأمر والتقييم العملي للطفل لإصدار التقرير.'
            ], 400);
        }

        $q_risk = $questionnaire->risk_level; 
        $g_risk = $gameAssessment->risk_level;

        // منطق مصفوفة القرار
        if (in_array($q_risk, ['High Risk', 'Moderate Risk']) && in_array($g_risk, ['High Risk', 'Moderate Risk'])) {
            $reportTitle = "تأكيد وجود تحديات تعلم";
            $parentMessage = "ملاحظاتك كانت دقيقة. التقييم العملي أكد وجود تحديات، وتم توجيه الطفل لمسار التدريب.";
        } elseif ($q_risk == 'No Risk' && in_array($g_risk, ['High Risk', 'Moderate Risk'])) {
            $reportTitle = "صعوبات تعلم غير ملحوظة";
            $parentMessage = "التقييم العملي أظهر احتياج الطفل لدعم إضافي في بعض المهارات رغم عدم ظهورها بوضوح في المنزل.";
        } elseif (in_array($q_risk, ['High Risk', 'Moderate Risk']) && $g_risk == 'No Risk') {
            $reportTitle = "مستوى ممتاز - يحتاج لتوجيه التركيز";
            $parentMessage = "الأداء العملي لطفلك ممتاز! التحديات التي تلاحظها قد تكون بسبب تشتت الانتباه وليس صعوبة تعلم عضوية.";
        } else {
            $reportTitle = "مسار تطور طبيعي";
            $parentMessage = "أداء طفلك وملاحظاتك تتوافق تماماً مع معدلات النمو الطبيعية.";
        }

        return response()->json([
            'status' => 'success',
            'report' => [
                'title' => $reportTitle,
                'message' => $parentMessage,
                'questionnaire_risk' => $q_risk,
                'game_risk' => $g_risk,
            ]
        ], 200);
    }
    
    // (باقي دوال العرض زي ما هي عندك)
}