<?php

namespace App\Imports;

use App\Models\HorarioAsesoria;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HorariosImportWide implements ToCollection, WithHeadingRow
{
    private array $omitidas  = [];
    private int   $insertados = 0;

    public function getOmitidas(): array
    {
        return $this->omitidas;
    }

    public function getInsertados(): int
    {
        return $this->insertados;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function normalizar(string $s): string
    {
        $s = strtolower(trim($s));
        return str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $s);
    }

    private function getString(mixed $value): string
    {
        // Fracción decimal de Excel para horas (ej: 0.75 = 18:00)
        if (is_float($value) && $value >= 0 && $value < 1) {
            $mins = (int) round($value * 1440);
            return sprintf('%02d:%02d', intdiv($mins, 60), $mins % 60);
        }
        if (is_object($value)) {
            return method_exists($value, '__toString') ? trim((string) $value) : '';
        }
        return trim((string) ($value ?? ''));
    }

    private function resolveKey(array $row, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            $normAlias = $this->normalizar($alias);
            foreach ($row as $key => $_) {
                if ($this->normalizar((string) $key) === $normAlias) {
                    return $key;
                }
            }
        }
        return null;
    }

    // ─── Parseo de hora ───────────────────────────────────────────────────────

    private function parsearHora(string $texto): ?string
    {
        $texto = strtolower(trim($texto));

        // "12m" / "12pm" → noon
        if (preg_match('/^(\d{1,2})\s*m$/i', $texto, $m)) {
            return sprintf('%02d:00:00', (int) $m[1]);
        }
        // "8am" / "8 am"
        if (preg_match('/^(\d{1,2})\s*am$/i', $texto, $m)) {
            $h = (int) $m[1];
            if ($h === 12) $h = 0;
            return sprintf('%02d:00:00', $h);
        }
        // "5pm" / "5 pm"
        if (preg_match('/^(\d{1,2})\s*pm$/i', $texto, $m)) {
            $h = (int) $m[1];
            if ($h !== 12) $h += 12;
            return sprintf('%02d:00:00', $h);
        }
        // "10:30"
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $texto, $m)) {
            return sprintf('%02d:%02d:00', (int) $m[1], (int) $m[2]);
        }
        // plain number like "16", "8"
        if (preg_match('/^(\d{1,2})$/', $texto, $m)) {
            return sprintf('%02d:00:00', (int) $m[1]);
        }

        return null;
    }

    private function parsearRango(string $celda): ?array
    {
        $texto = trim($celda);
        if (empty($texto)) return null;

        // Remove "Horario: " prefix and anything after | or (
        $texto = preg_replace('/^horario\s*:\s*/i', '', $texto);
        $texto = preg_replace('/[\(|].*$/s', '', $texto);
        $texto = trim($texto);

        // Separator " a " (common in Colombia: "10am a 12m")
        if (preg_match('/^(.+?)\s+a\s+(.+)$/i', $texto, $m)) {
            $i = $this->parsearHora(trim($m[1]));
            $f = $this->parsearHora(trim($m[2]));
            if ($i && $f) return [$i, $f];
        }

        // Separator " - " or "-"
        if (preg_match('/^(.+?)\s*[-–]\s*(.+)$/', $texto, $m)) {
            $i = $this->parsearHora(trim($m[1]));
            $f = $this->parsearHora(trim($m[2]));
            if ($i && $f) return [$i, $f];
        }

        return null;
    }

    // ─── Procesamiento principal ──────────────────────────────────────────────

    public function collection(Collection $rows): void
    {
        $diasMap = [
            'lunes'   => 'Lunes',
            'martes'  => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves'  => 'Jueves',
            'viernes' => 'Viernes',
            'sabado'  => 'Sábado',
        ];

        foreach ($rows as $row) {
            $rowArr = $row->toArray();

            $cursoKey    = $this->resolveKey($rowArr, ['cursos', 'curso', 'materia', 'asignatura']);
            $docenteKey  = $this->resolveKey($rowArr, ['profesor', 'docente', 'nombre_profesor', 'nombre_docente']);
            $correoKey   = $this->resolveKey($rowArr, ['correo', 'email', 'correo_electronico']);
            $sedeKey     = $this->resolveKey($rowArr, ['sede', 'campus']);
            $modalidadKey = $this->resolveKey($rowArr, ['modalidad', 'tipo', 'tipo_clase']);

            if (!$cursoKey || !$docenteKey) continue;

            $curso         = $this->getString($rowArr[$cursoKey]);
            $docenteNombre = $this->getString($rowArr[$docenteKey]);

            if (empty($curso) || empty($docenteNombre)) continue;

            $correo    = $correoKey    ? $this->getString($rowArr[$correoKey])    : '';
            $sede      = $sedeKey      ? $this->getString($rowArr[$sedeKey])      : '';
            $modalidad = $modalidadKey ? ucfirst(strtolower($this->getString($rowArr[$modalidadKey]))) : 'Presencial';

            // Buscar o crear profesor — prioridad: correo del Excel
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
                    : strtolower(preg_replace('/[^a-z0-9.]/', '.', strtolower($docenteNombre))) . '@agendau.com';

                if (User::where('email', $emailFinal)->exists()) {
                    $emailFinal = preg_replace('/[^a-z0-9.]/', '.', strtolower($docenteNombre))
                                . '.' . substr(md5($docenteNombre), 0, 6)
                                . '@agendau.com';
                }

                $profesor = User::create([
                    'name'             => $docenteNombre,
                    'email'            => $emailFinal,
                    'password'         => bcrypt('Pascual2026'),
                    'rol'              => 'profesor',
                    'activo'           => true,
                    'cambiar_password' => true,
                ]);
            }

            // Crear un horario por cada día con contenido
            foreach ($diasMap as $diaNorm => $diaNombre) {
                $dayKey = null;
                foreach ($rowArr as $key => $_) {
                    if ($this->normalizar((string) $key) === $diaNorm) {
                        $dayKey = $key;
                        break;
                    }
                }
                if (!$dayKey) continue;

                $celda = $this->getString($rowArr[$dayKey]);
                if (empty($celda)) continue;

                $rango = $this->parsearRango($celda);
                if (!$rango) continue;

                [$inicio, $fin] = $rango;

                // Límite de horario institucional
                $esSabado     = $diaNorm === 'sabado';
                $limiteMaximo = $esSabado ? '17:00:00' : '22:00:00';
                if ($fin > $limiteMaximo) {
                    $limite = $esSabado ? '5:00 PM' : '10:00 PM';
                    $this->omitidas[] = "Horario fuera de rango: \"$curso\" con $docenteNombre el $diaNombre de $inicio a $fin — el límite los " . ($esSabado ? 'sábados' : 'días de semana') . " es hasta las $limite.";
                    continue;
                }

                // Validar cruce de horarios
                $conflicto = DB::table('horarios_asesoria')
                    ->where('docente_nombre', $docenteNombre)
                    ->where('dia_semana', $diaNombre)
                    ->where(function ($q) use ($inicio, $fin) {
                        $q->where('hora_inicio', '<', $fin)
                          ->where('hora_fin', '>', $inicio);
                    })->exists();

                if ($conflicto) {
                    $this->omitidas[] = "Conflicto: \"$curso\" con $docenteNombre el $diaNombre de $inicio a $fin.";
                    continue;
                }

                HorarioAsesoria::create([
                    'curso_nombre'   => $curso,
                    'docente_nombre' => $docenteNombre,
                    'dia_semana'     => $diaNombre,
                    'hora_inicio'    => $inicio,
                    'hora_fin'       => $fin,
                    'modalidad'      => $modalidad,
                    'sede'           => $sede,
                    'bloque'         => '',
                    'aula'           => '',
                    'lugar'          => '',
                    'semestre'       => date('Y-m-d'),
                    'user_id'        => $profesor->id,
                ]);
                $this->insertados++;
            }
        }
    }
}
