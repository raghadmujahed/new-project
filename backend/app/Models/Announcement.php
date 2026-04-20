<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'target_type',
        'target_ids',
    ];

    protected $casts = [
        'target_ids' => 'array', // 🔥 مهم جدًا عشان JSON يصير array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}