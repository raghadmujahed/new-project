<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPortfolio extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'training_assignment_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainingAssignment()
    {
        return $this->belongsTo(TrainingAssignment::class);
    }

    public function entries()
    {
        return $this->hasMany(PortfolioEntry::class);
    }
}