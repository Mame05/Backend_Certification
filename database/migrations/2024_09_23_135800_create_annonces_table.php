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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type_annonce', ['besoin_urgence', 'collecte']);
            $table->string('nom_lieu');
            $table->string('adresse_lieu');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_debut');
            $table->time('heure_fin')->nullable();
            $table->enum('groupe_sanguin_requis', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-', 'Tous les groupes']);
            $table->integer('nombre_poches_vise')->nullable(); // Nombre de poches de sang cible
            $table->text('description')->nullable();
            $table->string('contact_responsable');
            $table->foreignId('structure_id')->constrained('structures')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
