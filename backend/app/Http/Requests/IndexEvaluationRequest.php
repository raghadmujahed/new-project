<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\EvaluationFormType;

class IndexEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_assignment_id' => 'nullable|exists:training_assignments,id',
            'template_id' => 'nullable|exists:evaluation_templates,id',
            'form_type' => 'nullable|in:' . implode(',', array_column(EvaluationFormType::cases(), 'value')),
            'evaluator_id' => 'nullable|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}