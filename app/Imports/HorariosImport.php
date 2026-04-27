<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HorariosImport implements ToModel, WithHeadingRow
{
    private array $omitidas = [];

    public function getOmitidas(): array
    {
        return $this->omitidas;
    }

    /**
     * Mapeo flexible de nombres de columna.
     * Permite que el Excel tenga variaciones como "Nombre Curso", "CURSO", "nombre_curso", etc.
     * Agrega más alias aquí si el archivo base cambia de nombre de columna.
     */
    private array $columnAliases = [
        'curso'     => ['curso', 'nombre_curso', 'materia', 'asignatura', 'nombre materia', 'nombre_materia'],
        'docente'   => ['docente', 'profesor', 'nombre_docente', 'nombre_profesor', 'docente_nombre'],
        'dia'       => ['dia', 'día', 'dia_semana', 'día_semana', 'day'],
        'inicio'    => ['inicio', 'hora_inicio', 'hora inicio', 'start', 'hora_start'],
        'fin'       => ['fin', 'hora_fin', 'hora fin', 'end', 'hora_end', 'final'],
        'lugar'     => ['lugar', 'salon', 'salón', 'ubicacion', 'ubicación'],
        'modalidad' => ['modalidad', 'tipo', 'mode', 'tipo_clase'],
        'sede'      => ['sede', 'campus'],
        'bloque'    => ['bloque', 'block', 'edificio'],
        'aula'      => ['aula', 'salon_numero', 'room', 'numero_aula', 'num_aula'],
        'semestre'  => ['semestre', 'periodo', 'semester', 'fecha_inicio', 'vigencia'],
    ];

    /**
     * Resuelve el alias de una columna: busca en el row la clave que corresponde al alias canónico.
     */
    private function resolve(array $row, string $canonico): ?string
    {
        foreach ($this->columnAliases[$canonico] as $alias) {
            // Normalizamos a minúsculas y sin tildes para comparar
            $aliasNorm = $this->normalizar($alias);
            foreach ($row as $key => $value) {
                if ($this->normalizar((string) $key) === $aliasNorm) {
                    return $key;
                }
            }
        }
        return null;
    }

    private function normalizar(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $texto = str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $texto);
        return $texto;
    }

    private function getString(mixed $value): string
    {
        if (is_object($value)) {
            return method_exists($value, '__toString') ? trim((string) $value) : '';
        }
        return trim((string) $value);
    }

    public function model(array $row)
    {
        // ──────────────────────────────────────────────────────────────
        // 1. DETECCIÓN AUTOMÁTICA DE COLUMNAS OBLIGATORIAS
        //    Si no encuentra 'curso', 'docente', 'inicio' o 'dia',
        //    lanza error descriptivo indicando qué columna falta.
        // ──────────────────────────────────────────────────────────────
        $cursoKey    = $this->resolve($row, 'curso');
        $docenteKey  = $this->resolve($row, 'docente');
        $inicioKey   = $this->resolve($row, 'inicio');
        $diaKey      = $this->resolve($row, 'dia');
        $finKey      = $this->resolve($row, 'fin');

        $faltantes = [];
        if (!$cursoKey)   $faltantes[] = 'curso (o "materia", "asignatura")';
        if (!$docenteKey) $faltantes[] = 'docente (o "profesor")';
        if (!$diaKey)     $faltantes[] = 'dia (o "día", "dia_semana")';
        if (!$inicioKey)  $faltantes[] = 'inicio (o "hora_inicio")';

        if (!empty($faltantes)) {
            throw new \Exception(
                "❌ El archivo no tiene el formato correcto. Columnas requeridas no encontradas: " .
                implode(', ', $faltantes) .
                ". Columnas que sí detectamos: " . implode(', ', array_keys($row))
            );
        }

        // ──────────────────────────────────────────────────────────────
        // 2. OBTENER VALORES Y LIMPIAR
        // ──────────────────────────────────────────────────────────────
        $curso        = $this->getString($row[$cursoKey]);
        $docenteNombre = $this->getString($row[$docenteKey]);
        $dia          = ucfirst(strtolower($this->getString($row[$diaKey])));

        // Normalizar nombre del día (tildes)
        $dia = str_replace(['miercoles','sabado'], ['Miércoles','Sábado'], strtolower($dia));
        $dia = ucfirst($dia);

        // Hora inicio
        $inicioCrudo = preg_replace('/[^0-9:]/', '', $this->getString($row[$inicioKey]));
        $iPartes = explode(':', $inicioCrudo);
        $inicio = sprintf('%02d:%02d:%02d', (int)($iPartes[0] ?? 0), (int)($iPartes[1] ?? 0), 0);

        // Hora fin
        $finCrudo = $finKey ? preg_replace('/[^0-9:]/', '', $this->getString($row[$finKey])) : '';
        $fPartes = explode(':', $finCrudo);
        $fin = sprintf('%02d:%02d:%02d', (int)($fPartes[0] ?? 0), (int)($fPartes[1] ?? 0), 0);

        // Fila vacía → ignorar
        if ($inicio === '00:00:00' && $fin === '00:00:00') return null;
        if (empty($curso) || empty($docenteNombre)) return null;

        // Columnas opcionales
        $lugarKey    = $this->resolve($row, 'lugar');
        $modalidadKey = $this->resolve($row, 'modalidad');
        $sedeKey     = $this->resolve($row, 'sede');
        $bloqueKey   = $this->resolve($row, 'bloque');
        $aulaKey     = $this->resolve($row, 'aula');
        $semestreKey = $this->resolve($row, 'semestre');

        $lugar     = $lugarKey    ? $this->getString($row[$lugarKey])    : '';
        $modalidad = $modalidadKey ? ucfirst(strtolower($this->getString($row[$modalidadKey]))) : 'Presencial';
        $sede      = $sedeKey     ? $this->getString($row[$sedeKey])     : '';
        $bloque    = $bloqueKey   ? $this->getString($row[$bloqueKey])   : '';
        $aula      = $aulaKey     ? $this->getString($row[$aulaKey])     : '';

        $semestreRaw = $semestreKey ? $this->getString($row[$semestreKey]) : '';
        $sTime = strtotime(str_replace('/', '-', preg_replace('/[^\d\/\-]/', '', $semestreRaw)));
        $semestre = ($sTime !== false && $sTime > 0) ? date('Y-m-d', $sTime) : date('Y-m-d');

        // ──────────────────────────────────────────────────────────────
        // 3. VALIDAR CRUCE DE HORARIOS (anti-duplicado por docente)
        // ──────────────────────────────────────────────────────────────
        $profesorOcupado = DB::table('horarios_asesoria')
            ->where('docente_nombre', $docenteNombre)
            ->where('dia_semana', $dia)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
            })->exists();

        if ($profesorOcupado) {
            $this->omitidas[] = "Conflicto de horario: \"$curso\" con $docenteNombre el $dia de $inicio a $fin (ya existe un horario que se cruza).";
            return null;
        }

        // ──────────────────────────────────────────────────────────────
        // 4. BUSCAR O CREAR PROFESOR
        //    ✅ Ahora funciona porque 'rol' está en User::$fillable
        // ──────────────────────────────────────────────────────────────
        $profesor = User::where('name', 'LIKE', '%' . $docenteNombre . '%')->first();

        if (!$profesor) {
            $emailBase = strtolower(str_replace([' ', '.', ','], ['.', '', ''], $docenteNombre));
            $profesor = User::create([
                'name'     => $docenteNombre,
                'email'    => $emailBase . '@agendau.com',
                'password' => bcrypt('profesor123'),
                'rol'      => 'profesor',  // ✅ Funciona ahora con fillable corregido
                'activo'   => true,
            ]);
        }

        return new HorarioAsesoria([
            'curso_nombre'   => $curso,
            'docente_nombre' => $docenteNombre,
            'dia_semana'     => $dia,
            'hora_inicio'    => $inicio,
            'hora_fin'       => $fin,
            'lugar'          => $lugar,
            'modalidad'      => $modalidad,
            'sede'           => $sede,
            'bloque'         => $bloque,
            'aula'           => $aula,
            'semestre'       => $semestre,
            'user_id'        => $profesor->id,
        ]);
    }
}