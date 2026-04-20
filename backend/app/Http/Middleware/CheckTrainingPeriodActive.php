<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TrainingPeriod;

class CheckTrainingPeriodActive
{
    public function handle(Request $request, Closure $next)
    {
        $activePeriod = TrainingPeriod::where('is_active', true)->first();
        if (!$activePeriod) {
            return response()->json(['message' => 'لا توجد فترة تدريب نشطة حالياً.'], 403);
        }
        $request->merge(['active_training_period' => $activePeriod]);
        return $next($request);
    }
}