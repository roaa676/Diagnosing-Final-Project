<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GameResult;
use App\Models\LearningDifficulty;
use App\Models\TrainingProgress;
use App\Services\DiagnosisService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    public function getComprehensiveReport(int $child_id, Request $request)
    {
        $child = Child::where('id', $child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $latestQuestionnaire = $child->questionnaires()->latest()->first();
        $latestAssessment = GameResult::where('child_id', $child_id)
            ->where('session_type', 'assessment')
            ->latest()
            ->first();
        $latestGame = $latestAssessment ?? $child->gameResults()->latest()->first();

        $trainingSessions = GameResult::where('child_id', $child_id)
            ->where('session_type', 'training')
            ->orderByDesc('created_at')
            ->get();

        $trainingProgress = TrainingProgress::where('child_id', $child_id)->get();
        $difficulties = LearningDifficulty::all()->keyBy('id');

        $qRisk = $latestQuestionnaire ? $latestQuestionnaire->risk_level : null;
        $gRisk = $latestGame ? $latestGame->risk_level : null;
        $finalConclusion = $this->diagnosisService->getFinalConclusion($qRisk, $gRisk);

        $scoreValues = $trainingSessions->map(fn (GameResult $session) => $this->sessionScorePercent($session))
            ->filter(fn ($score) => $score !== null);

        $averageScore = $scoreValues->isNotEmpty() ? round($scoreValues->avg()) : null;
        $currentLevel = $trainingProgress->max('current_level');
        $timeseries = $this->buildTimeseries($trainingSessions);

        $topics = $this->buildTopicCards($trainingSessions, $difficulties);

        return response()->json([
            'status' => 'success',
            'child_info' => [
                'name' => $child->name,
                'age' => $child->age . ' سنوات',
            ],
            'average_score' => $averageScore,
            'avg_score' => $averageScore,
            'completed_trainings_count' => $trainingSessions->count(),
            'completed_count' => $trainingSessions->count(),
            'current_level' => $currentLevel ?: null,
            'level' => $currentLevel ?: null,
            'timeseries' => $timeseries,
            'history_chart' => $timeseries,
            'trainings' => $topics,
            'topics' => $topics,
            'assessments' => [
                'parent_questionnaire' => [
                    'risk_level' => $qRisk ?? 'لم يتم التقييم',
                    'date' => $latestQuestionnaire ? $latestQuestionnaire->created_at->format('Y-m-d') : null,
                ],
                'game_performance' => [
                    'game_type' => $latestGame ? $latestGame->game_type : 'لم يلعب بعد',
                    'risk_level' => $gRisk ?? 'لم يتم التقييم',
                    'z_score' => $latestGame ? $latestGame->z_score : null,
                    'date' => $latestGame ? $latestGame->created_at->format('Y-m-d') : null,
                ],
            ],
            'final_conclusion' => $finalConclusion,
        ], 200);
    }

    private function sessionScorePercent(GameResult $session): ?int
    {
        if ($session->total_questions && $session->correct_count !== null) {
            return (int) round(($session->correct_count / $session->total_questions) * 100);
        }

        if ($session->total_questions && $session->raw_score !== null) {
            $maxScore = $session->total_questions * 10;

            return $maxScore > 0 ? (int) round(($session->raw_score / $maxScore) * 100) : null;
        }

        return null;
    }

    private function buildTimeseries($trainingSessions): array
    {
        return $trainingSessions
            ->sortBy('created_at')
            ->values()
            ->map(function (GameResult $session) {
                return [
                    'label' => $session->created_at?->format('d M'),
                    'date' => $session->created_at?->format('Y-m-d'),
                    'value' => $this->sessionScorePercent($session) ?? 0,
                    'score' => $this->sessionScorePercent($session) ?? 0,
                ];
            })
            ->all();
    }

    private function buildTopicCards($trainingSessions, $difficulties): array
    {
        $latestByKey = [];

        foreach ($trainingSessions as $session) {
            $difficulty = $session->learning_difficulty_id
                ? $difficulties->get($session->learning_difficulty_id)
                : LearningDifficulty::where('test_type', $session->game_type)->first();

            $key = ($difficulty?->id ?? $session->game_type) . '_' . ($session->difficulty_level ?? 0);

            if (!isset($latestByKey[$key]) || $session->created_at > $latestByKey[$key]->created_at) {
                $latestByKey[$key] = $session;
            }
        }

        $cards = [];

        foreach ($latestByKey as $session) {
            $difficulty = $session->learning_difficulty_id
                ? $difficulties->get($session->learning_difficulty_id)
                : LearningDifficulty::where('test_type', $session->game_type)->first();

            $score = $this->sessionScorePercent($session);
            $level = $session->difficulty_level;

            $cards[] = [
                'title' => ($difficulty?->name_ar ?? 'تدريب') . ($level ? ' — المستوى ' . $level : ''),
                'subtitle' => $session->created_at
                    ? Carbon::parse($session->created_at)->locale('ar')->translatedFormat('d F Y')
                    : '',
                'badge' => $score !== null ? $score . '%' : '-',
                'score' => $score,
                'level' => $level,
                'icon' => 'pi pi-star-fill',
                'tone' => ($score ?? 0) >= 80 ? 'green' : (($score ?? 0) >= 50 ? 'blue' : 'orange'),
                'progress' => $score,
                'featured' => false,
            ];
        }

        return $cards;
    }
}
