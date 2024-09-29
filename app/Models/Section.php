<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    public function banqueSang()
    {
        return $this->belongsTo(Banque_sang::class);
    }

    public function pocheSanguins()
    {
        return $this->hasMany(Poche_sanguin::class);
    }
}
