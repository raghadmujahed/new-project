<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'is_open', 'description'];

    protected $casts = [
        'is_open' => 'boolean',
    ];
}