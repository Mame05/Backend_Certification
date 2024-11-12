<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UtilisateurSimple extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(Rendez_vous::class);
    }

    public function getDonCountAttribute()
    {
        return $this->rendezVous()->where('etat', true)->count();
    }

    public function getGamificationLevelAttribute()
    {
        $donCount = $this->don_count;

        if ($donCount === 0) {
            return 'Nouveau'; // Niveau pour les nouveaux inscrits
        } elseif($donCount >= 20) {
            return 'Platine';
        } elseif ($donCount >= 10) {
            return 'Or';
        } elseif ($donCount >= 5) {
            return 'Argent';
        } elseif ($donCount >= 1) {
            return 'Bronze';
        } else {
            return 'Aucun niveau';
        }
    }

    public function getBadgeAttribute()
{
    switch ($this->gamification_level) {
        case 'Nouveau':
            return 'badge_nouveau.png';
        case 'Platine':
            return 'badge_platine.png';
        case 'Or':
            return 'badge_or.png';
        case 'Argent':
            return 'badge_argent.png';
        case 'Bronze':
            return 'badge_bronze.png';
        default:
            return 'badge_aucun.png';
    }
}


}
