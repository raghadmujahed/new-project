<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Semester;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'coordinator']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'academic_year' => 'required|digits:4|integer|min:2000|max:2100',
            'academic_supervisor_id' => 'required|exists:users,id',
            'semester' => 'required|in:first,second,summer',
            'course_id' => 'required|exists:courses,id',
        ];
    }
}