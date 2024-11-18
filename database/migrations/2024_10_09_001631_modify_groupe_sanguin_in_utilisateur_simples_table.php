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
        Schema::table('utilisateur_simples', function (Blueprint $table) {
            $table->enum('groupe_sanguin', [
                'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-', 'non_connu'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateur_simples', function (Blueprint $table) {
            $table->enum('groupe_sanguin', [
                'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-', 'Je ne connais pas mon groupe sanguin'
            ])->change();
        });
    }
};
