<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'model_type', 'version', 'is_active', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function steps()
    {
        return $this->hasMany(WorkflowStep::class);
    }

    public function instances()
    {
        return $this->hasMany(WorkflowInstance::class);
    }
}