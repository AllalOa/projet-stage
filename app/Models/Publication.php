<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model {
    protected $fillable = ['id_demande', 'titre', 'auteur_principal', 'date_publication', 'resume', 'pdf_path'];

    public function journal() { return $this->hasOne(Journal::class, 'id_publication'); }
    public function manifestation() { return $this->hasOne(Manifestation::class, 'id_publication'); }
}
