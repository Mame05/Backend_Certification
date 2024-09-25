<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification1::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(Rendez_vous::class);
    }
}
