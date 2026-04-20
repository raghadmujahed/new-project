<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingLogRequest;
use App\Http\Requests\UpdateTrainingLogRequest;
use App\Http\Requests\ReviewTrainingLogRequest;
use App\Http\Resources\TrainingLogResource;
use App\Models\TrainingLog;
use App\Services\TrainingLogService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class TrainingLogController extends Controller
{
    protected $trainingLogService;

    public function __construct(TrainingLogService $trainingLogService)
    {
        $this->trainingLogService = $trainingLogService;
    }

    // ================= INDEX =================
    public function index(Request $request)
    {
        $user = $request->user();

        ActivityLogger::log(
            'training_log',
            'viewed_list',
            'Viewed training logs page',
            null,
            [],
            $user
        );

        $query = TrainingLog::query();

        if ($user->role?->name === 'student') {
            $query->join('training_assignments', 'training_logs.training_assignment_id', '=', 'training_assignments.id')
                ->join('enrollments', 'training_assignments.enrollment_id', '=', 'enrollments.id')
                ->where('enrollments.user_id', $user->id)
                ->select('training_logs.*');
        }

        $logs = $query->latest('log_date')
            ->paginate($request->per_page ?? 15);

        return TrainingLogResource::collection($logs);
    }

    // ================= STORE =================
    public function store(StoreTrainingLogRequest $request)
    {
        $user = $request->user();

        $assignment = optional($user->enrollment)->trainingAssignment;

        if (!$assignment) {
            ActivityLogger::log(
                'training_log',
                'failed',
                'Failed to create training log (no assignment)',
                null,
                [],
                $user
            );

            return response()->json([
                'message' => 'لا يمكنك إضافة سجل بدون تدريب'
            ], 400);
        }

        $data = $request->validated();
        $data['training_assignment_id'] = $assignment->id;

        $log = $this->trainingLogService->createLog($data, $user->id);

        ActivityLogger::log(
            'training_log',
            'created',
            'Created training log',
            $log,
            [
                'training_log_id' => $log->id,
                'training_assignment_id' => $assignment->id,
            ],
            $user
        );

        return new TrainingLogResource($log);
    }

    // ================= SHOW =================
    public function show(TrainingLog $trainingLog)
    {
        ActivityLogger::log(
            'training_log',
            'viewed',
            'Viewed training log',
            $trainingLog,
            ['training_log_id' => $trainingLog->id],
            auth()->user()
        );

        return new TrainingLogResource(
            $trainingLog->load(['trainingAssignment'])
        );
    }

    // ================= UPDATE =================
    public function update(UpdateTrainingLogRequest $request, TrainingLog $trainingLog)
    {
        $trainingLog->update($request->validated());

        ActivityLogger::log(
            'training_log',
            'updated',
            'Updated training log',
            $trainingLog,
            ['training_log_id' => $trainingLog->id],
            auth()->user()
        );

        return new TrainingLogResource($trainingLog);
    }

    // ================= SUBMIT =================
    public function submit(TrainingLog $trainingLog)
    {
        $log = $this->trainingLogService->submitLog($trainingLog);

        ActivityLogger::log(
            'training_log',
            'submitted',
            'Submitted training log',
            $log,
            ['training_log_id' => $trainingLog->id],
            auth()->user()
        );

        return new TrainingLogResource($log);
    }

    // ================= REVIEW =================
    public function review(ReviewTrainingLogRequest $request, TrainingLog $trainingLog)
    {
        $log = $this->trainingLogService->reviewLog(
            $trainingLog,
            $request->status,
            $request->supervisor_notes
        );

        ActivityLogger::log(
            'training_log',
            'reviewed',
            'Reviewed training log',
            $log,
            [
                'training_log_id' => $trainingLog->id,
                'status' => $request->status,
            ],
            auth()->user()
        );

        return new TrainingLogResource($log);
    }

    // ================= DELETE =================
    public function destroy(TrainingLog $trainingLog)
    {
        $id = $trainingLog->id;

        $trainingLog->delete();

        ActivityLogger::log(
            'training_log',
            'deleted',
            'Deleted training log',
            null,
            ['training_log_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف السجل']);
    }

    // ================= EXTRA METHOD =================
    public function getTrainingLogs(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        ActivityLogger::log(
            'training_log',
            'viewed_list',
            'Viewed training logs (custom endpoint)',
            null,
            [],
            $user
        );

        $logs = TrainingLog::join('training_assignments', 'training_logs.training_assignment_id', '=', 'training_assignments.id')
            ->join('enrollments', 'training_assignments.enrollment_id', '=', 'enrollments.id')
            ->where('enrollments.user_id', $user->id)
            ->select('training_logs.*')
            ->orderBy('training_logs.log_date', 'desc')
            ->get();

        return response()->json($logs);
    }
}