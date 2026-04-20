<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Evaluation::class);
    }

    public function rules()
    {
        return [
            'training_assignment_id' => 'required|exists:training_assignments,id',
            'evaluator_id' => 'required|exists:users,id',
            'template_id' => 'required|exists:evaluation_templates,id',
            'total_score' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}