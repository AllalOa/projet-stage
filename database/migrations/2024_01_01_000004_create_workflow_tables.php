<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowTables extends Migration
{
    public function up()
    {
        // 1. Demande
        Schema::create('demandes', function (Blueprint $col) {
            $col->id(); // id_demande
            $col->date('date_demande');
            $col->string('statut')->default('en_attente'); // reception, recevabilite, proposition, avis, consolidation, transmis, fini
            $col->string('decision_finale')->nullable(); // favorable, reserve, defavorable
            $col->date('date_decision')->nullable();
            $col->foreignId('id_postulant')->constrained('personnels');
            $col->foreignId('id_sous_comm')->nullable()->constrained('sous_commissions');
            $col->timestamps();
        });

        // 2. Publication (Base)
        Schema::create('publications', function (Blueprint $col) {
            $col->id(); // id_publication
            $col->foreignId('id_demande')->constrained('demandes')->onDelete('cascade');
            $col->string('titre');
            $col->string('auteur_principal');
            $col->date('date_publication');
            $col->text('resume')->nullable();
            $col->string('pdf_path')->nullable(); // champ technique pour le stockage
            $col->timestamps();
        });

        // 3. Journal (Spécialisation Publication)
        Schema::create('journals', function (Blueprint $col) {
            $col->unsignedBigInteger('id_publication')->primary();
            $col->foreign('id_publication')->references('id')->on('publications')->onDelete('cascade');
            $col->string('nom_journal');
            $col->string('lien_url')->nullable();
            $col->string('issn')->nullable();
            $col->string('facteur_impact')->nullable();
            $col->timestamps();
        });

        // 4. Manifestation (Spécialisation Publication)
        Schema::create('manifestations', function (Blueprint $col) {
            $col->unsignedBigInteger('id_publication')->primary();
            $col->foreign('id_publication')->references('id')->on('publications')->onDelete('cascade');
            $col->string('nom_manifestation');
            $col->string('type_manifestation')->nullable(); // seminaire, congres...
            $col->date('date_event')->nullable();
            $col->string('lieu')->nullable();
            $col->timestamps();
        });

        // 5. Oral & Poster (Spécialisations Manifestation)
        Schema::create('orals', function (Blueprint $col) {
            $col->unsignedBigInteger('id_manifestation')->primary();
            $col->foreign('id_manifestation')->references('id_publication')->on('manifestations')->onDelete('cascade');
            $col->integer('duree_minutes')->nullable();
            $col->string('langue')->nullable();
            $col->timestamps();
        });

        Schema::create('posters', function (Blueprint $col) {
            $col->unsignedBigInteger('id_manifestation')->primary();
            $col->foreign('id_manifestation')->references('id_publication')->on('manifestations')->onDelete('cascade');
            $col->string('format')->nullable();
            $col->string('dimensions')->nullable();
            $col->timestamps();
        });

        // 6. Avis (Ternaire : Examinateur, Demande, Avis)
        Schema::create('avis', function (Blueprint $col) {
            $col->id(); // id_avis
            $col->foreignId('id_demande')->constrained('demandes')->onDelete('cascade');
            $col->foreignId('id_examinateur')->constrained('personnels');
            $col->string('resultat')->nullable(); // favorable, defavorable...
            $col->text('commentaire')->nullable();
            $col->date('date_avis')->nullable();
            $col->text('recommandation')->nullable();
            $col->integer('confiance')->default(50); // expert, basic...
            $col->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avis');
        Schema::dropIfExists('posters');
        Schema::dropIfExists('orals');
        Schema::dropIfExists('manifestations');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('demandes');
    }
}
