<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Storage; // ضروري جداً لمسح الملفات القديمة

class ChildController extends Controller
{
    /**
     * جلب كل الأطفال الخاصين بالمستخدم الحالي
     */
    public function index(Request $request)
    {
        $children = Child::where('user_id', $request->user()->id)->get();

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

        // التأكد من أن الطفل يخص المستخدم الحالي
        $child = Child::where('id', $id)
                      ->where('user_id', $request->user()->id)
                      ->first();

        if (!$child) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح لك بتعديل بيانات هذا الطفل'], 403);
        }

        // 1. مسح الصورة القديمة من السيرفر إذا وجدت
        if ($child->image) {
            // نقوم بإزالة 'storage/' من المسار للوصول للملف الحقيقي في الـ disk
            $oldPath = str_replace('storage/', '', $child->image);
            Storage::disk('public')->delete($oldPath);
        }

        // 2. رفع الصورة الجديدة في مجلد 'children_profiles'
        $path = $request->file('image')->store('children_profiles', 'public');

        // 3. تحديث مسار الصورة في قاعدة البيانات
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