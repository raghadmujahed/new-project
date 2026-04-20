<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_assignment_id', 'log_date', 'start_time', 'end_time',
        'activities_performed', 'supervisor_notes', 'student_reflection', 'status'
    ];

    protected $casts = [
        'log_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function trainingAssignment()
    {
        return $this->belongsTo(TrainingAssignment::class);
    }
}