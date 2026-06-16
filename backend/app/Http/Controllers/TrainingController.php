<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingProgress;
use Carbon\Carbon;

class TrainingController extends Controller
{
    // الحد الأقصى للعب في اليوم بالدقائق (معيار طبي لحماية الطفل)
    private $dailyLimitMinutes = 30;

    /**
     * جلب حالة التدريبات للطفل (عشان الفرونت إند يرسم المستويات ويشيك على الأقفال)
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

        $today = Carbon::today()->toDateString();

        $roadmap = $progress->map(function($training) use ($today) {
            
            // 1. فحص اليوم: لو دخلنا في يوم جديد، صفر عداد الدقائق تلقائياً
            if ($training->last_played_date !== $today) {
                $training->update([
                    'daily_time_spent' => 0,
                    'last_played_date' => $today
                ]);
            }

            // 2. التحقق من القفل الزمني للمستوى (الـ 24 ساعة اللي أنت كنت عاملها)
            $isTimeLocked = $training->next_level_unlocks_at ? Carbon::now()->lessThan($training->next_level_unlocks_at) : false;
            
            // 3. التحقق من قفل الحد اليومي (هل خلص الـ 30 دقيقة بتوعه النهاردة؟)
            $isDailyLimitReached = $training->daily_time_spent >= $this->dailyLimitMinutes;

            // المستوى هيكون مقفول لو وقت المراجعة مخلصش أو لو خلص دقائق النهاردة
            $isLocked = $isTimeLocked || $isDailyLimitReached;

            // تحديد سبب القفل للفرونت إند عشان يعرض الرسالة الصح
            $lockReason = 'none';
            if ($isDailyLimitReached) { $lockReason = 'daily_limit'; }
            elseif ($isTimeLocked) { $lockReason = 'level_time_lock'; }

            return [
                'training_type' => $training->training_type,
                'current_level' => $training->current_level,
                'progress_percentage' => $training->progress_percentage . '%',
                'daily_time_spent_minutes' => $training->daily_time_spent,
                'remaining_minutes_today' => max(0, $this->dailyLimitMinutes - $training->daily_time_spent),
                'is_locked' => $isLocked,
                'lock_reason' => $lockReason,
                'unlocks_at' => $isDailyLimitReached ? 'غداً (يوم جديد)' : ($training->next_level_unlocks_at ? Carbon::parse($training->next_level_unlocks_at)->diffForHumans() : 'متاح الآن'),
            ];
    });

        return response()->json([
            'status' => 'success',
            'data' => $roadmap
        ]);
    }

    /**
     * إرسال نتيجة إكمال مستوى تدريب (ترقية + قفل 24 ساعة + إضافة وقت اللعب)
     */
    public function completeTrainingLevel(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'training_type' => 'required|string',
            'minutes_played' => 'required|integer|min:1' // الفرونت إند بيبعت الجلسة دي خدت كام دقيقة
        ]);

        $training = TrainingProgress::where('child_id', $request->child_id)
                                    ->where('training_type', $request->training_type)
                                    ->first();

        if (!$training) {
            return response()->json(['status' => 'error', 'message' => 'البيانات غير موجودة'], 404);
        }

        $today = Carbon::today()->toDateString();
        $currentTime = ($training->last_played_date === $today) ? $training->daily_time_spent : 0;

        // ترقية الطفل للمستوى التالي وزيادة النسبة
        $nextLevel = $training->current_level + 1;
        $newPercentage = min($training->progress_percentage + 20, 100); 

        // تحديث كل البيانات بما فيها الوقت اليومي الجديد
        $training->update([
            'current_level' => $nextLevel,
            'progress_percentage' => $newPercentage,
            'next_level_unlocks_at' => Carbon::now()->addHours(24), // قفل المستوى الجديد 24 ساعة للمراجعة
            'daily_time_spent' => $currentTime + $request->minutes_played,
            'last_played_date' => $today
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنهاء المستوى العظيم! استرح الآن، المستوى القادم سيفتح غداً.',
            'new_level' => $nextLevel,
            'total_time_today' => ($currentTime + $request->minutes_played) . ' Minutes'
        ]);
    }

    /**
     * تحديث وقت اللعب فقط (عشان لو الطفل لعب وخرج من غير ما يخلص المستوى بالكامل)
     */
    public function updateDailyTime(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'training_type' => 'required|string',
            'minutes_played' => 'required|integer|min:1'
        ]);

        $training = TrainingProgress::where('child_id', $request->child_id)
                                    ->where('training_type', $request->training_type)
                                    ->first();

        if (!$training) {
            return response()->json(['status' => 'error', 'message' => 'البيانات غير موجودة'], 404);
        }

        $today = Carbon::today()->toDateString();
        $currentTime = ($training->last_played_date === $today) ? $training->daily_time_spent : 0;

        $training->update([
            'daily_time_spent' => $currentTime + $request->minutes_played,
            'last_played_date' => $today
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث وقت اللعب اليومي بنجاح.',
            'total_time_today' => ($currentTime + $request->request->get('minutes_played'))
        ]);
    }
}