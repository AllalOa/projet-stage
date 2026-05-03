<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Personnel extends Authenticatable
{
    use Notifiable;

    protected $table = 'personnels';

    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'phone', 'grade', 'departement', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relations vers les profils spécifiques (CTI)
    public function postulant() {
        return $this->hasOne(Postulant::class, 'id_personnel');
    }

    public function examinateur() {
        return $this->hasOne(Examinateur::class, 'id_personnel');
    }

    public function presidentSub() {
        return $this->hasOne(PresidentSousCommission::class, 'id_personnel');
    }

    public function membreConseil() {
        return $this->hasOne(MembreConseil::class, 'id_personnel');
    }

    public function presidentCouncil() {
        return $this->hasOne(PresidentConseil::class, 'id_personnel');
    }
}
