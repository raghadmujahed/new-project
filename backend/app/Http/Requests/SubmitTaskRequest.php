<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_path' => 'required|file|mimes:pdf,doc,docx,zip|max:10240',
            'notes' => 'nullable|string',
        ];
    }
}