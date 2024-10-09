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
            $table->dropColumn('section_id');
            
            // Ajout de la clé étrangère vers la table banque_sangs
            $table->foreignId('banque_sang_id')->constrained('banque_sangs')->onDelete('cascade');
            
            // Ajout de la clé étrangère vers la table rendez_vouses
            $table->foreignId('rendez_vous_id')->constrained('rendez_vouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       //
    
    }
};
