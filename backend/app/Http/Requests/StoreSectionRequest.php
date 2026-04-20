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

    public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'course_id' => 'required|exists:courses,id',
        'semester' => 'required|in:first,second',
        'academic_year' => 'required|string|max:20',
        'academic_supervisor_id' => 'nullable|exists:users,id', // تغيير إلى nullable
    ];
}
}