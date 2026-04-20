<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingAssignment;

class TrainingAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // كل مستخدم يرى ما يخصه
    }

    public function view(User $user, TrainingAssignment $assignment): bool
    {
        if ($user->role?->name === 'admin') return true;
        if ($user->id === $assignment->teacher_id) return true;
        if ($user->id === $assignment->academic_supervisor_id) return true;
        if ($user->id === $assignment->coordinator_id) return true;
        if ($user->id === $assignment->enrollment->user_id) return true;
        return false;
    }

    public function update(User $user, TrainingAssignment $assignment): bool
    {
        return in_array($user->role?->name, ['admin', 'coordinator', 'academic_supervisor']);
    }
}