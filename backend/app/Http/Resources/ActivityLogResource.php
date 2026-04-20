<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => $this->event,
            'log_name' => $this->log_name,
            'description' => $this->description,

            'causer' => $this->causer ? [
                'id' => $this->causer->id,
                'name' => $this->causer->name,
            ] : null,

            'properties' => $this->properties,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}