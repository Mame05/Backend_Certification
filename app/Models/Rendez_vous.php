<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendez_vous extends Model
{
    use HasFactory;
    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function utilisateurSimple()
    {
        return $this->belongsTo(UtilisateurSimple::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification2::class);
    }
}
