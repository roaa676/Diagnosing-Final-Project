<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Child;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        // مسح التوكن الحالي اللي اليوزر داخل بيه
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'children' => 'required|array' // التأكد من إرسال قائمة أطفال
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    // تخزين كل طفل في جدول الأطفال وربطه بالمستخدم
    foreach ($request->children as $child) {
        Child::create([
            'user_id' => $user->id,
            'name' => $child['name'],
            'age' => $child['age']
        ]);
    }

    return response()->json(['message' => 'User and Children Registered successfully']);
}

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid login'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
