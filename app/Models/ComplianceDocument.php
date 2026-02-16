<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceDocument extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['issue_date' => 'date', 'expiry_date' => 'date', 'is_critical' => 'boolean']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function holder() { return $this->belongsTo(User::class, 'holder_id'); }
    public function subcontractor() { return $this->belongsTo(Subcontractor::class); }

    public function getIsExpiredAttribute(): bool { return $this->expiry_date && $this->expiry_date->isPast(); }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && !$this->expiry_date->isPast() && $this->expiry_date->diffInDays(now()) <= $this->reminder_days;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        return $this->expiry_date ? (int)now()->diffInDays($this->expiry_date, false) : null;
    }

    public function getComputedStatusAttribute(): string
    {
        if ($this->is_expired) return 'expired';
        if ($this->is_expiring_soon) return 'expiring_soon';
        return 'valid';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->computed_status) {
            'expired' => 'red', 'expiring_soon' => 'yellow', 'valid' => 'green', default => 'gray',
        };
    }
}
