<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogDetail extends Model
{
    use HasFactory;

    protected $fillable = ['activity_log_id', 'field', 'old_value', 'new_value'];

    public function activityLog()
    {
        return $this->belongsTo(ActivityLog::class);
    }
}