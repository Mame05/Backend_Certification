<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonneurExterne extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function pocheSanguins()
    {
        return $this->hasMany(Poche_sanguin::class);
    }
}
