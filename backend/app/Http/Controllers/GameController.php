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
    // 2. دالة جلب الاختبار الشامل للتشخيص (مع التحقق من فترة الـ 90 يوم)
    public function getAssessmentContent(int $child_id, int $difficulty_id)
    {
<<<<<<< HEAD:app/Http/Controllers/GameController.php
        // 💡 القاعدة: التحقق من تاريخ آخر تقييم (Assessment Interval)
        $lastAssessment = GameResult::where('child_id', $child_id)
            ->where('game_type', 'assessment')
            ->where('learning_difficulty_id', $difficulty_id)
            ->latest()
            ->first();

        if ($lastAssessment && $lastAssessment->created_at->diffInDays(now()) < 90) {
            $daysPassed = $lastAssessment->created_at->diffInDays(now());
            $remainingDays = 90 - $daysPassed;
            return response()->json([
                'status' => 'blocked', 
                'message' => 'عفواً، لا يمكنك إجراء التقييم حالياً. التقييم متاح مرة أخرى بعد ' . $remainingDays . ' يوم.'
            ], 403);
        }

        // ... استكمال منطق جلب الأسئلة كما هو ...
        $levels = GameContent::where('learning_difficulty_id', $difficulty_id)
                    ->where('content_type', 'assessment')
                    ->orderBy('difficulty_level', 'asc')
                    ->get();
=======
        $levels = GameContent::where('learning_difficulty_id', $difficulty_id)
                            ->where('content_type', 'assessment')
                            ->orderBy('difficulty_level', 'asc')
                            ->get();
>>>>>>> 1ae74b281c6ff2aa89740f98bb404ecec0ea8b57:backend/app/Http/Controllers/GameController.php

        if ($levels->isEmpty()) {
            return response()->json([
                'status' => 'error', 
                'message' => 'لا يوجد محتوى تقييم لهذا الاختبار'
            ], 404);
        }

        $assessmentLevels = [];

        foreach ($levels as $level) {
            $data = json_decode($level->content_data, true);
            
            if (isset($data['questions'])) {
                $shuffledQuestions = collect($data['questions'])->shuffle()->take(8)->map(function ($question) use ($difficulty_id, $level) {
<<<<<<< HEAD:app/Http/Controllers/GameController.php
                    if ($difficulty_id == 1 && isset($question['options'])) {
                        $question['options'] = collect($question['options'])->shuffle()->toArray();
=======
                    
                    // لخبطة الاختيارات
                    if (isset($question['options']) && is_array($question['options'])) {
                        $question['options'] = collect($question['options'])->shuffle()->values()->all();
>>>>>>> 1ae74b281c6ff2aa89740f98bb404ecec0ea8b57:backend/app/Http/Controllers/GameController.php
                    }
                    $question['difficulty_level'] = $level->difficulty_level; 
                    return $question;
                });

                $assessmentLevels[] = [
                    'difficulty_level' => $level->difficulty_level,
                    'level_name' => $level->level_name,
                    'questions' => $shuffledQuestions->values()->all()
                ];
            }
        }

        return response()->json([
            'status' => 'success',
<<<<<<< HEAD:app/Http/Controllers/GameController.php
            'assessment_data' => $assessmentLevels
=======
            'data' => $assessmentLevels
>>>>>>> 1ae74b281c6ff2aa89740f98bb404ecec0ea8b57:backend/app/Http/Controllers/GameController.php
        ]);
    }

    // 3. دالة جلب مستوى معين للتدريب اليومي (تسحب أسئلة الـ training فقط)
    // 3. دالة جلب مستوى معين للتدريب اليومي (مع قاعدة الـ 20 سؤال)
    public function getGameContent(Request $request, int $difficulty_id, int $level)
    {
        $child_id = $request->child_id; // لازم الفرونت إند يبعت الـ child_id في الريكويست

        // 💡 القاعدة 2: التحقق من الحد اليومي (الطفل ميتعداش 20 سؤال)
        // بنحسب هو حل كام سؤال النهاردة من جدول الـ TrainingLogs
        $solvedToday = \App\Models\TrainingLog::where('child_id', $child_id)
            ->whereDate('created_at', today())
            ->count();

        if ($solvedToday >= 20) {
            return response()->json([
                'status' => 'limit_reached', 
                'message' => 'أنت بطل! لقد أنهيت تدريب اليوم، نراك غداً لمواصلة التقدم.'
            ], 429); // 429 Too Many Requests
        }

        // 1. جلب بنك أسئلة التدريب للمستوى المطلوب
        $gameContent = GameContent::where('learning_difficulty_id', $difficulty_id)
                    ->where('difficulty_level', $level)
                    ->where('content_type', 'training')
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
<<<<<<< HEAD:app/Http/Controllers/GameController.php
            'questions' => $randomizedQuestions->values()->all()
=======
            'questions' => $randomizedQuestions->values()->all(),
            'data' => $randomizedQuestions->values()->all()
        ]);
    }

    /**
     * 3.1 دالة جلب آخر نتيجة assessment محفوظة للطفل لنفس game_type (difficulty_id)
     */
    public function getAssessmentResult(Request $request, int $child_id)
    {
        $request->validate([
            'game_type' => 'required|string'
        ]);

        $gameType = $request->query('game_type');

        // لازم نتحقق إن الطفل تابع للمستخدم الحالي
        $child = Child::where('id', $child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $result = GameResult::where('child_id', $child->id)
            ->where('game_type', $gameType)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $result
                ? [
                    'raw_score' => $result->raw_score,
                    'z_score' => $result->z_score,
                    'risk_level' => $result->risk_level,
                    'created_at' => $result->created_at,
                    'id' => $result->id,
                ]
                : null,
>>>>>>> 1ae74b281c6ff2aa89740f98bb404ecec0ea8b57:backend/app/Http/Controllers/GameController.php
        ]);
    }

    // 4. دالة حفظ نتيجة التقييم (وحساب الـ Z-Score وتحديد مسار التدريب)
   public function submitGameResult(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'game_type' => 'required|string', // 'assessment' or 'training'
            'raw_score' => 'required|numeric',
            'learning_difficulty_id' => 'required|exists:learning_difficulties,id', // ضروري للتحقق من الـ 90 يوم
        ]);

        $child = Child::where('id', $request->child_id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        // 1. [قاعدة الـ 90 يوم]: التحقق قبل حفظ أي تقييم جديد
        if ($request->game_type === 'assessment') {
            $lastAssessment = GameResult::where('child_id', $child->id)
                ->where('game_type', 'assessment')
                ->where('learning_difficulty_id', $request->learning_difficulty_id)
                ->latest()
                ->first();

            if ($lastAssessment && $lastAssessment->created_at->diffInDays(now()) < 90) {
                return response()->json(['status' => 'blocked', 'message' => 'التقييم متاح كل 3 أشهر فقط.'], 403);
            }
        }

        // 2. [قاعدة الـ 20 سؤال]: لو نوع اللعبة تدريب، سجل الجلسة في الـ TrainingLog
        if ($request->game_type === 'training') {
            // التحقق من الحد اليومي قبل الحفظ
            $solvedToday = \App\Models\TrainingLog::where('child_id', $child->id)
                ->whereDate('created_at', today())
                ->count();

            if ($solvedToday >= 20) {
                return response()->json(['status' => 'limit_reached', 'message' => 'أنت بطل! لقد أنهيت تدريب اليوم.'], 429);
            }
            
            // تسجيل الجلسة/السؤال
            \App\Models\TrainingLog::create(['child_id' => $child->id]);
        }

        // 3. حساب الـ Z-Score والنتيجة (المنطق الأصلي)
        $analysis = $this->diagnosisService->calculateGameZScore(
            $child->age, 
            $request->game_type, 
            (float) $request->raw_score
        );

        // 4. حفظ النتيجة
        $result = GameResult::create([
            'child_id' => $child->id,
            'learning_difficulty_id' => $request->learning_difficulty_id,
            'game_type' => $request->game_type,
            'raw_score' => $request->raw_score,
            'z_score' => $analysis['z_score'],
            'risk_level' => $analysis['risk_level'],
        ]);

        // 5. ترشيح مسار التدريب (تحديث مستوى الطفل)
        $trainingProgress = null;
        if ($request->game_type === 'assessment') {
            $startingLevel = ($analysis['risk_level'] === 'Moderate Risk') ? 2 : (($analysis['risk_level'] === 'No Risk') ? 3 : 1);
            $startingPercentage = ($analysis['risk_level'] === 'Moderate Risk') ? 30 : (($analysis['risk_level'] === 'No Risk') ? 60 : 0);

            $trainingProgress = TrainingProgress::updateOrCreate(
                ['child_id' => $child->id, 'training_type' => 'general'], // أو حسب نوع الصعوبة
                [
                    'current_level' => $startingLevel,
                    'progress_percentage' => $startingPercentage,
                    'next_level_unlocks_at' => now(),
                ]
            );
        }

<<<<<<< HEAD:app/Http/Controllers/GameController.php
=======
        // 4. حفظ أو تحديث مسار التدريب للطفل
        $trainingProgress = TrainingProgress::updateOrCreate(
            [
                'child_id' => $child->id, 
                'training_type' => $request->game_type
            ],
            [
                'current_level' => $startingLevel,
                'progress_percentage' => $startingPercentage,
                'next_level_unlocks_at' => now(), // يقدر يبدأ التدريب فورماً
            ]
        );

        // 5. إرجاع الرد للفرونت إند
>>>>>>> 1ae74b281c6ff2aa89740f98bb404ecec0ea8b57:backend/app/Http/Controllers/GameController.php
        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ وتحليل النتيجة بنجاح',
            'analysis' => $analysis,
            'training_roadmap' => $trainingProgress
        ], 201);
    }
}

