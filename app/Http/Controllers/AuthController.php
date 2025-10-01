<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

   // app/Http/Controllers/AuthController.php
public function register(Request $request) {
    $request->validate([
        'user_name' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'role' => 'required|in:admin,publisher,student'
    ]);

    $user = User::create([
        'user_name' => $request->user_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user
    ], 201);
}

public function login(Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = $request->user();
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->user_name,
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
}

    public function user(Request $request)
    {
        return response()->json($request->user());
    }


  public function logout(Request $request)
{
    try {
       
        $request->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
            'success' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'فشل تسجيل الخروج',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
