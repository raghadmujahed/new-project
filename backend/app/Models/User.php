<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'university_id', 'name', 'email', 'password', 'status',
        'department_id', 'role_id', 'phone', 'major',
        'training_site_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ================= Activity Log =================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ================= العلاقات =================

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function trainingSite()
    {
        return $this->belongsTo(TrainingSite::class, 'training_site_id');
    }

    public function trainingRequests()
    {
        return $this->hasMany(TrainingRequestStudent::class, 'user_id');
    }

    public function assignedTeacherRequests()
    {
        return $this->hasMany(TrainingRequestStudent::class, 'assigned_teacher_id');
    }

    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function evaluationTemplates()
    {
        return $this->hasMany(EvaluationTemplate::class, 'department_id', 'department_id');
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversationsParticipantOne()
    {
        return $this->hasMany(Conversation::class, 'participant_one_id');
    }

    public function conversationsParticipantTwo()
    {
        return $this->hasMany(Conversation::class, 'participant_two_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }

    public function backups()
    {
        return $this->hasMany(Backup::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function approvedAttendances()
    {
        return $this->hasMany(Attendance::class, 'approved_by');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function taskSubmissions()
    {
        return $this->hasMany(TaskSubmission::class, 'user_id');
    }

    public function studentPortfolio()
    {
        return $this->hasOne(StudentPortfolio::class, 'user_id');
    }

    public function supervisorVisits()
    {
        return $this->hasMany(SupervisorVisit::class, 'supervisor_id');
    }

    public function sentOfficialLetters()
    {
        return $this->hasMany(OfficialLetter::class, 'sent_by');
    }

    public function receivedOfficialLetters()
    {
        return $this->hasMany(OfficialLetter::class, 'received_by');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function submittedWeeklySchedules()
    {
        return $this->hasMany(WeeklySchedule::class, 'submitted_by');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // ================= صلاحيات =================

    public function hasPermission($permission)
    {
        return $this->role && $this->role->permissions->contains('name', $permission);
    }

    public function isAdmin()
    {
        return $this->role?->name === 'admin';
    }

    // ================= Features =================

    public function getFeaturesAttribute()
    {
        $flag = \App\Models\FeatureFlag::where('name', 'training_requests.create')->first();

        return [
            'training_requests.create' => $flag ? (int) $flag->is_active : 0,
        ];
    }
}