<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluationRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->evaluation);
    }

    public function rules()
    {
        return [
            'total_score' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}