<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'response_text' => $this->response_text,
            'file_path' => $this->file_path,
            'evaluation' => new EvaluationResource($this->whenLoaded('evaluation')),
            'item' => new EvaluationItemResource($this->whenLoaded('item')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}