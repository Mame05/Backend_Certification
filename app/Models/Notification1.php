<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification1 extends Model
{
    use HasFactory;

    protected $table = 'notification1s'; // Spécifiez la table à utiliser

    protected $guarded=[];
    
    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
