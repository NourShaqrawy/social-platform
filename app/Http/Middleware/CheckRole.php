<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // تحقق من أن المستخدم مسجل الدخول
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // تحقق من أن الدور يطابق الدور المطلوب
        if (Auth::user()->role !== $role) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // السماح بالمتابعة
        return $next($request);
    }
}
