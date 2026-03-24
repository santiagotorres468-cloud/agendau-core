<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HorariosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // =======================================================
        // 1. PURIFICACIÓN DE DATOS (El Exorcismo de Excel)
        // =======================================================
        
        // Función rápida para traducir la hora, ya sea decimal o texto
        $parseTime = function($value) {
            if (empty($value)) return '00:00:00';
            
            // Si Excel nos manda su número decimal (ej. 0.333333)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('H:i:s');
            }
            
            // Si viene como texto normal (ej. "08:00")
            $timeRaw = preg_replace('/[^\d:]/', '', $value);
            $partes = explode(':', $timeRaw);
            return sprintf('%02d:%02d:%02d', (int)($partes[0] ?? 0), (int)($partes[1] ?? 0), (int)($partes[2] ?? 0));
        };

        $inicio = $parseTime($row['inicio'] ?? null);
        $fin = $parseTime($row['fin'] ?? null);

        // Si por alguna razón la fila no tiene hora, la ignoramos
        if ($inicio === '00:00:00' && $fin === '00:00:00') {
            return null;
        }

        // Limpiamos y aseguramos la fecha del Semestre
        $semestreRaw = $row['semestre'] ?? '';
        $semestreLimpio = preg_replace('/[^\d\/\-]/', '', $semestreRaw);
        $semestreLimpio = str_replace('/', '-', $semestreLimpio);
        
        $sTime = strtotime($semestreLimpio);
        $semestre = ($sTime !== false && $sTime > 0) ? date('Y-m-d', $sTime) : date('Y-m-d');

        // Limpiamos los textos básicos que se habían quedado por fuera en el mensaje anterior
        $dia = ucfirst(strtolower(trim($row['dia'] ?? '')));
        $sede = trim($row['sede'] ?? '');
        $bloque = trim($row['bloque'] ?? '');
        $aula = trim($row['aula'] ?? '');
        $modalidad = strtolower(trim($row['modalidad'] ?? 'presencial'));
        $curso = trim($row['curso'] ?? '');
        $docenteNombre = trim($row['docente'] ?? 'Sin Asignar');

        // =======================================================
        // 2. VALIDACIÓN ANTI-CHOQUES
        // =======================================================
        $choqueDeHorario = HorarioAsesoria::where('dia_semana', $dia)
            ->where('sede', $sede)
            ->where('bloque', $bloque)
            ->where('aula', $aula)
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('hora_inicio', [$inicio, $fin])
                      ->orWhereBetween('hora_fin', [$inicio, $fin]);
            })->exists();

        // Si hay choque y la clase no es virtual, ignoramos esta fila del Excel
        if ($choqueDeHorario && $modalidad !== 'virtual') {
            return null; 
        }

        // =======================================================
        // 3. ASIGNACIÓN DE DOCENTE
        // =======================================================
        $profesor = User::where('name', 'LIKE', '%' . $docenteNombre . '%')->first();
        
        // Si el profesor no existe en el sistema, se lo creamos automáticamente
        if (!$profesor) {
            $profesor = User::create([
                'name' => $docenteNombre,
                'email' => strtolower(str_replace(' ', '.', $docenteNombre)) . '@agendau.com',
                'password' => bcrypt('profesor123'),
                'rol' => 'profesor'
            ]);
        }
        $userId = $profesor->id;

        // =======================================================
        // 4. GUARDAR EN BD
        // =======================================================
        return new HorarioAsesoria([
            'curso_nombre'   => $curso,
            'docente_nombre' => $docenteNombre,
            'dia_semana'     => $dia,
            'hora_inicio'    => $inicio,
            'hora_fin'       => $fin,
            'lugar'          => trim($row['lugar'] ?? ''),
            'modalidad'      => ucfirst($modalidad),
            'sede'           => $sede,
            'bloque'         => $bloque,
            'aula'           => $aula,
            'semestre'       => $semestre,
            'user_id'        => $userId,
        ]);
    }
}