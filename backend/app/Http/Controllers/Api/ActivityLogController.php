<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityLogResource;

class ActivityLogController extends Controller
{
   public function index(Request $request)
{
    $query = Activity::with('causer');

    // الفلاتر ...
    if ($request->filled('user_id')) {
        $query->where('causer_id', $request->user_id);
    }
    if ($request->filled('action')) {
        $query->where(function ($q) use ($request) {
            $q->where('event', 'like', "%{$request->action}%")
              ->orWhere('log_name', 'like', "%{$request->action}%")
              ->orWhere('description', 'like', "%{$request->action}%");
        });
    }

    // تحديد عدد العناصر لكل صفحة
    $perPage = $request->input('per_page', 10);
    
    // إذا طلب المستخدم -1، نعطي كل السجلات (بدون pagination)
    if ($perPage == -1) {
        $logs = $query->latest()->get();
        return ActivityLogResource::collection($logs);
    }

    // تأكد من أن per_page عدد صحيح موجب
    $perPage = max(1, (int)$perPage);
    $logs = $query->latest()->paginate($perPage);

    return ActivityLogResource::collection($logs);
}

    public function show($id)
    {
        $log = Activity::with('causer')->findOrFail($id);
        return new ActivityLogResource($log);
    }

    public function destroy($id)
    {
        $log = Activity::findOrFail($id);
        $log->delete();

        return response()->json([
            'message' => 'تم حذف السجل'
        ]);
    }
}