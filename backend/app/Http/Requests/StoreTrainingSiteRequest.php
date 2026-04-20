<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SiteType;
use App\Enums\GoverningBody;
use App\Enums\Directorate;

class StoreTrainingSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role?->name, ['admin', 'education_directorate', 'ministry_of_health']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:training_sites',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'directorate' => 'required|in:وسط,شمال,جنوب,يطا',
            'capacity' => 'required|integer|min:1',
            'site_type' => 'required|in:school,health_center',
            'governing_body' => 'required|in:directorate_of_education,ministry_of_health',
        ];
    }
}