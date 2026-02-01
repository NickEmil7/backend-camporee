<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'church', 'location', 'members'];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }
}
