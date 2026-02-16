<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    protected $guarded = [];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function template() { return $this->belongsTo(DocumentTemplate::class, 'template_id'); }
    public function project() { return $this->belongsTo(Project::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }
}
