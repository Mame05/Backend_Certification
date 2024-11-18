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
        Schema::create('donneur_externe_structure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donneur_externe_id')->constrained()->onDelete('cascade');
            $table->foreignId('structure_id')->constrained()->onDelete('cascade');
            $table->integer('nombre_dons')->default(0);
            $table->timestamps();
            $table->unique(['donneur_externe_id', 'structure_id']); // Chaque donneur a un seul enregistrement par structure
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donneur_externe_structure');
    }
};
