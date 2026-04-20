<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeTaskSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grade' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded',
        ];
    }
}