<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Club;
use App\Models\Event;

class Score extends Model
{
    use HasFactory;

    protected $fillable = ['juez_id', 'club_id', 'evento_id', 'score'];

    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function evento()
    {
        return $this->belongsTo(Event::class);
    }
}

