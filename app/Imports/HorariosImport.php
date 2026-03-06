<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class HorariosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return HorarioAsesoria::updateOrCreate(
            [
                'curso_nombre' => $row['curso'], 
                'dia_semana'   => $row['dia'], 
                'hora_inicio'  => $this->formatearHora($row['inicio'])
            ],
            [
                'docente_nombre' => $row['docente'],
                'hora_fin'       => $this->formatearHora($row['fin']),
                'lugar'          => $row['lugar'],
                // Agregamos el semestre por defecto si no viene en el Excel
                'semestre'       => $row['semestre'] ?? '2026-1', 
            ]
        );
    }
    
    private function formatearHora($valorHora)
    {
        if (empty($valorHora)) {
            return '00:00:00';
        }

        if (is_numeric($valorHora)) {
            return Date::excelToDateTimeObject($valorHora)->format('H:i:s');
        }
        
        try {
            return Carbon::parse($valorHora)->format('H:i:s');
        } catch (\Exception $e) {
            return '00:00:00';
        }
    }
}