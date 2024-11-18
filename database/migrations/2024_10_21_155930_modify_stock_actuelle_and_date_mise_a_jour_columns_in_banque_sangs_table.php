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
        Schema::table('banque_sangs', function (Blueprint $table) {
            // Modifier la colonne stock_actuelle pour qu'elle soit nullable et de type integer
            $table->integer('stock_actuelle')->nullable()->change();
            
            // Modifier la colonne date_mise_a_jour pour qu'elle soit nullable et de type date
            $table->date('date_mise_a_jour')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banque_sangs', function (Blueprint $table) {
            // Restaurer les colonnes à leurs types précédents si nécessaire
            $table->integer('stock_actuelle')->change();
            $table->date('date_mise_a_jour')->change();
        });
    }
};
