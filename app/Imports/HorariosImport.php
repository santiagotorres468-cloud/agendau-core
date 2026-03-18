<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HorariosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. VALIDACIÓN ANTI-CHOQUES (Mismo día, misma sede/bloque/aula, horas cruzadas)
        $choqueDeHorario = HorarioAsesoria::where('dia_semana', ucfirst(strtolower($row['dia'])))
            ->where('sede', $row['sede'])
            ->where('bloque', $row['bloque'])
            ->where('aula', $row['aula'])
            ->where(function ($query) use ($row) {
                // Verificamos si la hora se cruza
                $query->whereBetween('hora_inicio', [$row['inicio'], $row['fin']])
                      ->orWhereBetween('hora_fin', [$row['inicio'], $row['fin']]);
            })->exists();

        // Si hay choque y la clase no es virtual, ignoramos esta fila del Excel
        if ($choqueDeHorario && strtolower(trim($row['modalidad'] ?? '')) !== 'virtual') {
            return null; 
        }

        // 2. ASIGNACIÓN DE DOCENTE
        // Buscamos si el docente del Excel ya tiene cuenta en el sistema
        $profesor = User::where('name', 'LIKE', '%' . $row['docente'] . '%')->first();
        $userId = $profesor ? $profesor->id : auth()->id(); // Si no existe, se lo asigna al admin que lo sube

        // 3. GUARDAR EN BD
        return new HorarioAsesoria([
            'curso_nombre'   => $row['curso'],
            'docente_nombre' => $row['docente'],
            'dia_semana'     => ucfirst(strtolower($row['dia'])),
            'hora_inicio'    => $row['inicio'],
            'hora_fin'       => $row['fin'],
            'lugar'          => $row['lugar'],
            'modalidad'      => $row['modalidad'] ?? 'Presencial',
            'sede'           => $row['sede'],
            'bloque'         => $row['bloque'],
            'aula'           => $row['aula'],
            'user_id'        => $userId,
        ]);
    }
}