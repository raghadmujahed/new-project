<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        if ($user->id === $task->assigned_by) return true;
        if ($user->id === $task->trainingAssignment->teacher_id) return true;
        if ($user->id === $task->trainingAssignment->academic_supervisor_id) return true;
        if ($user->id === $task->trainingAssignment->enrollment->user_id) return true;
        return $user->role?->name === 'admin';
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['academic_supervisor', 'teacher']);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_by;
    }
}