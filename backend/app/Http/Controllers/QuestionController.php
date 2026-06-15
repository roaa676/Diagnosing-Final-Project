<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // 1. جلب كل الأسئلة
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Question::with('difficulty')->orderBy('learning_difficulty_id')->get()
        ]);
    }

    // 2. إضافة سؤال جديد
    public function store(Request $request)
    {
        $request->validate([
            'learning_difficulty_id' => 'required|exists:learning_difficulties,id',
            'question_text' => 'required|string',
            'order' => 'nullable|integer'
        ]);

        $question = Question::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة السؤال بنجاح',
            'data' => $question
        ], 201);
    }

    // 3. تعديل سؤال
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        $question->update($request->only(['question_text', 'order']));

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث السؤال بنجاح',
            'data' => $question
        ]);
    }

    // 4. حذف سؤال
    public function destroy($id)
    {
        Question::findOrFail($id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف السؤال بنجاح'
        ]);
    }
}
