<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Storage;

class ChildController extends Controller
{
    /**
     * جلب كل الأطفال الخاصين بالمستخدم الحالي
     */
    public function index(Request $request)
    {
        $children = Child::where('user_id', $request->user()->id)->get();

        // اللف على كل طفل والتحقق من جدول الاستبيان وجدول التقييم
        foreach ($children as $child) {
            // 1. هل حل الاستبيان؟
            $child->has_completed_questionnaire = \App\Models\Questionnaire::where('child_id', $child->id)->exists();
            
            // 2. هل حل ألعاب التقييم؟ (تأكد إن الموديل اسمه GameResult)
            $child->has_completed_assessment = \App\Models\GameResult::where('child_id', $child->id)->exists();
        }

        return response()->json([
            'status' => 'success',
            'data' => $children
        ], 200);
    }

    /**
     * جلب بيانات طفل واحد محدد
     */
    public function show(Request $request, int $id)
    {
        $child = Child::where('id', $id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'الطفل غير موجود أو غير مصرح لك'], 404);
        }

        // التحقق من الاستبيان والتقييم للطفل المحدد
        $child->has_completed_questionnaire = \App\Models\Questionnaire::where('child_id', $child->id)->exists();
        $child->has_completed_assessment = \App\Models\GameResult::where('child_id', $child->id)->exists();

        return response()->json([
            'status' => 'success',
            'data' => $child
        ], 200);
    }

    /**
     * إضافة طفل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
        ]);

        $child = Child::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'age' => $request->age,
        ]);

        // طفل جديد معناه أكيد لسه معملش الاستبيان ولا التقييم
        $child->has_completed_questionnaire = false;
        $child->has_completed_assessment = false;

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة ملف الطفل بنجاح',
            'data' => $child
        ], 201);
    }

    /**
     * رفع وتحديث صورة الطفل (مع مسح الصورة القديمة)
     */
    public function uploadImage(Request $request, int $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $child = Child::where('id', $id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح لك بتعديل بيانات هذا الطفل'], 403);
        }

        if ($child->image) {
            $oldPath = str_replace('storage/', '', $child->image);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('image')->store('children_profiles', 'public');

        $child->update([
            'image' => 'storage/' . $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث صورة الطفل بنجاح',
            'image_url' => asset($child->image)
        ]);
    }
}