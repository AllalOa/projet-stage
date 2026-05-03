<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// 1. SousCommission
class SousCommission extends Model {
    protected $table = 'sous_commissions';
    protected $fillable = ['nom', 'date_creation', 'domaine', 'id_conseil', 'id_president_sc'];

    public function president() { return $this->belongsTo(Personnel::class, 'id_president_sc'); }
    public function demandes() { return $this->hasMany(Demande::class, 'id_sous_comm'); }
}

// 2. Demande
class Demande extends Model {
    protected $fillable = ['date_demande', 'statut', 'decision_finale', 'date_decision', 'id_postulant', 'id_sous_comm'];

    public function postulant() { return $this->belongsTo(Personnel::class, 'id_postulant'); }
    public function sousCommission() { return $this->belongsTo(SousCommission::class, 'id_sous_comm'); }
    public function publication() { return $this->hasOne(Publication::class, 'id_demande'); }
    public function avis() { return $this->hasMany(Avis::class, 'id_demande'); }
}

// 3. Avis
class Avis extends Model {
    protected $table = 'avis';
    protected $fillable = ['id_demande', 'id_examinateur', 'resultat', 'commentaire', 'date_avis', 'recommandation', 'confiance'];

    public function examinateur() { return $this->belongsTo(Personnel::class, 'id_examinateur'); }
    public function demande() { return $this->belongsTo(Demande::class, 'id_demande'); }
}
