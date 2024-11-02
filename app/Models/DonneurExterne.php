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
    public function structures()
{
    return $this->belongsToMany(Structure::class, 'donneur_externe_structure')
                ->withPivot('nombre_dons')
                ->withTimestamps();
}

}
