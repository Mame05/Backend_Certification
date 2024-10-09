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
            // Restaurer les colonnes supprimées uniquement si elles n'existent pas
            if (!Schema::hasColumn('poche_sanguins', 'nom_donneur')) {
                $table->string('nom_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'prenom_donneur')) {
                $table->string('prenom_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'telephone_donneur')) {
                $table->string('telephone_donneur')->unique();
            }
            if (!Schema::hasColumn('poche_sanguins', 'adresse_donneur')) {
                $table->string('adresse_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'sexe_donneur')) {
                $table->string('sexe_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'date_naiss_donneur')) {
                $table->date('date_naiss_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'numero_identite_national_donneur')) {
                $table->string('numero_identite_national_donneur')->unique();
            }
            if (!Schema::hasColumn('poche_sanguins', 'profession_donneur')) {
                $table->string('profession_donneur');
            }

            // Ajout de la clé étrangère pour `section_id` si elle n'existe pas déjà
            if (!Schema::hasColumn('poche_sanguins', 'section_id')) {
                $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            }

            // Supprimer les clés étrangères ajoutées
            if (Schema::hasColumn('poche_sanguins', 'banque_sang_id')) {
                $table->dropForeign(['banque_sang_id']);
                $table->dropColumn('banque_sang_id');
            }
            
            if (Schema::hasColumn('poche_sanguins', 'rendez_vous_id')) {
                $table->dropForeign(['rendez_vous_id']);
                $table->dropColumn('rendez_vous_id');
            }
            
            if (Schema::hasColumn('poche_sanguins', 'donneur_externe_id')) {
                $table->dropForeign(['donneur_externe_id']);
                $table->dropColumn('donneur_externe_id');
            }
        });
    }
        
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poche_sanguins', function (Blueprint $table) {
            // Restaurer les colonnes supprimées uniquement si elles n'existent pas
            if (!Schema::hasColumn('poche_sanguins', 'nom_donneur')) {
                $table->string('nom_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'prenom_donneur')) {
                $table->string('prenom_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'telephone_donneur')) {
                $table->string('telephone_donneur')->unique();
            }
            if (!Schema::hasColumn('poche_sanguins', 'adresse_donneur')) {
                $table->string('adresse_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'sexe_donneur')) {
                $table->string('sexe_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'date_naiss_donneur')) {
                $table->date('date_naiss_donneur');
            }
            if (!Schema::hasColumn('poche_sanguins', 'numero_identite_national_donneur')) {
                $table->string('numero_identite_national_donneur')->unique();
            }
            if (!Schema::hasColumn('poche_sanguins', 'profession_donneur')) {
                $table->string('profession_donneur');
            }


            // Supprimer les clés étrangères ajoutées
            $table->dropForeign(['banque_sang_id']);
            $table->dropColumn('banque_sang_id');
            
            $table->dropForeign(['rendez_vous_id']);
            $table->dropColumn('rendez_vous_id');
            
            $table->dropForeign(['donneur_externe_id']);
            $table->dropColumn('donneur_externe_id');
        });
    }
};

