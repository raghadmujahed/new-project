<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // سيتم التحكم عبر Policy لاحقاً
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:draft,submitted,approved,returned',
            'supervisor_notes' => 'nullable|string',
            'student_reflection' => 'nullable|string',
        ];
    }
}