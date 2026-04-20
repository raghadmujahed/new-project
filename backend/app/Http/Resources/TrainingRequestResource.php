<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\TrainingRequestStatus;
use App\Enums\BookStatus;

class TrainingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'letter_number' => $this->letter_number,
            'letter_date' => $this->letter_date?->toDateString(),
            'book_status' => $this->book_status,
            'book_status_label' => BookStatus::tryFrom($this->book_status)?->label() ?? $this->book_status,
            'status' => $this->status,
            'status_label' => TrainingRequestStatus::tryFrom($this->status)?->label() ?? $this->status,
            'sent_to_directorate_at' => $this->sent_to_directorate_at?->toDateTimeString(),
            'directorate_approved_at' => $this->directorate_approved_at?->toDateTimeString(),
            'sent_to_school_at' => $this->sent_to_school_at?->toDateTimeString(),
            'school_approved_at' => $this->school_approved_at?->toDateTimeString(),
            'requested_at' => $this->requested_at?->toDateTimeString(),
            'rejection_reason' => $this->rejection_reason,
            'training_site' => new TrainingSiteResource($this->whenLoaded('trainingSite')),
            'students' => TrainingRequestStudentResource::collection($this->whenLoaded('trainingRequestStudents')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'training_period' => new TrainingPeriodResource($this->whenLoaded('trainingPeriod')),
        ];
    }
}