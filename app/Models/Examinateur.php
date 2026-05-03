<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examinateur extends Model {
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'num_these', 'directeur_these'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
    public function avis() { return $this->hasMany(Avis::class, 'id_examinateur', 'id_personnel'); }
}
