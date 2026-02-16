<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['variables' => 'array', 'is_active' => 'boolean']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function generatedDocuments() { return $this->hasMany(GeneratedDocument::class, 'template_id'); }
}
