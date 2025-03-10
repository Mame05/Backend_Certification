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
            if (Schema::hasColumn('poche_sanguins', 'donneur_externe_id')) {
                // Modifier la colonne pour qu'elle soit nullable
                $table->unsignedBigInteger('donneur_externe_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poche_sanguins', function (Blueprint $table) {
            if (Schema::hasColumn('poche_sanguins', 'donneur_externe_id')) {
                // Annuler la modification et rendre la colonne non nullable
                $table->unsignedBigInteger('donneur_externe_id')->nullable(false)->change();
            }
        });
    }
};
