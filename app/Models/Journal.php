<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model {
    protected $primaryKey = 'id_publication';
    public $incrementing = false;
    protected $fillable = ['id_publication', 'nom_journal', 'lien_url', 'issn', 'facteur_impact'];
}
