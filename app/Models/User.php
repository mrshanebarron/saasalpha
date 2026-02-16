<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'role', 'job_title', 'phone', 'avatar', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }
    public function cpdRecords() { return $this->hasMany(CpdRecord::class); }
    public function managedProjects() { return $this->hasMany(Project::class, 'project_manager_id'); }
    public function projectMemberships() { return $this->hasMany(ProjectMember::class); }
    public function notifications() { return $this->hasMany(Notification::class); }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isManager() { return in_array($this->role, ['admin', 'manager']); }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))->map(fn($n) => strtoupper($n[0]))->implode('');
    }
}
