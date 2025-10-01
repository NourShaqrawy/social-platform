<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // تسجيل مستخدم جديد
public function register(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role'     => 'in:admin,user' // اختياري
    ]);

    // إنشاء المستخدم
    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role'     => $validated['role'] ?? 'user',
    ]);

    // توليد توكن باستخدام Laravel Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    // إرجاع الرد بصيغة JSON
    return response()->json([
        'message' => 'تم تسجيل المستخدم بنجاح.',
        'token'   => $token,
        'user'    => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]
    ], 201);
}

    // تسجيل الدخول



public function login(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    // جلب المستخدم من قاعدة البيانات
    $user = User::where('email', $request->email)->first();

    // التحقق من وجود المستخدم وكلمة المرور
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'بيانات الدخول غير صحيحة.'], 401);
    }

    // توليد توكن باستخدام Laravel Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    // إرجاع الرد بصيغة JSON
    return response()->json([
        'message' => 'تم تسجيل الدخول بنجاح.',
        'token'   => $token,
        'user'    => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]
    ]);
}

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
