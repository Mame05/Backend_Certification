<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Structure extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=[];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function banqueSangs()
    {
        return $this->hasMany(Banque_sang::class);
    }
    public function donneursExternes()
{
    return $this->belongsToMany(DonneurExterne::class, 'donneur_externe_structure')
                ->withPivot('nombre_dons')
                ->withTimestamps();
}

}
