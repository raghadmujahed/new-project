<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'template_id' => $this->template_id,
            'title' => $this->title,
            'field_type' => $this->field_type,
            'options' => $this->options,
            'is_required' => (bool) $this->is_required,
            'max_score' => $this->max_score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}