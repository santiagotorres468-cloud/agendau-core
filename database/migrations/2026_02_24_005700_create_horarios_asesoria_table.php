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
        Schema::create('horarios_asesoria', function (Blueprint $table) {
            $table->id();
            
            // Datos del curso y docente
            $table->string('curso_nombre');
            $table->string('docente_nombre');
            
            // Datos de tiempo y espacio
            $table->string('dia_semana'); // Ej: Lunes, Martes...
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('lugar'); // Ej: Bloque 4 - Aula 201
            
            // Control académico
            $table->string('semestre'); // Ej: 2026-1
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_asesoria');
    }
};
