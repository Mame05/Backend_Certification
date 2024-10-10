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
            $table->foreignId('banque_sang_id')->constrained('banque_sangs')->onDelete('cascade');
            $table->foreignId('rendez_vouse_id')->constrained('rendez_vouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poche_sanguins', function (Blueprint $table) {
            $table->dropForeign(['banque_sang_id']);
            $table->dropColumn('banque_sang_id');
            $table->dropForeign(['rendez_vouse_id']);
            $table->dropColumn('rendez_vouse_id');
        });
    }
};
