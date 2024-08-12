<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Gestionnaire extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'mot_de_passe',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    public function setMotDePasseAttribute($value)
    {
        $this->attributes['mot_de_passe'] = Hash::make($value);
    }
}
