<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', EvaluationTemplate::class);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_type' => 'required|in:evaluation,student_form',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}