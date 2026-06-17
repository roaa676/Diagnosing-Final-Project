<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GameResult;
use App\Models\LearningDifficulty;
use App\Models\Questionnaire;
use App\Models\TrainingProgress;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function getChildHistory(int $child_id, Request $request)
    {
        $child = Child::where('id', $child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $difficulties = LearningDifficulty::all()->keyBy('id');

        $questionnaires = Questionnaire::where('child_id', $child_id)
            ->orderByDesc('created_at')
            ->get();

        $gameResults = GameResult::where('child_id', $child_id)
            ->orderByDesc('created_at')
            ->get();

        $entries = collect();

        foreach ($questionnaires as $questionnaire) {
            $difficulty = $difficulties->get($questionnaire->learning_difficulty_id);
            $difficultyName = $difficulty?->name_ar ?? 'صعوبة تعلم';

            $entries->push([
                'id' => 'questionnaire_' . $questionnaire->id,
                'activity_type' => 'استبيان ولي الأمر',
                'description' => $difficultyName . ' — تقييم سلوكي',
                'timestamp' => $questionnaire->created_at?->toIso8601String(),
                'learning_difficulty_id' => $questionnaire->learning_difficulty_id,
                'result' => [
                    'score' => $questionnaire->total_risk_score,
                    'risk_level' => $questionnaire->risk_level,
                ],
            ]);
        }

        foreach ($gameResults as $result) {
            $difficulty = $result->learning_difficulty_id
                ? $difficulties->get($result->learning_difficulty_id)
                : LearningDifficulty::where('test_type', $result->game_type)->first();

            $difficultyName = $difficulty?->name_ar ?? $result->game_type;
            $isTraining = $result->session_type === 'training';
            $levelLabel = $result->difficulty_level ? 'المستوى ' . $result->difficulty_level : null;
            $scorePercent = $this->scorePercentage($result);

            $entries->push([
                'id' => 'game_' . $result->id,
                'activity_type' => $isTraining ? 'تدريب' : 'تقييم شامل',
                'description' => trim(implode(' • ', array_filter([
                    $difficultyName,
                    $levelLabel,
                    $isTraining ? null : ($result->risk_level ? 'مستوى الخطر: ' . $result->risk_level : null),
                ]))),
                'timestamp' => $result->created_at?->toIso8601String(),
                'learning_difficulty_id' => $result->learning_difficulty_id ?? $difficulty?->id,
                'result' => [
                    'raw_score' => $result->raw_score,
                    'score' => $scorePercent,
                    'correct_count' => $result->correct_count,
                    'total_questions' => $result->total_questions,
                    'risk_level' => $result->risk_level,
                    'z_score' => $result->z_score,
                    'level' => $result->difficulty_level,
                ],
            ]);
        }

        $sorted = $entries->sortByDesc('timestamp')->values();

        return response()->json([
            'status' => 'success',
            'child_name' => $child->name,
            'history_count' => $sorted->count(),
            'data' => $sorted,
        ]);
    }

    private function scorePercentage(GameResult $result): ?int
    {
        if ($result->total_questions && $result->correct_count !== null) {
            return (int) round(($result->correct_count / $result->total_questions) * 100);
        }

        if ($result->total_questions && $result->raw_score !== null) {
            $maxScore = $result->total_questions * 10;

            return $maxScore > 0 ? (int) round(($result->raw_score / $maxScore) * 100) : null;
        }

        return null;
    }
}
