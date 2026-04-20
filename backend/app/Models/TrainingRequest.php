<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_status', 'sent_to_directorate_at', 'directorate_approved_at',
        'sent_to_school_at', 'school_approved_at', 'training_site_id',
        'status', 'requested_at' ,     'rejection_reason', 'letter_number',
    'letter_date',  'training_period_id', 

    ];

    protected $casts = [
        'sent_to_directorate_at' => 'datetime',
        'directorate_approved_at' => 'datetime',
        'sent_to_school_at' => 'datetime',
        'school_approved_at' => 'datetime',
        'requested_at' => 'datetime',
            'rejection_reason'=> 'string',  
    'letter_date'=> 'datetime',
    ];

    public function trainingSite()
    {
        return $this->belongsTo(TrainingSite::class);
    }

    public function trainingRequestStudents()
    {
        return $this->hasMany(TrainingRequestStudent::class);
    }

    public function officialLetters()
    {
        return $this->hasMany(OfficialLetter::class);
    }
}