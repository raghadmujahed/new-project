<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'coordinator']);
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'academic_year' => 'sometimes|digits:4|integer|min:2000|max:2100',
            'academic_supervisor_id' => 'sometimes|exists:users,id',
            'semester' => 'sometimes|in:first,second,summer',
            'course_id' => 'sometimes|exists:courses,id',
        ];
    }
}