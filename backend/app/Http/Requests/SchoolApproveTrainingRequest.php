<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolApproveTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role?->name === 'school_manager';
    }

    public function rules(): array
    {
        return [
            'students' => 'required|array',
            'students.*.id' => 'required|exists:training_request_students,id',
            'students.*.assigned_teacher_id' => 'required|exists:users,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ];
    }
}