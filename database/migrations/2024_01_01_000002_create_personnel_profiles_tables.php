<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelProfilesTables extends Migration
{
    public function up()
    {
        // 1. Postulant
        Schema::create('postulants', function (Blueprint $col) {
            $col->unsignedBigInteger('id_personnel')->primary();
            $col->foreign('id_personnel')->references('id')->on('personnels')->onDelete('cascade');
            $col->string('laboratoire')->nullable();
            $col->string('grade_recherche')->nullable();
            $col->timestamps();
        });

        // 2. Examinateur
        Schema::create('examinateurs', function (Blueprint $col) {
            $col->unsignedBigInteger('id_personnel')->primary();
            $col->foreign('id_personnel')->references('id')->on('personnels')->onDelete('cascade');
            $col->string('num_these')->nullable();
            $col->string('directeur_these')->nullable();
            $col->timestamps();
        });

        // 3. President Sous-Commission
        Schema::create('president_sous_commissions', function (Blueprint $col) {
            $col->unsignedBigInteger('id_personnel')->primary();
            $col->foreign('id_personnel')->references('id')->on('personnels')->onDelete('cascade');
            $col->date('date_nomination')->nullable();
            $col->timestamps();
        });

        // 4. Membre Conseil
        Schema::create('membre_conseils', function (Blueprint $col) {
            $col->unsignedBigInteger('id_personnel')->primary();
            $col->foreign('id_personnel')->references('id')->on('personnels')->onDelete('cascade');
            $col->string('specialite')->nullable();
            $col->date('date_entree')->nullable();
            $col->timestamps();
        });

        // 5. President Conseil
        Schema::create('president_conseils', function (Blueprint $col) {
            $col->unsignedBigInteger('id_personnel')->primary();
            $col->foreign('id_personnel')->references('id')->on('personnels')->onDelete('cascade');
            $col->string('mandat')->nullable();
            $col->date('date_debut')->nullable();
            $col->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('president_conseils');
        Schema::dropIfExists('membre_conseils');
        Schema::dropIfExists('president_sous_commissions');
        Schema::dropIfExists('examinateurs');
        Schema::dropIfExists('postulants');
    }
}
