<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresidentSousCommission extends Model {
    protected $table = 'president_sous_commissions';
    protected $primaryKey = 'id_personnel';
    public $incrementing = false;
    protected $fillable = ['id_personnel', 'date_nomination'];

    public function personnel() { return $this->belongsTo(Personnel::class, 'id_personnel'); }
}
