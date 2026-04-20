<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\ApproveAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
        $this->authorizeResource(Attendance::class, 'attendance');
    }

    /**
     * 📌 عرض كل سجلات الحضور
     * + تسجيل دخول الصفحة
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Attendance::with(['user', 'trainingAssignment']);

        // 🔥 Log: دخول صفحة الحضور (بدون model)
        ActivityLogger::log(
            'view',
            null,
            'Visited attendance page',
            [
                'ip' => $request->ip()
            ]
        );

        if ($request->has('training_assignment_id')) {
            $query->where('training_assignment_id', $request->training_assignment_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->latest('date')
            ->paginate($request->per_page ?? 15);

        return AttendanceResource::collection($attendances);
    }

    /**
     * 📌 تسجيل حضور
     */
    public function store(StoreAttendanceRequest $request)
    {
        $attendance = $this->attendanceService->recordAttendance(
            $request->validated(),
            $request->user()->id
        );

        ActivityLogger::log(
            'create',
            $attendance,
            'Attendance recorded',
            [
                'attendance_id' => $attendance->id,
                'status' => $attendance->status
            ]
        );

        return new AttendanceResource($attendance);
    }

    /**
     * 📌 عرض سجل واحد
     */
    public function show(Attendance $attendance)
    {
        ActivityLogger::log(
            'view',
            $attendance,
            'Viewed attendance'
        );

        return new AttendanceResource(
            $attendance->load(['user', 'approvedBy', 'trainingAssignment'])
        );
    }

    /**
     * 📌 الموافقة على الحضور
     */
    public function approve(ApproveAttendanceRequest $request, Attendance $attendance)
    {
        $this->authorize('approve', $attendance);

        $attendance = $this->attendanceService->approveAttendance(
            $attendance,
            $request->user()->id,
            $request->notes
        );

        ActivityLogger::log(
            'approve',
            $attendance,
            'Attendance approved',
            [
                'notes' => $request->notes
            ]
        );

        return new AttendanceResource($attendance);
    }

    /**
     * 📌 ملخص الحضور
     */
    public function summary(Request $request)
    {
        $request->validate([
            'training_assignment_id' => 'required|exists:training_assignments,id'
        ]);

        $summary = $this->attendanceService
            ->getAttendanceSummary($request->training_assignment_id);

        ActivityLogger::log(
            'view',
            null,
            'Viewed attendance summary',
            [
                'training_assignment_id' => $request->training_assignment_id
            ]
        );

        return response()->json($summary);
    }
}