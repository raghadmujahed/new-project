<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationTemplate extends Model
{
    use HasFactory, SoftDeletes; // إذا أردت soft delete

    protected $fillable = [
        'name',
        'description',
        'form_type',
        'department_id',
    ];

    protected $casts = [
        'form_type' => 'string',
    ];

    // العلاقات
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

public function items()
{
    return $this->hasMany(EvaluationItem::class, 'template_id');
}
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // النطاقات المحلية
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeOfFormType($query, $type)
    {
        return $query->where('form_type', $type);
    }
}