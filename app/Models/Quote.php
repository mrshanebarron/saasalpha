<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $guarded = [];
    protected function casts(): array {
        return [
            'subtotal' => 'decimal:2', 'tax_rate' => 'decimal:2', 'tax_amount' => 'decimal:2',
            'total' => 'decimal:2', 'valid_until' => 'date', 'sent_at' => 'date', 'accepted_at' => 'date',
        ];
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function enquiry() { return $this->belongsTo(Enquiry::class); }
    public function lineItems() { return $this->hasMany(QuoteLineItem::class); }
    public function preparedBy() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function project() { return $this->hasOne(Project::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray', 'sent' => 'blue', 'accepted' => 'green',
            'rejected' => 'red', 'expired' => 'yellow', default => 'gray',
        };
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->lineItems()->sum('amount');
        $this->tax_amount = round($this->subtotal * ($this->tax_rate / 100), 2);
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }
}
