<?php
namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GameContent;
use App\Models\GameResult;
use App\Models\LearningDifficulty;
use App\Models\TrainingProgress;
use App\Services\DiagnosisService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    public function getAssessmentContent(int $difficulty_id)
    {
        $assessmentContent = GameContent::where('content_type', 'assessment')
            ->orderBy('id', 'asc')
            ->first();

        if (! $assessmentContent) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يوجد محتوى تقييم لهذا الاختبار',
            ], 404);
        }

        $questions = $this->extractAssessmentQuestions($assessmentContent);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'difficulty_level' => $assessmentContent->difficulty_level,
                'level_name'       => $assessmentContent->level_name,
                'questions'        => $questions,
            ],
        ]);
    }

    public function getGameContent(int $difficulty_id, int $level)
    {
        $gameContent = GameContent::where('learning_difficulty_id', $difficulty_id)
            ->where('difficulty_level', $level)
            ->where('content_type', 'training')
            ->first();

        if (! $gameContent) {
            return response()->json([
                'status'  => 'error',
                'message' => 'محتوى التدريب غير موجود لهذا المستوى',
            ], 404);
        }

        $data      = json_decode($gameContent->content_data, true);
        $questions = collect($data['questions'] ?? []);

        $randomizedQuestions = $questions->shuffle()->map(function ($question) use ($difficulty_id) {
            if ($difficulty_id == 1 && isset($question['options']) && is_array($question['options'])) {
                $question['options'] = collect($question['options'])->shuffle()->values()->all();
            }

            return $question;
        });

        return response()->json([
            'status'           => 'success',
            'level_name'       => $gameContent->level_name,
            'difficulty_level' => $gameContent->difficulty_level,
            'questions'        => $randomizedQuestions->values()->all(),
            'data'             => $randomizedQuestions->values()->all(),
        ]);
    }

    public function getAssessmentResult(Request $request, int $child_id)
    {
        $request->validate([
            'game_type' => 'required|string',
        ]);

        $gameType           = $request->query('game_type');
        $resolved           = $this->resolveGameTypeAndDifficulty($gameType, null);
        $normalizedGameType = $resolved['game_type'];

        $child = Child::where('id', $child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $result = GameResult::where('child_id', $child->id)
            ->where('session_type', 'assessment')
            ->where(function ($query) use ($gameType, $normalizedGameType) {
                $query->where('game_type', $gameType)
                    ->orWhere('game_type', $normalizedGameType);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'data'   => $result
                ? [
                'raw_score'       => $result->raw_score,
                'correct_count'   => $result->correct_count,
                'total_questions' => $result->total_questions,
                'z_score'         => $result->z_score,
                'risk_level'      => $result->risk_level,
                'created_at'      => $result->created_at,
                'id'              => $result->id,
            ]
                : null,
        ]);
    }

    public function submitGameResult(Request $request)
    {
        $request->validate([
            'child_id'                => 'required|exists:children,id',
            'game_type'               => 'required|string',
            'raw_score'               => 'required|numeric',
            'session_type'            => 'nullable|in:assessment,training',
            'learning_difficulty_id'  => 'nullable|integer|exists:learning_difficulties,id',
            'difficulty_level'        => 'nullable|integer|min:1|max:10',
            'correct_count'           => 'nullable|integer|min:0',
            'total_questions'         => 'nullable|integer|min:1',
            'reading_correct_count'   => 'nullable|integer|min:0',
            'reading_total_questions' => 'nullable|integer|min:0',
            'reading_percentage'      => 'nullable|numeric|min:0|max:100',
            'math_correct_count'      => 'nullable|integer|min:0',
            'math_total_questions'    => 'nullable|integer|min:0',
            'math_percentage'         => 'nullable|numeric|min:0|max:100',
        ]);

        $child = Child::where('id', $request->child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $sessionType = $request->input('session_type', 'assessment');
        $resolved    = $this->resolveGameTypeAndDifficulty(
            $request->game_type,
            $request->learning_difficulty_id
        );
        $gameType             = $resolved['game_type'];
        $learningDifficultyId = $resolved['learning_difficulty_id'];

        $analysis = $this->diagnosisService->calculateGameZScore(
            $child->age,
            $gameType,
            (float) $request->raw_score
        );

        $assessmentDiagnosis = null;
        if ($sessionType === 'assessment' && (
            $request->has('reading_total_questions') ||
            $request->has('math_total_questions') ||
            $request->has('reading_correct_count') ||
            $request->has('math_correct_count')
        )) {
            $assessmentDiagnosis = $this->buildAssessmentDiagnosis($request);
        }

        if ($sessionType === 'training') {

            $result = GameResult::updateOrCreate(

                [
                    'child_id'               => $child->id,
                    'session_type'           => 'training',
                    'learning_difficulty_id' => $learningDifficultyId,
                    'difficulty_level'       => $request->difficulty_level,
                ],

                [
                    'game_type'       => $gameType,
                    'raw_score'       => $request->raw_score,
                    'correct_count'   => $request->correct_count,
                    'total_questions' => $request->total_questions,
                    'z_score'         => $analysis['z_score'],
                    'risk_level'      => $analysis['risk_level'],
                ]
            );

        } else {

            $result = GameResult::create([
                'child_id'               => $child->id,
                'game_type'              => $gameType,
                'session_type'           => $sessionType,
                'learning_difficulty_id' => $learningDifficultyId,
                'difficulty_level'       => $request->difficulty_level,
                'raw_score'              => $request->raw_score,
                'correct_count'          => $request->correct_count,
                'total_questions'        => $request->total_questions,
                'z_score'                => $analysis['z_score'],
                'risk_level'             => $analysis['risk_level'],
            ]);

        }

        $trainingProgress = [];

        if (
            $sessionType === 'assessment'
            && $assessmentDiagnosis
        ) {

            $diagnosisType = $assessmentDiagnosis['diagnosis_type'];

            if (
                $diagnosisType === 'reading_difficulty'
                || $diagnosisType === 'both_difficulties'
            ) {

                $trainingProgress[] = TrainingProgress::updateOrCreate(
                    [
                        'child_id'      => $child->id,
                        'training_type' => 'visual_discrimination',
                    ],
                    [
                        'current_level'         => 1,
                        'progress_percentage'   => 0,
                        'next_level_unlocks_at' => now(),
                    ]
                );
            }

            if (
                $diagnosisType === 'math_difficulty'
                || $diagnosisType === 'both_difficulties'
            ) {

                $trainingProgress[] = TrainingProgress::updateOrCreate(
                    [
                        'child_id'      => $child->id,
                        'training_type' => 'magnitude_comparison',
                    ],
                    [
                        'current_level'         => 1,
                        'progress_percentage'   => 0,
                        'next_level_unlocks_at' => now(),
                    ]
                );
            }
        }

        $message = $sessionType === 'training'
            ? 'تم حفظ نتيجة التدريب بنجاح'
            : 'تم حفظ نتيجة التقييم وتحديد مسار التدريب بنجاح';

        return response()->json([
            'status'           => 'success',
            'message'          => $message,
            'analysis'         => $analysis,
            'diagnosis'        => $assessmentDiagnosis,
            'data'             => $result,
            'training_roadmap' => $trainingProgress,
        ], 201);
    }

    private function resolveGameTypeAndDifficulty(string $gameTypeInput, ?int $learningDifficultyId): array
    {
        if ($learningDifficultyId) {
            $difficulty = LearningDifficulty::find($learningDifficultyId);
            if ($difficulty?->test_type) {
                return [
                    'game_type'              => $difficulty->test_type,
                    'learning_difficulty_id' => $difficulty->id,
                ];
            }
        }

        if (is_numeric($gameTypeInput)) {
            $difficulty = LearningDifficulty::find((int) $gameTypeInput);
            if ($difficulty?->test_type) {
                return [
                    'game_type'              => $difficulty->test_type,
                    'learning_difficulty_id' => $difficulty->id,
                ];
            }
        }

        $difficulty = LearningDifficulty::where('test_type', $gameTypeInput)->first();

        return [
            'game_type'              => $gameTypeInput,
            'learning_difficulty_id' => $difficulty?->id ?? $learningDifficultyId,
        ];
    }

    private function buildAssessmentDiagnosis(Request $request): array
    {
        // Threshold rule: any category below 60% is treated as weak.
        $threshold = 60;

        $reading = $this->buildCategorySummary($request, 'reading');
        $math    = $this->buildCategorySummary($request, 'math');

        $readingWeak = $reading['total_questions'] > 0 && $reading['percentage'] < $threshold;
        $mathWeak    = $math['total_questions'] > 0 && $math['percentage'] < $threshold;

        $diagnosisType  = 'no_significant_difficulty';
        $recommendation = 'لا توجد مؤشرات واضحة على صعوبة محددة حاليًا.';

        if ($readingWeak && $mathWeak) {
            $diagnosisType  = 'both_difficulties';
            $recommendation = 'يوصى بالاستمرار في تدريبات القراءة والحساب';
        } elseif ($readingWeak) {
            $diagnosisType  = 'reading_difficulty';
            $recommendation = 'يوصى بالاستمرار في تدريبات القراءة';
        } elseif ($mathWeak) {
            $diagnosisType  = 'math_difficulty';
            $recommendation = 'يوصى بالاستمرار في تدريبات الحساب';
        }

        return [
            'diagnosis_type' => $diagnosisType,
            'recommendation' => $recommendation,
            'threshold'      => $threshold,
            'reading'        => $reading,
            'math'           => $math,
        ];
    }

    private function buildCategorySummary(Request $request, string $prefix): array
    {
        $correctCount   = (int) $request->input($prefix . '_correct_count', 0);
        $totalQuestions = (int) $request->input($prefix . '_total_questions', 0);
        $percentage     = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

        return [
            'correct_count'   => $correctCount,
            'total_questions' => $totalQuestions,
            'percentage'      => $percentage,
        ];
    }

    private function extractAssessmentQuestions(GameContent $level)
    {
        $data = json_decode($level->content_data, true);

        return collect($data['questions'] ?? [])
            ->shuffle()
            ->map(function ($question) use ($level) {
                if (isset($question['options']) && is_array($question['options'])) {
                    $question['options'] = collect($question['options'])->shuffle()->values()->all();
                }

                $question['difficulty_level'] = $level->difficulty_level;

                return $question;
            })
            ->values()
            ->all();
    }
}
