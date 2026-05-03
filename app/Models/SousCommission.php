<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SousCommission extends Model {
    protected $table = 'sous_commissions';
    protected $fillable = ['nom', 'date_creation', 'domaine', 'id_conseil', 'id_president_sc'];

    public function president() { return $this->belongsTo(Personnel::class, 'id_president_sc'); }
    public function demandes() { return $this->hasMany(Demande::class, 'id_sous_comm'); }
}
