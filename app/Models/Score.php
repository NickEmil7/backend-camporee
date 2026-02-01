<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Models\User;
// use App\Models\Event;
// use App\Models\Club;

class Score extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['juez_id', 'club_id', 'event_id', 'score'];
    
    public function club()
    {
        // withTrashed() permite que el puntaje siga unido al club aunque se desactive
        return $this->belongsTo(Club::class)->withTrashed();
    }

    public function event()
    {
        return $this->belongsTo(Event::class)->withTrashed();
    }

    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_id')->withTrashed();
    }

}

