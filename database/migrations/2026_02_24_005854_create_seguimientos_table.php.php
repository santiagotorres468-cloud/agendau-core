<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
public function up(): void
{
    Schema::create('seguimientos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
        $table->foreignId('horario_id')->constrained('horarios_asesoria')->onDelete('cascade');
        $table->date('fecha');
        $table->time('hora_registro');
        $table->string('estado')->default('Asistió'); // Puede ser: Asistió, Cancelada, etc.
        $table->timestamps();
    });
}

   
    public function down(): void
    {
        //
    }
};
