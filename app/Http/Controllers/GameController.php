<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\GameResult;
use App\Models\GameContent;
use App\Models\TrainingProgress; 
use App\Services\DiagnosisService;

class GameController extends Controller
{
    // 1. تعريف المتغيرات ودالة البناء
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    // 2. دالة جلب الاختبار الشامل للتشخيص (تسحب أسئلة الـ assessment فقط)
    public function getAssessmentContent(int $difficulty_id)
    {
        // هنجيب مستويات التقييم بس للصعوبة دي مرتبة تصاعدياً
        $levels = GameContent::where('learning_difficulty_id', $difficulty_id)
                    ->where('content_type', 'assessment') // 💡 التعديل هنا: أسئلة تقييم فقط
                    ->orderBy('difficulty_level', 'asc')
                    ->get();

        if ($levels->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'لا يوجد محتوى تقييم لهذا الاختبار'], 404);
        }

        $assessmentLevels = [];

        // بنمشي على كل مستوى ونجهز أسئلته لوحدها
        foreach ($levels as $level) {
            $data = json_decode($level->content_data, true);
            
            if (isset($data['questions'])) {
                // سحب 8 أسئلة عشوائية من البنك الخاص بهذا المستوى
                $shuffledQuestions = collect($data['questions'])->shuffle()->take(8)->map(function ($question) use ($difficulty_id, $level) {
                    
                    // لخبطة الاختيارات
                    if ($difficulty_id == 1 && isset($question['options'])) {
                        $question['options'] = collect($question['options'])->shuffle()->toArray();
                    }
                    
                    $question['difficulty_level'] = $level->difficulty_level; 
                    return $question;
                });

                // تجميع الداتا كـ "بلوك" كامل لكل مستوى
                $assessmentLevels[] = [
                    'difficulty_level' => $level->difficulty_level,
                    'level_name' => $level->level_name,
                    'questions' => $shuffledQuestions->values()->all() // الـ 8 أسئلة
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'assessment_data' => $assessmentLevels // مصفوفة متدرجة من المستويات
        ]);
    }

    // 3. دالة جلب مستوى معين للتدريب اليومي (تسحب أسئلة الـ training فقط)
    public function getGameContent(int $difficulty_id, int $level)
    {
        // 1. جلب بنك أسئلة التدريب للمستوى المطلوب
        $gameContent = GameContent::where('learning_difficulty_id', $difficulty_id)
                    ->where('difficulty_level', $level)
                    ->where('content_type', 'training') // 💡 التعديل هنا: أسئلة تدريب فقط
                    ->first();

        if (!$gameContent) {
            return response()->json(['status' => 'error', 'message' => 'محتوى التدريب غير موجود لهذا المستوى'], 404);
        }

        $data = json_decode($gameContent->content_data, true);
        $questions = collect($data['questions']);

        // 2. سحب 8 أسئلة عشوائية للتدريب في كل جلسة
        $randomizedQuestions = $questions->shuffle()->take(8)->map(function ($question) use ($difficulty_id) {
            
            // لخبطة أماكن الاختيارات
            if ($difficulty_id == 1 && isset($question['options'])) {
                $question['options'] = collect($question['options'])->shuffle()->toArray();
            }
            
            return $question;
        });

        return response()->json([
            'status' => 'success',
            'level_name' => $gameContent->level_name,
            'difficulty_level' => $gameContent->difficulty_level,
            'questions' => $randomizedQuestions->values()->all() // إرجاع أسئلة التدريب (8 عشوائيين)
        ]);
    }

    // 4. دالة حفظ نتيجة التقييم (وحساب الـ Z-Score وتحديد مسار التدريب)
    public function submitGameResult(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'game_type' => 'required|string',
            'raw_score' => 'required|numeric'
        ]);

        $child = Child::where('id', $request->child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        // 1. حساب الـ Z-Score والنتيجة
        $analysis = $this->diagnosisService->calculateGameZScore(
            $child->age, 
            $request->game_type, 
            (float) $request->raw_score
        );

        // 2. حفظ النتيجة في الداتا بيز
        $result = GameResult::create([
            'child_id' => $child->id,
            'game_type' => $request->game_type,
            'raw_score' => $request->raw_score,
            'z_score' => $analysis['z_score'],
            'risk_level' => $analysis['risk_level'],
        ]);

        // 3. ترشيح مسار التدريب بناءً على النتيجة
        $startingLevel = 1;
        $startingPercentage = 0;

        if ($analysis['risk_level'] === 'Moderate Risk') {
            $startingLevel = 2;
            $startingPercentage = 30;
        } elseif ($analysis['risk_level'] === 'No Risk') {
            $startingLevel = 3;
            $startingPercentage = 60;
        }

        // 4. حفظ أو تحديث مسار التدريب للطفل
        $trainingProgress = TrainingProgress::updateOrCreate(
            [
                'child_id' => $child->id, 
                'training_type' => $request->game_type
            ],
            [
                'current_level' => $startingLevel,
                'progress_percentage' => $startingPercentage,
                'next_level_unlocks_at' => now(), // يقدر يبدأ التدريب فوراً
            ]
        );

        // 5. إرجاع الرد للفرونت إند
        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ وتحليل النتيجة وتحديد مسار التدريب بنجاح',
            'analysis' => $analysis,
            'data' => $result,
            'training_roadmap' => $trainingProgress // تفاصيل التدريب
        ], 201);
    }
}