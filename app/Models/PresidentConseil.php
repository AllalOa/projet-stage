<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresidentConseil extends Model {
    protected $table = 'president_conseils';
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'mandat', 'date_debut'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
}
