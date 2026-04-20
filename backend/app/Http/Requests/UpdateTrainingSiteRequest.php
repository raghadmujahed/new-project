<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'education_directorate', 'ministry_of_health']);
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|unique:training_sites,name,' . $this->route('training_site'),
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'capacity' => 'sometimes|integer|min:1',
        ];
    }
}