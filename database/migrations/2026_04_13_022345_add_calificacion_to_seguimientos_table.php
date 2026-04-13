<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            // Agregamos la columna para guardar las estrellas (del 1 al 5)
            $table->tinyInteger('calificacion')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropColumn('calificacion');
        });
    }
};