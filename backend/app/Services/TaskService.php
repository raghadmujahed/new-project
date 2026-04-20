<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function createTask(array $data, int $assignedBy): Task
    {
        $data['assigned_by'] = $assignedBy;
        $data['status'] = TaskStatus::PENDING->value;
        return Task::create($data);
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function submitTask(Task $task, int $userId, array $submissionData): TaskSubmission
    {
        // رفع الملف إذا وُجد
        $filePath = null;
        if (isset($submissionData['file']) && $submissionData['file']->isValid()) {
            $filePath = $submissionData['file']->store('task_submissions', 'public');
        }

        $submission = TaskSubmission::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'file_path' => $filePath,
            'notes' => $submissionData['notes'] ?? null,
            'submitted_at' => now(),
        ]);

        // تحديث حالة المهمة إلى submitted إذا لم تكن مقيمة
        if ($task->status !== TaskStatus::GRADED->value) {
            $task->update(['status' => TaskStatus::SUBMITTED->value]);
        }

        return $submission;
    }

    public function gradeSubmission(TaskSubmission $submission, float $grade, ?string $feedback = null): TaskSubmission
    {
        $submission->update([
            'grade' => $grade,
            'feedback' => $feedback,
        ]);

        // تحديث حالة المهمة إلى graded
        $submission->task->update(['status' => TaskStatus::GRADED->value]);

        return $submission;
    }
}