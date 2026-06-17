<?php
namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GameResult;
use App\Models\TrainingProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrainingController extends Controller
{

    public function getTrainingRoadmap(Request $request, int $child_id)
    {
        $progress = TrainingProgress::where('child_id', $child_id)->get();

        if ($progress->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'الطفل لم يقم بالتقييم بعد. يجب إجراء التقييم الشامل أولاً.',
            ], 400);
        }

        $roadmap = $progress->map(function ($training) {
            // التحقق هل المستوى متاح ولا لسه مقفول زمنياً
            $isLocked = Carbon::now()->lessThan($training->next_level_unlocks_at);

            return [
                'training_type'       => $training->training_type,
                'current_level'       => $training->current_level,
                'progress_percentage' => $training->progress_percentage . '%',
                'is_locked'           => $isLocked,
                'unlocks_at'          => $training->next_level_unlocks_at ? Carbon::parse($training->next_level_unlocks_at)->diffForHumans() : 'متاح الآن',
                // هترجع للفرونت إند: "يفتح بعد 23 ساعة" مثلاً
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $roadmap,
        ]);
    }

    public function completeTrainingLevel(Request $request)
    {
        $request->validate([
            'child_id'      => 'required|exists:children,id',
            'training_type' => 'required|string',
        ]);

        $child = Child::where('id', $request->child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $training = TrainingProgress::where('child_id', $request->child_id)
            ->where('training_type', $request->training_type)
            ->first();

        if (! $training) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يوجد مسار تدريب لهذا الطفل',
            ], 404);
        }

        // ترقية الطفل للمستوى التالي
        $nextLevel     = $training->current_level + 1;
        $newPercentage = min($training->progress_percentage + 20, 100); // زيادة النسبة لحد أقصى 100%

        // تطبيق المعيار العالمي: قفل المستوى التالي لمدة 24 ساعة للمراجعة الذهنية
        $training->update([
            'current_level'         => $nextLevel,
            'progress_percentage'   => $newPercentage,
            'next_level_unlocks_at' => Carbon::now()->addHours(24),
        ]);

        return response()->json([
            'status'                => 'success',
            'message'               => 'تم إنهاء المستوى العظيم! استرح الآن، المستوى القادم سيفتح غداً.',
            'new_percentage'        => $newPercentage . '%',
            'next_level_unlocks_in' => '24 Hours',
        ]);
    }
    public function getTrainingResults(Request $request, int $child_id)
    {
        $child = Child::where('id', $child_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized access',
            ], 403);
        }

        $difficultyId = $request->query('difficulty_id');

        $results = GameResult::where('child_id', $child_id)
            ->where('session_type', 'training')
            ->where('learning_difficulty_id', $difficultyId)
            ->orderByDesc('id')
            ->get()
            ->unique('difficulty_level')
            ->values()
            ->map(function ($result) {

                $percentage = 0;

                if ($result->total_questions > 0) {
                    $percentage = round(
                        ($result->correct_count / $result->total_questions) * 100
                    );
                }

                return [
                    'level'           => $result->difficulty_level,
                    'score'           => $percentage,
                    'correct_count'   => $result->correct_count,
                    'total_questions' => $result->total_questions,
                    'completed'       => true,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $results,
        ]);
    }

}
