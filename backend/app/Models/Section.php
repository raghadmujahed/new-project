<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'academic_year', 'academic_supervisor_id', 'semester', 'course_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSupervisor()
    {
        return $this->belongsTo(User::class, 'academic_supervisor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}