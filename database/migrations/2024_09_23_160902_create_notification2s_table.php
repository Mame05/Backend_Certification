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
        Schema::create('notification2s', function (Blueprint $table) {
            $table->id();
            $table->text('contenu'); // Le contenu de la notification
            $table->date('date_envoie'); // Date d'envoi de la notification
            $table->time('heure_envoie'); // Heure d'envoi de la notification
            $table->enum('type', ['SMS', 'Email'])->default('SMS'); // Spécifie que c'est une notification SMS par défaut
            $table->foreignId('rendez_vouse_id')->constrained('rendez_vouses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification2s');
    }
};
