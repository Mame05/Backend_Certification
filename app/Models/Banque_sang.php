<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banque_sang extends Model
{
    use HasFactory;
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
