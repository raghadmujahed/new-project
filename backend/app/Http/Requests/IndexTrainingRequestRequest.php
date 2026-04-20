<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\BookStatus;
use App\Enums\TrainingRequestStatus;

class IndexTrainingRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // سيتم التحكم عبر Policy
    }

    public function rules(): array
    {
        return [
            'book_status' => 'nullable|in:' . implode(',', array_column(BookStatus::cases(), 'value')),
            'status' => 'nullable|in:' . implode(',', array_column(TrainingRequestStatus::cases(), 'value')),
            'training_site_id' => 'nullable|exists:training_sites,id',
            'training_period_id' => 'nullable|exists:training_periods,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'book_status.in' => 'حالة الكتاب غير صحيحة.',
            'status.in' => 'حالة الطلب غير صحيحة.',
        ];
    }
}