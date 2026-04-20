<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'attachment_path' => $this->attachment_path,
            'is_read' => (bool) $this->is_read,
            'read_at' => $this->read_at?->toDateTimeString(),
            'conversation' => new ConversationResource($this->whenLoaded('conversation')),
            'sender' => new UserResource($this->whenLoaded('sender')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}