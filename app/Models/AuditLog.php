<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AuditLog extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id', 
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    // Convertimos automáticamente los JSON a Array
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed(); // withTrashed para ver usuarios eliminados
    }

    public function prunable()
    {
        // Puedes cambiar subYear() por subMonths(6) si prefieres 6 meses
        return static::where('created_at', '<=', now()->subYear());
    }
}