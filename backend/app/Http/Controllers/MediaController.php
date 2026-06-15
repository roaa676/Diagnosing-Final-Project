<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // 1. رفع صورة لبروفايل الأب
    public function uploadUserProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // مسح الصورة القديمة لو موجودة
        if ($user->profile_image) {
            Storage::delete('public/' . $user->profile_image);
        }

        $path = $request->file('image')->store('profiles', 'public');
        $user->update(['profile_image' => $path]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث صورة البروفايل',
            'image_url' => asset('storage/' . $path)
        ]);
    }

    // 2. رفع صورة للطفل
    public function uploadChildImage(Request $request, $child_id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $child = Child::where('id', $child_id)->where('user_id', $request->user()->id)->firstOrFail();

        if ($child->image) {
            Storage::delete('public/' . $child->image);
        }

        $path = $request->file('image')->store('children', 'public');
        $child->update(['image' => $path]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث صورة الطفل',
            'image_url' => asset('storage/' . $path)
        ]);
    }
}
