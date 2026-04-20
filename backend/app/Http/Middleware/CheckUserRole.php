<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role?->name, $roles)) {
            return response()->json(['message' => 'غير مصرح لك بالوصول إلى هذه الصفحة.'], 403);
        }
        return $next($request);
    }
}