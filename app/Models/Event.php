<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'event_type',
        'evaluation_type',
        'description',
        'max_score',
        'weight',
        'is_active',
        'date',     // Opcional
        'location'  // Opcional
    ];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'event_user');
    }
}