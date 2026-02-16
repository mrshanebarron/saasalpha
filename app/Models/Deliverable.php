<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['due_date' => 'date', 'delivered_date' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray', 'in_progress' => 'blue', 'review' => 'yellow',
            'approved' => 'green', 'delivered' => 'indigo', default => 'gray',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['approved', 'delivered']);
    }
}
