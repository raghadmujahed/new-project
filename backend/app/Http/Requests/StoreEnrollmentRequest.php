<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Semester;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'coordinator']);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year' => 'required|digits:4',
            'semester' => 'required|in:first,second,summer',
            'status' => 'sometimes|in:active,dropped,completed',
        ];
    }
}