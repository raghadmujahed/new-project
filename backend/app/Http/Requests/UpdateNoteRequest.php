<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('note')->user_id;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
        ];
    }
}