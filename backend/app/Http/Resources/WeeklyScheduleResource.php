<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\WeeklyScheduleDay;

class WeeklyScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'day_label' => WeeklyScheduleDay::tryFrom($this->day)?->label() ?? $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'training_site' => new TrainingSiteResource($this->whenLoaded('trainingSite')),
            'submitted_by' => new UserResource($this->whenLoaded('submittedBy')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}