<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\SubmitTaskRequest;
use App\Http\Requests\GradeTaskSubmissionRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Services\TaskService;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Student Tasks
     */
    public function studentIndex(Request $request)
    {
        ActivityLogger::log(
            'task',
            'viewed_list',
            'Viewed student tasks',
            null,
            [],
            $request->user()
        );

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tasks = Task::whereHas('trainingAssignment.enrollment', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->latest()
        ->get();

        return response()->json([
            'data' => $tasks
        ]);
    }

    /**
     * Admin / Teacher index
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'task',
            'viewed_list',
            'Viewed tasks list',
            null,
            [],
            $request->user()
        );

        $query = Task::with([
            'trainingAssignment.enrollment.user',
            'assignedBy'
        ]);

        if ($request->user()->role?->name === 'student') {
            $query->whereHas('trainingAssignment.enrollment', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });

        } elseif ($request->user()->role?->name === 'teacher') {
            $query->where('assigned_by', $request->user()->id);
        }

        $tasks = $query->latest()->paginate($request->per_page ?? 15);

        return TaskResource::collection($tasks);
    }

    /**
     * Create task
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask(
            $request->validated(),
            $request->user()->id
        );

        ActivityLogger::log(
            'task',
            'created',
            'Task created',
            $task,
            ['task_id' => $task->id],
            $request->user()
        );

        return new TaskResource($task);
    }

    /**
     * Show task
     */
    public function show(Task $task)
    {
        ActivityLogger::log(
            'task',
            'viewed',
            'Viewed task',
            $task,
            ['task_id' => $task->id],
            auth()->user()
        );

        return new TaskResource(
            $task->load(['submissions.user', 'trainingAssignment'])
        );
    }

    /**
     * Update task
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = $this->taskService->updateTask($task, $request->validated());

        ActivityLogger::log(
            'task',
            'updated',
            'Task updated',
            $task,
            ['task_id' => $task->id],
            $request->user()
        );

        return new TaskResource($task);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        ActivityLogger::log(
            'task',
            'deleted',
            'Task deleted',
            $task,
            ['task_id' => $task->id],
            auth()->user()
        );

        $task->delete();

        return response()->json([
            'message' => 'تم حذف المهمة'
        ]);
    }

    /**
     * Submit task (student)
     */
    public function submit(SubmitTaskRequest $request, Task $task)
    {
        $submission = $this->taskService->submitTask(
            $task,
            $request->user()->id,
            $request->validated()
        );

        ActivityLogger::log(
            'task',
            'submitted',
            'Task submitted',
            $task,
            [
                'task_id' => $task->id,
                'submission_id' => $submission->id
            ],
            $request->user()
        );

        return response()->json([
            'message' => 'تم تسليم المهمة بنجاح',
            'submission_id' => $submission->id
        ]);
    }

    /**
     * Grade submission
     */
    public function grade(GradeTaskSubmissionRequest $request, TaskSubmission $submission)
    {
        $this->taskService->gradeSubmission(
            $submission,
            $request->grade,
            $request->feedback
        );

        ActivityLogger::log(
            'task_submission',
            'graded',
            'Task submission graded',
            $submission,
            [
                'submission_id' => $submission->id,
                'grade' => $request->grade
            ],
            $request->user()
        );

        return response()->json([
            'message' => 'تم تقييم المهمة بنجاح'
        ]);
    }
}