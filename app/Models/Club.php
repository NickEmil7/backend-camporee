<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 
        'code',         // <--- Asegúrate que esté aquí
        'description', 
        'is_active'     // <--- Asegúrate que esté aquí
    ];
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }
}
