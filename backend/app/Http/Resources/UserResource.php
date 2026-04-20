<?php

namespace App\Http\Resources;

use App\Models\FeatureFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'university_id'    => $this->university_id,
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'status'           => $this->status,
            'status_label'     => $this->status_label,
            'role_id'          => $this->role_id,
            'role'             => new RoleResource($this->whenLoaded('role')),
            'department_id'    => $this->department_id,
            'department'       => new DepartmentResource($this->whenLoaded('department')),
            'major'            => $this->major,                  // التخصص (للطالب) أو المادة (للمعلم)
            'training_site_id' => $this->training_site_id,
            'training_site'    => new TrainingSiteResource($this->whenLoaded('trainingSite')),
            'created_at'       => $this->created_at?->toDateTimeString(),
            'updated_at'       => $this->updated_at?->toDateTimeString(),
            'deleted_at'       => $this->deleted_at?->toDateTimeString(),
            'features'         => $this->getDynamicFeatures(),
        ];
    }

    protected function getDynamicFeatures(): array
    {
        $features = [];
        if ($this->role?->name === 'student') {
            $flag = FeatureFlag::where('name', 'training_requests.create')->first();
            $features['training_requests.create'] = $flag && $flag->is_open ? 1 : 0;
        } else {
            $features['training_requests.create'] = 0;
        }
        return $features;
    }
}