<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOfficialLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:sent_to_directorate,sent_to_school,completed',
        ];
    }
}