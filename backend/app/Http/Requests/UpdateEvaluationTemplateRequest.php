<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluationTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->evaluation_template);
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'form_type' => 'sometimes|in:evaluation,student_form',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}