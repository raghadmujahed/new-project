<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],

            // 🔥 new system
            'target_type' => ['required', 'in:all,role,user,department'],

            'target_ids' => ['nullable', 'array'],

            // كل عنصر داخل المصفوفة رقم
            'target_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}