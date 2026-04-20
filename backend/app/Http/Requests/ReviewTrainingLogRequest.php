<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TrainingLogStatus;

class ReviewTrainingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,returned',
            'supervisor_notes' => 'required_if:status,returned|nullable|string',
        ];
    }
}