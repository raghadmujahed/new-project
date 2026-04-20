<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\WeeklyScheduleDay;

class StoreWeeklyScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:users,id',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'training_site_id' => 'required|exists:training_sites,id',
        ];
    }
}