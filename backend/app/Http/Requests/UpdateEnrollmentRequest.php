<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'coordinator']);
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:active,dropped,completed',
            'final_grade' => 'nullable|numeric|min:0|max:100',
        ];
    }
}