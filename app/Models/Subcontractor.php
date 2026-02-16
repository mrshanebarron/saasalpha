<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['default_rate' => 'decimal:2']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function complianceDocuments() { return $this->hasMany(ComplianceDocument::class); }

    public function getActiveDocumentsCountAttribute(): int
    {
        return $this->complianceDocuments()->where('status', 'valid')->count();
    }
}
