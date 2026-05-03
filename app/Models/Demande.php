<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model {
    protected $fillable = ['date_demande', 'statut', 'decision_finale', 'date_decision', 'id_postulant', 'id_sous_comm'];

    public function postulant() { return $this->belongsTo(Personnel::class, 'id_postulant'); }
    public function sousCommission() { return $this->belongsTo(SousCommission::class, 'id_sous_comm'); }
    public function publication() { return $this->hasOne(Publication::class, 'id_demande'); }
    public function avis() { return $this->hasMany(Avis::class, 'id_demande'); }
}
