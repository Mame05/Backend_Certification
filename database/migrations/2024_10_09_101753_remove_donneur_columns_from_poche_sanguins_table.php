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
             // Supprimer la contrainte de clé étrangère
            $table->dropForeign('poche_sanguins_section_id_foreign');
            $table->dropColumn('section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poche_sanguins', function (Blueprint $table) {
             // Ajouter la colonne section_id
             $table->unsignedBigInteger('section_id')->nullable();

             // Rétablir la clé étrangère
             $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }
};
