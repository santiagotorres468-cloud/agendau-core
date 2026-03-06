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
        Schema::table('horarios_asesoria', function (Blueprint $table) {
            // Agregamos las columnas y las hacemos "nullable" (opcionales) 
            // por si la clase es "Virtual" y no necesita Bloque ni Aula
            $table->string('modalidad')->nullable()->after('lugar'); 
            $table->string('sede')->nullable()->after('modalidad'); // Cata o Pascual
            $table->string('bloque')->nullable()->after('sede');
            $table->string('aula')->nullable()->after('bloque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios_asesoria', function (Blueprint $table) {
            // Este método borra las columnas si decidimos deshacer el cambio
            $table->dropColumn(['modalidad', 'sede', 'bloque', 'aula']);
        });
    }
};
