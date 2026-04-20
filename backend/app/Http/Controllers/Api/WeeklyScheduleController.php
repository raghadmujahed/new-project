<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWeeklyScheduleRequest;
use App\Http\Resources\WeeklyScheduleResource;
use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class WeeklyScheduleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(WeeklySchedule::class, 'weekly_schedule');
    }

    public function index(Request $request)
    {
        $query = WeeklySchedule::with(['teacher', 'trainingSite', 'submittedBy']);

        if ($request->has('teacher_id')) $query->where('teacher_id', $request->teacher_id);
        if ($request->has('training_site_id')) $query->where('training_site_id', $request->training_site_id);
        if ($request->has('day')) $query->where('day', $request->day);

        ActivityLogger::log(
            'weekly_schedule',
            'viewed_list',
            'عرض الجداول الأسبوعية',
            null,
            [
                'filters' => $request->all(),
                'ip' => $request->ip()
            ],
            $request->user()
        );

        $schedules = $query->paginate($request->per_page ?? 15);

        return WeeklyScheduleResource::collection($schedules);
    }

    public function store(StoreWeeklyScheduleRequest $request)
    {
        $data = $request->validated();
        $data['submitted_by'] = $request->user()->id;

        $schedule = WeeklySchedule::create($data);

        ActivityLogger::log(
            'weekly_schedule',
            'created',
            'تم إنشاء جدول أسبوعي',
            $schedule,
            $data,
            $request->user()
        );

        return new WeeklyScheduleResource($schedule);
    }

    public function show(WeeklySchedule $weeklySchedule)
    {
        ActivityLogger::log(
            'weekly_schedule',
            'viewed',
            'عرض جدول أسبوعي',
            $weeklySchedule,
            [],
            auth()->user()
        );

        return new WeeklyScheduleResource(
            $weeklySchedule->load(['teacher', 'trainingSite', 'submittedBy'])
        );
    }

    public function destroy(WeeklySchedule $weeklySchedule)
    {
        ActivityLogger::log(
            'weekly_schedule',
            'deleted',
            'تم حذف الجدول الأسبوعي',
            $weeklySchedule,
            [
                'teacher_id' => $weeklySchedule->teacher_id,
                'training_site_id' => $weeklySchedule->training_site_id
            ],
            auth()->user()
        );

        $weeklySchedule->delete();

        return response()->json(['message' => 'تم حذف الجدول']);
    }
}