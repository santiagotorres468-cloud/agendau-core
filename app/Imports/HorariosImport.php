<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HorariosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. VALIDACIÓN ESTRICTA DEL ARCHIVO
        // Si el Excel no tiene estas columnas exactas, bloquea la subida por completo
        if (!isset($row['curso']) || !isset($row['docente']) || !isset($row['inicio']) || !isset($row['dia'])) {
            throw new \Exception("El archivo no tiene el formato correcto. Asegúrate de usar la plantilla oficial con las columnas: curso, docente, dia, inicio, fin.");
        }

        $getString = function($value) {
            if (is_object($value)) { return method_exists($value, '__toString') ? (string) $value : ''; }
            return trim((string) $value);
        };

        // Purificamos las horas
        $inicioCrudo = preg_replace('/[^0-9:]/', '', $getString($row['inicio']));
        $finCrudo = preg_replace('/[^0-9:]/', '', $getString($row['fin'] ?? ''));

        $iPartes = explode(':', $inicioCrudo);
        $fPartes = explode(':', $finCrudo);
        
        $inicio = sprintf('%02d:%02d:%02d', (int)($iPartes[0] ?? 0), (int)($iPartes[1] ?? 0), (int)($iPartes[2] ?? 0));
        $fin = sprintf('%02d:%02d:%02d', (int)($fPartes[0] ?? 0), (int)($fPartes[1] ?? 0), (int)($fPartes[2] ?? 0));

        if ($inicio === '00:00:00' && $fin === '00:00:00') { return null; }

        // Fecha y Textos
        $semestreRaw = $getString($row['semestre'] ?? '');
        $sTime = strtotime(str_replace('/', '-', preg_replace('/[^\d\/\-]/', '', $semestreRaw)));
        $semestre = ($sTime !== false && $sTime > 0) ? date('Y-m-d', $sTime) : date('Y-m-d');

        $dia = ucfirst(strtolower($getString($row['dia'])));
        $sede = $getString($row['sede'] ?? '');
        $bloque = $getString($row['bloque'] ?? '');
        $aula = $getString($row['aula'] ?? '');
        $modalidad = ucfirst(strtolower($getString($row['modalidad'] ?? 'Presencial')));
        $curso = $getString($row['curso']);
        $docenteNombre = $getString($row['docente']);

        // =======================================================
        // 2. REGLA ESTRICTA DE PROFESORES Y HORARIOS
        // =======================================================
        // Valida si el DOCENTE ya tiene una clase asignada ESE DÍA a ESA MISMA HORA.
        // Las horas (< y >) comprueban si los tiempos se cruzan en algún punto.
        $profesorOcupado = DB::table('horarios_asesoria')
            ->where('docente_nombre', $docenteNombre)
            ->where('dia_semana', $dia)
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('hora_inicio', '<', $fin)
                      ->where('hora_fin', '>', $inicio);
            })->exists();

        if ($profesorOcupado) {
            // Si el profesor ya da clase a esa hora (virtual o presencial), IGNORA esta fila
            return null; 
        }

        // =======================================================
        // 3. ASIGNAR O CREAR PROFESOR Y GUARDAR
        // =======================================================
        $profesor = User::where('name', 'LIKE', '%' . $docenteNombre . '%')->first();
        if (!$profesor) {
            $profesor = User::create([
                'name' => $docenteNombre,
                'email' => strtolower(str_replace(' ', '.', $docenteNombre)) . '@agendau.com',
                'password' => bcrypt('profesor123'),
                'rol' => 'profesor'
            ]);
        }

        return new HorarioAsesoria([
            'curso_nombre'   => $curso,
            'docente_nombre' => $docenteNombre,
            'dia_semana'     => $dia,
            'hora_inicio'    => $inicio,
            'hora_fin'       => $fin,
            'lugar'          => $getString($row['lugar'] ?? ''),
            'modalidad'      => $modalidad,
            'sede'           => $sede,
            'bloque'         => $bloque,
            'aula'           => $aula,
            'semestre'       => $semestre,
            'user_id'        => $profesor->id,
        ]);
    }
}