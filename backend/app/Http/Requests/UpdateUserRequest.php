<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->role?->name === 'admin' || $user->id == $this->route('user');
    }

    public function rules(): array
    {
        return [
            'name'             => 'sometimes|string|max:255',
            'email'            => 'sometimes|email|unique:users,email,' . $this->route('user'),
            'phone'            => 'nullable|string|max:20',
            'department_id'    => 'nullable|exists:departments,id',
            'training_site_id' => 'nullable|exists:training_sites,id',
            'major'            => 'nullable|string|max:255',
            'status'           => 'sometimes|in:active,inactive,suspended',
            'university_id'    => 'sometimes|string|max:255|unique:users,university_id,' . $this->route('user'),
        ];
    }
}