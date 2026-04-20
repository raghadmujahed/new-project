<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\WeeklyScheduleDay;

class IndexWeeklyScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'nullable|exists:users,id',
            'training_site_id' => 'nullable|exists:training_sites,id',
            'day' => 'nullable|in:' . implode(',', array_column(WeeklyScheduleDay::cases(), 'value')),
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}