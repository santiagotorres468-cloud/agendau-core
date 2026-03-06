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
        // 1. SANITIZACIÓN Y NORMALIZACIÓN DE DATOS
        // Convertimos a minúsculas, quitamos espacios extra y ponemos la primera letra en mayúscula
        // Ej: " virtual " -> "Virtual"
        $modalidad = isset($row['modalidad']) ? ucfirst(strtolower(trim($row['modalidad']))) : 'Presencial';

        // 2. LÓGICA DE NEGOCIO INTELIGENTE (Historia de Usuario 06)
        if ($modalidad === 'Virtual') {
            // Si es virtual, forzamos estos campos a N/A sin importar qué diga el Excel
            $sede = 'N/A';
            $bloque = 'N/A';
            $aula = 'N/A';
            $lugarFinal = 'Virtual (Enlace por definir)';
        } else {
            // Si es presencial, limpiamos los datos o ponemos valores por defecto seguros
            $sede = isset($row['sede']) ? trim($row['sede']) : 'Por asignar';
            $bloque = isset($row['bloque']) ? trim($row['bloque']) : '-';
            $aula = isset($row['aula']) ? trim($row['aula']) : '-';
            
            // Reconstruimos el campo "lugar" original concatenando los nuevos datos
            // Así no rompemos las vistas antiguas que usaban solo "lugar"
            $lugarFinal = $sede . ' - Bloque ' . $bloque . ' - Aula ' . $aula; 
        }

        // 3. INSERCIÓN O ACTUALIZACIÓN EN LA BASE DE DATOS
        return HorarioAsesoria::updateOrCreate(
            [
                // Llaves de búsqueda (Si esto coincide, actualiza. Si no, crea uno nuevo)
                'curso_nombre' => $row['curso'], 
                'dia_semana'   => $row['dia'], 
                'hora_inicio'  => $this->formatearHora($row['inicio'])
            ],
            [
                'docente_nombre' => $row['docente'],
                'hora_fin'       => $this->formatearHora($row['fin']),
                'lugar'          => $row['lugar'] ?? $lugarFinal, // Prioriza lo que venga en excel, si no, usa el construido
                'semestre'       => $row['semestre'] ?? '2026-1', 
                
                // NUEVOS CAMPOS GUARDADOS CORRECTAMENTE
                'modalidad'      => $modalidad, 
                'sede'           => $sede,
                'bloque'         => $bloque,
                'aula'           => $aula,
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