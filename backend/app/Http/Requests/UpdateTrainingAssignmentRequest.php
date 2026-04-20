<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TrainingAssignmentStatus;

class UpdateTrainingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:assigned,ongoing,completed',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'teacher_id' => 'nullable|exists:users,id',
            'academic_supervisor_id' => 'nullable|exists:users,id',
        ];
    }
}