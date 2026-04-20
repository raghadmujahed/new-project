<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequestStudent extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role?->name === 'coordinator';
    }

    public function rules(): array
    {
        return [
            'training_request_id' => 'required|exists:training_requests,id',
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ];
    }
}