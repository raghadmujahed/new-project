<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRequestStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_request_id', 'user_id', 'course_id', 'start_date', 'end_date',
        'notes', 'rejection_reason', 'assigned_teacher_id', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function assignedTeacher()
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    public function trainingAssignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }
}