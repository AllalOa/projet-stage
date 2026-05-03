<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembreConseil extends Model {
    protected $table = 'membre_conseils';
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'specialite', 'date_entree'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
}
