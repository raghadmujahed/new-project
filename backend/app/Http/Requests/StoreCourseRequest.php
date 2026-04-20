<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'department_head']);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255|unique:courses',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:1|max:6',
            'type' => 'required|in:practical,theoretical,both',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}