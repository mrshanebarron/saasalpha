<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpdRecord extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['hours' => 'decimal:2', 'completed_date' => 'date', 'expiry_date' => 'date', 'verified' => 'boolean']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'course' => 'Course', 'seminar' => 'Seminar', 'conference' => 'Conference',
            'self_study' => 'Self Study', 'mentoring' => 'Mentoring', 'publication' => 'Publication',
            default => ucfirst($this->category),
        };
    }
}
