<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteLineItem extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'rate' => 'decimal:2', 'amount' => 'decimal:2']; }

    public function quote() { return $this->belongsTo(Quote::class); }
}
