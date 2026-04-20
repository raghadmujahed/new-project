<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\CourseType;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'credit_hours',
        'type',
        'department_id',
    ];

    protected $casts = [
        'type' => CourseType::class,
    ];

    // العلاقات
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Accessor لنوع المساق
    public function getTypeLabelAttribute()
    {
        return $this->type?->label() ?? $this->type;
    }
}