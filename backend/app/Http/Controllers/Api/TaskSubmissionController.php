<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskSubmissionRequest;
use App\Http\Requests\UpdateTaskSubmissionRequest;
use App\Http\Requests\GradeTaskSubmissionRequest;
use App\Http\Resources\TaskSubmissionResource;
use App\Models\TaskSubmission;
use App\Models\Task;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskSubmissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TaskSubmission::class, 'task_submission');
    }

    /**
     * INDEX
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'task_submission',
            'viewed_list',
            'Viewed task submissions list',
            null,
            [],
            $request->user()
        );

        $query = TaskSubmission::with(['task', 'user']);

        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $submissions = $query->latest()->paginate($request->per_page ?? 15);

        return TaskSubmissionResource::collection($submissions);
    }

    /**
     * STORE (Submit Task)
     */
    public function store(StoreTaskSubmissionRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('task_submissions', 'public');
        }

        $data['submitted_at'] = now();
        $data['user_id'] = $request->user()->id;

        $submission = TaskSubmission::create($data);

        $task = Task::find($data['task_id']);

        if ($task && in_array($task->status, ['pending', 'in_progress'])) {
            $task->update(['status' => 'submitted']);
        }

        ActivityLogger::log(
            'task_submission',
            'submitted',
            'Task submitted',
            $submission,
            [
                'task_id' => $data['task_id'],
                'submission_id' => $submission->id
            ],
            $request->user()
        );

        return new TaskSubmissionResource($submission);
    }

    /**
     * SHOW
     */
    public function show(TaskSubmission $taskSubmission)
    {
        ActivityLogger::log(
            'task_submission',
            'viewed',
            'Viewed task submission',
            $taskSubmission,
            ['submission_id' => $taskSubmission->id],
            auth()->user()
        );

        return new TaskSubmissionResource(
            $taskSubmission->load(['task', 'user'])
        );
    }

    /**
     * UPDATE
     */
    public function update(UpdateTaskSubmissionRequest $request, TaskSubmission $taskSubmission)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($taskSubmission->file_path) {
                Storage::disk('public')->delete($taskSubmission->file_path);
            }
            $data['file_path'] = $request->file('file')->store('task_submissions', 'public');
        }

        $taskSubmission->update($data);

        ActivityLogger::log(
            'task_submission',
            'updated',
            'Task submission updated',
            $taskSubmission,
            ['submission_id' => $taskSubmission->id],
            $request->user()
        );

        return new TaskSubmissionResource($taskSubmission);
    }

    /**
     * GRADE
     */
    public function grade(GradeTaskSubmissionRequest $request, TaskSubmission $taskSubmission)
    {
        $this->authorize('grade', $taskSubmission);

        $taskSubmission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
        ]);

        $taskSubmission->task->update(['status' => 'graded']);

        ActivityLogger::log(
            'task_submission',
            'graded',
            'Task submission graded',
            $taskSubmission,
            [
                'submission_id' => $taskSubmission->id,
                'grade' => $request->grade
            ],
            $request->user()
        );

        return new TaskSubmissionResource($taskSubmission);
    }

    /**
     * DELETE
     */
    public function destroy(TaskSubmission $taskSubmission)
    {
        if ($taskSubmission->file_path) {
            Storage::disk('public')->delete($taskSubmission->file_path);
        }

        ActivityLogger::log(
            'task_submission',
            'deleted',
            'Task submission deleted',
            $taskSubmission,
            ['submission_id' => $taskSubmission->id],
            auth()->user()
        );

        $taskSubmission->delete();

        return response()->json([
            'message' => 'تم حذف التسليم بنجاح'
        ]);
    }
}