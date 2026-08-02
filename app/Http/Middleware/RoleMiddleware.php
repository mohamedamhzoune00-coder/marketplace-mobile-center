<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // إلا ماكانش المستخدم مسجل الدخول
        if (!$request->user()) {

            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);

        }

        // إلا الرول ماشي هو المطلوب
        if ($request->user()->role != $role) {

            return response()->json([
                'message' => 'Accès interdit'
            ], 403);

        }

        // إذا كلشي صحيح كمل للـ Controller
        return $next($request);
    }
}