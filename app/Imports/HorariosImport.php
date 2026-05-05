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
    private int   $insertados = 0;

    public function getOmitidas(): array
    {
        return $this->omitidas;
    }

    public function getInsertados(): int
    {
        return $this->insertados;
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
        'correo'    => ['correo', 'email', 'correo_electronico', 'correo electronico', 'mail'],
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

    private function detectarSemestre(): string
    {
        $mes = (int) date('n');
        $ano = (int) date('Y');

        if ($mes >= 1 && $mes <= 6) {
            return "{$ano}-01-01";   // Primer semestre: enero – 1 junio
        }
        if ($mes >= 8 && $mes <= 11) {
            return "{$ano}-08-05";   // Segundo semestre: 5 agosto – 30 noviembre
        }
        if ($mes === 12) {
            return ($ano + 1) . '-01-01';   // Diciembre → apunta al primer semestre siguiente
        }
        return "{$ano}-08-05";   // Julio intersemestral → segundo semestre entrante
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

    private function getTimeString(mixed $value): string
    {
        // Excel almacena horas como fracción del día (0.75 = 18:00, 0.333... = 08:00)
        if (is_float($value) && $value >= 0 && $value < 1) {
            $mins = (int) round($value * 1440);
            return sprintf('%02d:%02d', intdiv($mins, 60), $mins % 60);
        }
        return $this->getString($value);
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

        // Hora inicio — soporta texto "08:00" y fracción decimal de Excel (0.333...)
        $inicioCrudo = preg_replace('/[^0-9:]/', '', $this->getTimeString($row[$inicioKey]));
        $iPartes = explode(':', $inicioCrudo);
        $inicio = sprintf('%02d:%02d:%02d', (int)($iPartes[0] ?? 0), (int)($iPartes[1] ?? 0), 0);

        // Hora fin
        $finCrudo = $finKey ? preg_replace('/[^0-9:]/', '', $this->getTimeString($row[$finKey])) : '';
        $fPartes = explode(':', $finCrudo);
        $fin = sprintf('%02d:%02d:%02d', (int)($fPartes[0] ?? 0), (int)($fPartes[1] ?? 0), 0);

        // Fila vacía → ignorar
        if ($inicio === '00:00:00' && $fin === '00:00:00') return null;
        if (empty($curso) || empty($docenteNombre)) return null;

        // Límite inferior: no se permiten asesorías antes de las 6:00 AM
        if ($inicio < '06:00:00') {
            $this->omitidas[] = "Horario antes de las 6:00 AM: \"$curso\" con $docenteNombre el $dia — inicio a las $inicio. No se permiten asesorías antes de las 6:00 AM.";
            return null;
        }

        // Límite superior de horario institucional
        $esSabado   = strtolower($dia) === 'sábado' || strtolower($dia) === 'sabado';
        $limiteMaximo = $esSabado ? '17:00:00' : '22:00:00';
        if ($fin > $limiteMaximo) {
            $limite = $esSabado ? '5:00 PM' : '10:00 PM';
            $this->omitidas[] = "Horario fuera de rango: \"$curso\" con $docenteNombre el $dia de $inicio a $fin — el límite los " . ($esSabado ? 'sábados' : 'días de semana') . " es hasta las $limite.";
            return null;
        }

        // Columnas opcionales
        $correoKey   = $this->resolve($row, 'correo');
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
        $semestre = ($sTime !== false && $sTime > 0) ? date('Y-m-d', $sTime) : $this->detectarSemestre();

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
        // 4. BUSCAR O CREAR PROFESOR — prioridad: correo del Excel
        // ──────────────────────────────────────────────────────────────
        $correo   = $correoKey ? $this->getString($row[$correoKey]) : '';
        $profesor = null;

        if (!empty($correo)) {
            $profesor = User::where('email', $correo)->first();
        }
        if (!$profesor) {
            $profesor = User::where('name', 'LIKE', '%' . $docenteNombre . '%')->first();
        }

        if (!$profesor) {
            $emailFinal = !empty($correo)
                ? $correo
                : strtolower(str_replace([' ', '.', ','], ['.', '', ''], $docenteNombre)) . '@agendau.com';

            $profesor = User::create([
                'name'             => $docenteNombre,
                'email'            => $emailFinal,
                'password'         => bcrypt('Pascual2026'),
                'rol'              => 'profesor',
                'activo'           => true,
                'cambiar_password' => true,
            ]);
        }

        $this->insertados++;

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