<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function workflowSteps()
    {
        return $this->hasMany(WorkflowStep::class, 'role_id');
    }

    public function announcementTargets()
    {
        return $this->hasMany(AnnouncementTarget::class, 'role_id');
    }
}