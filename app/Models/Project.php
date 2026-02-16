<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];
    protected function casts(): array {
        return [
            'budget' => 'decimal:2', 'spent' => 'decimal:2', 'tags' => 'array',
            'start_date' => 'date', 'target_date' => 'date', 'completed_date' => 'date',
        ];
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function quote() { return $this->belongsTo(Quote::class); }
    public function manager() { return $this->belongsTo(User::class, 'project_manager_id'); }
    public function members() { return $this->hasMany(ProjectMember::class); }
    public function deliverables() { return $this->hasMany(Deliverable::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }
    public function generatedDocuments() { return $this->hasMany(GeneratedDocument::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green', 'on_hold' => 'yellow', 'completed' => 'blue',
            'cancelled' => 'red', default => 'gray',
        };
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->timeEntries()->sum('hours');
    }

    public function getBudgetUsedPercentAttribute(): int
    {
        if (!$this->budget || $this->budget == 0) return 0;
        return min(100, (int)(($this->spent / $this->budget) * 100));
    }
}
