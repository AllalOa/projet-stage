<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// 1. Postulant
class Postulant extends Model {
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'laboratoire', 'grade_recherche'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
    public function demandes() { return $this->hasMany(Demande::class, 'id_postulant', 'id_personnel'); }
}

// 2. Examinateur
class Examinateur extends Model {
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'num_these', 'directeur_these'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
    public function avis() { return $this->hasMany(Avis::class, 'id_examinateur', 'id_personnel'); }
}

// 3. PresidentSousCommission
class PresidentSousCommission extends Model {
    protected $table = 'president_sous_commissions';
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'date_nomination'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
}

// 4. MembreConseil
class MembreConseil extends Model {
    protected $table = 'membre_conseils';
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'specialite', 'date_entree'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
}
