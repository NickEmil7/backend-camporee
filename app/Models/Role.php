<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- Asegúrate de tener esto

class Role extends Model
{
    use HasFactory, SoftDeletes; // <--- Y esto

    protected $fillable = [
        'name',
        'description', // Agregamos description al fillable
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
