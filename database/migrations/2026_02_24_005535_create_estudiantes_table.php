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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            
            // Aquí van los campos personalizados para tu sistema:
            $table->string('cedula')->unique(); // Unique asegura que no haya dos estudiantes con la misma cédula
            $table->string('nombre_completo')->nullable(); // Nullable permite dejarlo vacío temporalmente si solo tienes las cédulas al principio
            $table->string('programa_academico')->nullable();
            $table->boolean('esta_activo')->default(true); // Para saber si el estudiante está matriculado
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
