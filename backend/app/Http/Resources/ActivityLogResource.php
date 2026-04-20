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
            'action' => $this->action,
            'description' => $this->description,
            'ip_address' => $this->ip_address,
            'method' => $this->method,
            'route' => $this->route,
            'user_agent' => $this->user_agent,
            'old_data' => $this->old_data,
            'new_data' => $this->new_data,
            'user' => new UserResource($this->whenLoaded('user')),
            'details' => ActivityLogDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}