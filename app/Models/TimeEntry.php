<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['date' => 'date', 'hours' => 'decimal:2', 'rate' => 'decimal:2', 'billable' => 'boolean']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function deliverable() { return $this->belongsTo(Deliverable::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getAmountAttribute(): float
    {
        return round($this->hours * ($this->rate ?? 0), 2);
    }
}
