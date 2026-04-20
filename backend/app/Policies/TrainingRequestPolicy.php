<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingRequest;

class TrainingRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'coordinator', 'education_directorate', 'school_manager', 'academic_supervisor']);
    }

    public function view(User $user, TrainingRequest $trainingRequest): bool
    {
        if ($user->role?->name === 'admin') return true;
        if ($user->role?->name === 'coordinator') return true;
        if ($user->role?->name === 'education_directorate' && $trainingRequest->book_status === 'sent_to_directorate') return true;
        if ($user->role?->name === 'school_manager' && $trainingRequest->training_site_id === $user->training_site_id) return true;
        return false;
    }

    public function create(User $user): bool
    {
 return in_array($user->role?->name, ['student', 'coordinator']);    }

    public function update(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'coordinator' && in_array($trainingRequest->book_status, ['draft', 'rejected']);
    }

    public function delete(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'admin';
    }

    public function sendToDirectorate(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'coordinator' && $trainingRequest->book_status === 'draft';
    }

    public function approveByDirectorate(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'education_directorate' && $trainingRequest->book_status === 'sent_to_directorate';
    }

    public function sendToSchool(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'education_directorate' && $trainingRequest->book_status === 'directorate_approved';
    }

    public function approveBySchool(User $user, TrainingRequest $trainingRequest): bool
    {
        return $user->role?->name === 'school_manager' && $trainingRequest->book_status === 'sent_to_school';
    }
}