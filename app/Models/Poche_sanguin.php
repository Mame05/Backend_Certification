<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poche_sanguin extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function banquesang()
    {
        return $this->belongsTo(Banque_Sang::class);
    }
    public function rendezVous()
    {
        return $this->belongsTo(Rendez_vous::class);
    }
    public function donneur_externe()
    {
        return $this->belongsTo(DonneurExterne::class);
    }
}
