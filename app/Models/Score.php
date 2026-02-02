<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// --- IMPORTACIONES OBLIGATORIAS ---
use App\Models\Event; 
use App\Models\Club;
use App\Models\User;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'club_id',
        'juez_id',
        'total_score',
        'details',
        'feedback'
    ];

    protected $casts = [
        'details' => 'array',
        'total_score' => 'decimal:2',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class)->withTrashed();
    }

    public function event()
    {
        // Esto fallaba porque no tenías "use App\Models\Event;" arriba
        return $this->belongsTo(Event::class)->withTrashed();
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'juez_id')->withTrashed();
    }
}