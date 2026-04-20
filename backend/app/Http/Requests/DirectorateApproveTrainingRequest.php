<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DirectorateApproveTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role?->name === 'education_directorate';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ];
    }
}