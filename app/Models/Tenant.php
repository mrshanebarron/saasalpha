<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected function casts(): array { return ['settings' => 'array', 'is_active' => 'boolean']; }

    public function users() { return $this->hasMany(User::class); }
    public function projects() { return $this->hasMany(Project::class); }
    public function enquiries() { return $this->hasMany(Enquiry::class); }
    public function quotes() { return $this->hasMany(Quote::class); }
    public function complianceDocuments() { return $this->hasMany(ComplianceDocument::class); }
    public function subcontractors() { return $this->hasMany(Subcontractor::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }
    public function cpdRecords() { return $this->hasMany(CpdRecord::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function auditLogs() { return $this->hasMany(AuditLog::class); }
    public function documentTemplates() { return $this->hasMany(DocumentTemplate::class); }
}
