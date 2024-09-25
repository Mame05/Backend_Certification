<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('poche_sanguins', function (Blueprint $table) {
            $table->id();
            $table->string('numero_poche');
            $table->string('groupe_sanguin');
            $table->string('nom_donneur');
            $table->string('prenom_donneur');
            $table->string('telephone_donneur')->unique();
            $table->string('adresse_donneur');
            $table->string('sexe_donneur');
            $table->date('date_naiss_donneur');
            $table->string('numero_identite_national_donneur')->unique();
            $table->string('profession_donneur');
            $table->date('date_prelevement');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poche_sanguins');
    }
};
