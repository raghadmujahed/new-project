<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_assignment_id',
        'evaluator_id',
        'template_id',
        'total_score',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function trainingAssignment()
    {
        return $this->belongsTo(TrainingAssignment::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function template()
    {
        return $this->belongsTo(EvaluationTemplate::class);
    }

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class);
    }

    public function isApproved()
    {
        return !is_null($this->approved_at);
    }
}