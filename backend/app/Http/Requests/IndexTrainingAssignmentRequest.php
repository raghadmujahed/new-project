<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TrainingAssignmentStatus;

class IndexTrainingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:' . implode(',', array_column(TrainingAssignmentStatus::cases(), 'value')),
            'user_id' => 'nullable|exists:users,id',
            'training_site_id' => 'nullable|exists:training_sites,id',
            'academic_supervisor_id' => 'nullable|exists:users,id',
            'teacher_id' => 'nullable|exists:users,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}