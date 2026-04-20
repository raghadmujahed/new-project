<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleDepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next, ...$rules)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    foreach ($rules as $rule) {

        [$role, $department] = explode(':', $rule);

        if (
            $user->role?->name === $role &&
            $user->department === $department
        ) {
            return $next($request);
        }
    }

    return response()->json([
        'message' => 'Unauthorized for this department/role'
    ], 403);
}
}
