<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulant extends Model {
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'laboratoire', 'grade_recherche'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
    public function demandes() { return $this->hasMany(Demande::class, 'id_postulant', 'id_personnel'); }
}
