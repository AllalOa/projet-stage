<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelsTable extends Migration
{
    public function up()
    {
        Schema::create('personnels', function (Blueprint $col) {
            $col->id(); // id_personnel (PK)
            $col->string('nom');
            $col->string('prenom');
            $col->string('email')->unique();
            $col->string('password');
            $col->string('phone')->nullable();
            $col->string('grade')->nullable();
            $col->string('departement')->nullable();
            $col->enum('role', ['admin', 'postulant', 'examiner', 'president-sub', 'president-council'])->default('postulant');
            $col->rememberToken();
            $col->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnels');
    }
}
