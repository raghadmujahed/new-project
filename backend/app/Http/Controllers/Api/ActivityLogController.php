<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');


    $query = Activity::with(['causer']); // 👈 أهم سطر

    if ($request->filled('user_id')) {
        $query->where('causer_id', $request->user_id);
    }

  

    $logs = $query->latest()->paginate(15);

    return response()->json($logs);


        // فلترة حسب المستخدم
     

        // فلترة حسب الحدث (login / created ...)
        if ($request->action) {
            $query->where(function ($q) use ($request) {
                $q->where('event', 'like', "%{$request->action}%")
                  ->orWhere('log_name', 'like', "%{$request->action}%")
                  ->orWhere('description', 'like', "%{$request->action}%");
            });
        }

        // فلترة بالتاريخ
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($logs);
    }

    public function show($id)
    {
        $log = Activity::with('causer')->findOrFail($id);

        return response()->json($log);
    }

    public function destroy($id)
    {
        $log = Activity::findOrFail($id);
        $log->delete();

        return response()->json(['message' => 'تم حذف السجل']);
    }
}