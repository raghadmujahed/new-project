<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],

            // 🔥 new system
            'target_type' => ['sometimes', 'in:all,role,user,department'],

            'target_ids' => ['nullable', 'array'],
            'target_ids.*' => ['integer'],
        ];
    }
}