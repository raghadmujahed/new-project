<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'name', 'file_path', 'size', 'status', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}