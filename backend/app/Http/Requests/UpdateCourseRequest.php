<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'department_head']);
    }

    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:255|unique:courses,code,' . $this->route('course'),
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'sometimes|integer|min:1|max:6',
            'type' => 'sometimes|in:practical,theoretical,both',
        ];
    }
}