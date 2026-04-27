<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seguimiento_id')->constrained('seguimientos')->onDelete('cascade');
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horarios_asesoria')->onDelete('cascade');

            $table->tinyInteger('p1_claridad')->unsigned()->default(0);
            $table->tinyInteger('p2_puntualidad')->unsigned()->default(0);
            $table->tinyInteger('p3_dominio_tema')->unsigned()->default(0);
            $table->tinyInteger('p4_utilidad')->unsigned()->default(0);
            $table->tinyInteger('p5_ambiente')->unsigned()->default(0);

            $table->text('resumen_sesion')->nullable();
            $table->text('aspectos_mejorar')->nullable();
            $table->text('comentario')->nullable();

            $table->decimal('promedio', 3, 2)->default(0);

            $table->timestamps();
            $table->unique('seguimiento_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};