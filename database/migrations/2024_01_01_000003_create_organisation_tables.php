<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationTables extends Migration
{
    public function up()
    {
        // 1. Conseil Scientifique
        Schema::create('conseil_scientifiques', function (Blueprint $col) {
            $col->id(); // id_conseil
            $col->string('nom_conseil');
            $col->year('annee_creation')->nullable();
            $col->string('institution')->nullable();
            // Lien vers son président (1,1)
            $col->foreignId('id_president')->nullable()->constrained('personnels');
            $col->timestamps();
        });

        // 2. Sous-Commission
        Schema::create('sous_commissions', function (Blueprint $col) {
            $col->id(); // id_sous_comm
            $col->string('nom');
            $col->date('date_creation')->nullable();
            $col->string('domaine')->nullable();
            $col->foreignId('id_conseil')->constrained('conseil_scientifiques')->onDelete('cascade');
            // Lien vers son président (1,1)
            $col->foreignId('id_president_sc')->nullable()->constrained('personnels');
            $col->timestamps();
        });

        // Association Membre Conseil <-> Conseil (N,M)
        Schema::create('conseil_membre_pivot', function (Blueprint $col) {
            $col->foreignId('id_conseil')->constrained('conseil_scientifiques')->onDelete('cascade');
            $col->foreignId('id_membre')->constrained('personnels')->onDelete('cascade');
            $col->primary(['id_conseil', 'id_membre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conseil_membre_pivot');
        Schema::dropIfExists('sous_commissions');
        Schema::dropIfExists('conseil_scientifiques');
    }
}
