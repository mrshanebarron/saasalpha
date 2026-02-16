<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['estimated_value' => 'decimal:2', 'deadline' => 'date']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function quotes() { return $this->hasMany(Quote::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'blue', 'reviewing' => 'yellow', 'qualified' => 'indigo',
            'converted' => 'green', 'declined' => 'red', default => 'gray',
        };
    }
}
