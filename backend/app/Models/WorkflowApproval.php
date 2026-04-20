<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_instance_id', 'workflow_step_id', 'status', 'approved_by', 'approved_at', 'comments'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function instance()
    {
        return $this->belongsTo(WorkflowInstance::class);
    }

    public function step()
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}