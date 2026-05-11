<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifestation extends Model {
    protected $primaryKey = 'id_publication';
    public $incrementing = false;
    protected $fillable = [
        'id_publication',
        'nom_manifestation',
        'type_manifestation',
        'date_event',
        'lieu',
    ];

    public function publication() {
        return $this->belongsTo(Publication::class, 'id_publication', 'id');
    }
}
