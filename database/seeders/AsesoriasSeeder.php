<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estudiante;
use App\Models\HorarioAsesoria;
use App\Models\Seguimiento;

class AsesoriasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Estudiantes de prueba
        $estudiante1 = Estudiante::create([
            'cedula' => '1001001000',
            'nombre_completo' => 'Ana María Pérez',
            'programa_academico' => 'Ingeniería de Software',
            'esta_activo' => true,
        ]);

        $estudiante2 = Estudiante::create([
            'cedula' => '2002002000',
            'nombre_completo' => 'Carlos Andrés López',
            'programa_academico' => 'Tecnología en Desarrollo de Software',
            'esta_activo' => true,
        ]);

        // 2. Crear Horarios de Asesoría de prueba
        $horario1 = HorarioAsesoria::create([
            'curso_nombre' => 'Lógica de Programación',
            'docente_nombre' => 'Prof. Juan David',
            'dia_semana' => 'Lunes',
            'hora_inicio' => '14:00:00',
            'hora_fin' => '16:00:00',
            'lugar' => 'Bloque 4 - Aula 201',
            'semestre' => '2026-1',
        ]);

        $horario2 = HorarioAsesoria::create([
            'curso_nombre' => 'Bases de Datos Relacionales',
            'docente_nombre' => 'Prof. Laura Gómez',
            'dia_semana' => 'Miércoles',
            'hora_inicio' => '10:00:00',
            'hora_fin' => '12:00:00',
            'lugar' => 'Bloque 5 - Sala de Sistemas 3',
            'semestre' => '2026-1',
        ]);

        // 3. Crear Seguimientos (Asistencias de prueba)
        Seguimiento::create([
            'estudiante_id' => $estudiante1->id,
            'horario_id' => $horario1->id,
            'fecha' => '2026-02-23',
            'hora_registro' => '14:05:00',
            'estado' => 'Asistió',
        ]);

        Seguimiento::create([
            'estudiante_id' => $estudiante2->id,
            'horario_id' => $horario2->id,
            'fecha' => '2026-02-25',
            'hora_registro' => '10:15:00',
            'estado' => 'Asistió',
        ]);
    }
}
