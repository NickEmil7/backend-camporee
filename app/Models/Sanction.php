<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = ['juez_id', 'club_id', 'points_deducted', 'description'];

    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}

