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
        Schema::table('poche_sanguins', function (Blueprint $table) {
            $table->dropColumn([
                'nom_donneur',
                'prenom_donneur',
                'telephone_donneur',
                'adresse_donneur',
                'sexe_donneur',
                'date_naiss_donneur',
                'numero_identite_national_donneur',
                'profession_donneur',
            ]);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poche_sanguins', function (Blueprint $table) {
            $table->string('nom_donneur');
            $table->string('prenom_donneur');
            $table->string('telephone_donneur');
            $table->string('adresse_donneur');
            $table->string('sexe_donneur');
            $table->date('date_naiss_donneur');
            $table->string('numero_identite_national_donneur');
            $table->string('profession_donneur');
        });
    }
};
