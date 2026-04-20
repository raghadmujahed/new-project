<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'content' => $this->content,

            // 🔥 user who created it
            'user' => new UserResource($this->whenLoaded('user')),

            // 🔥 NEW SYSTEM (important)
            'target_type' => $this->target_type,
            'target_ids' => $this->target_ids,

            // optional: readable format for frontend
            'targets_summary' => [
                'type' => $this->target_type,
                'count' => is_array($this->target_ids) ? count($this->target_ids) : 0,
            ],

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}