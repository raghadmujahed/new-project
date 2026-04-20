<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'participant_two_id' => 'required|exists:users,id|different:user_id',
            'initial_message' => 'required|string',
        ];
    }
}