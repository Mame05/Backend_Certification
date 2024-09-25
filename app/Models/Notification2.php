<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification2 extends Model
{
    use HasFactory;
    public function rendezVous()
    {
        return $this->belongsTo(Rendez_vous::class);
    }
}
