<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingProgress;
use Carbon\Carbon;

class TrainingController extends Controller
{
    /**
     * جلب حالة التدريبات للطفل (عشان الفرونت إند يرسم المستويات)
     */
    public function getTrainingRoadmap(Request $request, int $child_id)
    {
        $progress = TrainingProgress::where('child_id', $child_id)->get();

        if ($progress->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطفل لم يقم بالتقييم بعد. يجب إجراء التقييم الشامل أولاً.'
            ], 400);
        }

        $roadmap = $progress->map(function($training) {
            // التحقق هل المستوى متاح ولا لسه مقفول زمنياً
            $isLocked = Carbon::now()->lessThan($training->next_level_unlocks_at);
            
            return [
                'training_type' => $training->training_type,
                'current_level' => $training->current_level,
                'progress_percentage' => $training->progress_percentage . '%',
                'is_locked' => $isLocked,
                'unlocks_at' => $training->next_level_unlocks_at ? Carbon::parse($training->next_level_unlocks_at)->diffForHumans() : 'متاح الآن',
                // هترجع للفرونت إند: "يفتح بعد 23 ساعة" مثلاً
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $roadmap
        ]);
    }

    /**
     * إرسال نتيجة إكمال مستوى تدريب (عشان نرقيه للمستوى اللي بعده ونقفل التدريب 24 ساعة)
     */
    public function completeTrainingLevel(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'training_type' => 'required|string'
        ]);

        $training = TrainingProgress::where('child_id', $request->child_id)
                                    ->where('training_type', $request->training_type)
                                    ->first();

        // ترقية الطفل للمستوى التالي
        $nextLevel = $training->current_level + 1;
        $newPercentage = min($training->progress_percentage + 20, 100); // زيادة النسبة لحد أقصى 100%

        // تطبيق المعيار العالمي: قفل المستوى التالي لمدة 24 ساعة للمراجعة الذهنية
        $training->update([
            'current_level' => $nextLevel,
            'progress_percentage' => $newPercentage,
            'next_level_unlocks_at' => Carbon::now()->addHours(24), 
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنهاء المستوى العظيم! استرح الآن، المستوى القادم سيفتح غداً.',
            'new_percentage' => $newPercentage . '%',
            'next_level_unlocks_in' => '24 Hours'
        ]);
    }
}