<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage; // ضفنا دي عشان مسح الصور القديمة

class ProfileController extends Controller
{
    /**
     * جلب بيانات بروفايل المستخدم الحالي
     */
    public function show(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    /**
     * تحديث الاسم والإيميل
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        // صلحنا الأقواس هنا عشان المحرر ميطلعش خط أحمر
        $user->update($request->only(['name', 'email']));

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث بياناتك بنجاح',
            'data' => $user
        ]);
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

    /**
     * رفع وتحديث صورة البروفايل (مع مسح القديمة)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        // 1. لو اليوزر عنده صورة قديمة، امسحها الأول من فولدر السيرفر
        if ($user->profile_image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_image));
        }

        // 2. ارفع الصورة الجديدة
        $path = $request->file('image')->store('profiles', 'public');

        // 3. احفظ المسار الجديد في الداتا بيز
        $user->update([
            'profile_image' => 'storage/' . $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث صورة الحساب بنجاح',
            'image_url' => asset($user->profile_image)
        ]);
    }
}
