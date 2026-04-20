<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FeatureFlag;

class CheckFeatureFlag
{
    public function handle(Request $request, Closure $next, string $featureName)
    {
        $flag = FeatureFlag::where('name', $featureName)->first();

        if (!$flag || !$flag->is_open) {
            return response()->json([
                'message' => 'هذه الخدمة غير متاحة حالياً. يرجى مراجعة الإدارة.',
                'feature' => $featureName
            ], 403);
        }

        return $next($request);
    }
}