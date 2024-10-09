<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donneur_externe extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function pocheSanguins()
    {
        return $this->hasMany(Poche_sanguin::class);
    }
}
