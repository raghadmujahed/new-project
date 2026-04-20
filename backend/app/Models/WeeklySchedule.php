<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id', 'day', 'start_time', 'end_time', 'training_site_id'];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
            'submitted_by',   // <-- أضف هذا السطر

    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function trainingSite()
    {
        return $this->belongsTo(TrainingSite::class);
    }
    public function submittedBy()
{
    return $this->belongsTo(User::class, 'submitted_by');
}
}