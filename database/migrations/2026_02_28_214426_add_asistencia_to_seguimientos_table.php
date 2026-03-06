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
    Schema::table('seguimientos', function (Blueprint $table) {
        // Agregamos asistencia como booleano (0 o 1) que empieza en nulo
        $table->boolean('asistencia')->nullable()->after('estado');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            //
        });
    }
};
