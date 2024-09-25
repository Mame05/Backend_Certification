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
        Schema::create('utilisateur_simples', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->unique();
            $table->string('adresse');
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naiss');
            $table->string('photo')->nullable();
            $table->string('profession');
            $table->enum('groupe_sanguin',['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-', 'Je ne connais pas mon groupe sanguin']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur_simples');
    }
};
