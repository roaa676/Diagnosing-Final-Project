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
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'children' => 'required|array',
        'children.*.name' => 'required|string',
        'children.*.age' => 'required|integer|min:3|max:18'
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password'])
    ]);

    // تخزين كل طفل في جدول الأطفال وربطه بالمستخدم
    foreach ($validated['children'] as $child) {
        Child::create([
            'user_id' => $user->id,
            'name' => $child['name'],
            'age' => $child['age']
        ]);
    }

    $token = $user->createToken('token')->plainTextToken;

    return response()->json([
        'message' => 'User and Children Registered successfully',
        'token' => $token
    ], 201);
}

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Invalid login credentials'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
