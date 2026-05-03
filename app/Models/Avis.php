<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model {
    protected $table = 'avis';
    protected $fillable = ['id_demande', 'id_examinateur', 'resultat', 'commentaire', 'date_avis', 'recommandation', 'confiance'];

    public function examinateur() { return $this->belongsTo(Personnel::class, 'id_examinateur'); }
    public function demande() { return $this->belongsTo(Demande::class, 'id_demande'); }
}
