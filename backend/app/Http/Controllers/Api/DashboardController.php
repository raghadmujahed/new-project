<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingAssignment;
use App\Models\User;
use App\Models\TrainingSite;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $stats = [
            'total_students' => User::whereHas('role', fn($q) => $q->where('name', 'student'))->count(),
            'active_trainings' => TrainingAssignment::where('status', 'ongoing')->count(),
            'completed_trainings' => TrainingAssignment::where('status', 'completed')->count(),
            'total_sites' => TrainingSite::count(),
            'pending_evaluations' => Evaluation::whereNull('total_score')->count(),
        ];
        
        // إحصائيات حسب المستخدم
        if ($request->user()->role?->name === 'teacher') {
            $stats['my_students'] = TrainingAssignment::where('teacher_id', $request->user()->id)
                ->with('enrollment.user')->get()->pluck('enrollment.user')->unique()->count();
        } elseif ($request->user()->role?->name === 'academic_supervisor') {
            $stats['my_students'] = TrainingAssignment::where('academic_supervisor_id', $request->user()->id)->count();
        } elseif ($request->user()->role?->name === 'student') {
            $stats['my_training'] = TrainingAssignment::whereHas('enrollment', fn($q) => $q->where('user_id', $request->user()->id))
                ->first();
        }
        
        return response()->json($stats);
    }
}