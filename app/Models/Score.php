<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'club_id',
        'juez_id', // <--- CAMBIO CRÍTICO: Antes decía user_id
        'score',
        'details',
        'feedback'
    ];

    protected $casts = [
        'details' => 'array', 
        'score' => 'decimal:2'
    ];

    public function event() { return $this->belongsTo(Event::class); }
    public function club() { return $this->belongsTo(Club::class); }
    
    // Relación con el Juez (Usuario)
    // Le decimos a Laravel explícitamente que use la columna 'juez_id'
    public function juez() { 
        return $this->belongsTo(User::class, 'juez_id'); 
    }
}