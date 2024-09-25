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
        Schema::create('banque_sangs', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->integer('stock_actuelle');
            $table->date('date_mise_a_jour');
            $table->foreignId('structure_id')->constrained('structures')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banque_sangs');
    }
};
