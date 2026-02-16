<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['old_values' => 'array', 'new_values' => 'array']; }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function log(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): self
    {
        return self::create([
            'tenant_id' => $model->tenant_id ?? auth()->user()?->tenant_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
