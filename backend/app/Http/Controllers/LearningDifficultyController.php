<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningDifficulty;
use App\Models\Question; // 💡 دي غالباً اللي كانت ناقصة وعملت الـ 500

class LearningDifficultyController extends Controller
{
    // دالة جلب كل الصعوبات
    public function index()
    {
        $difficulties = LearningDifficulty::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $difficulties
        ]);
    }

    // دالة جلب أسئلة الاستبيان الخاصة بصعوبة معينة
    public function getQuestions(int $id)
    {
        // بنجيب الأسئلة اللي الـ learning_difficulty_id بتاعها بيساوي الـ ID اللي مبعوت في الرابط
        $questions = Question::where('learning_difficulty_id', $id)->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا توجد أسئلة لهذه الصعوبة حالياً'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }
}   