<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTrainingRequestToSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role?->name === 'education_directorate';
    }

    public function rules(): array
    {
        return [
            'letter_number' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'content' => 'required|string',
        ];
    }
}