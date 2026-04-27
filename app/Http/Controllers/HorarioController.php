<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\HorarioAsesoria;
use App\Models\Seguimiento;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Imports\HorariosImport;
use App\Imports\HorariosImportWide;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HorarioController extends Controller
{
    // ─── Utilidad interna ────────────────────────────────────────────────────

    private function autorizarSeguimiento(Seguimiento $seguimiento): void
    {
        $user = auth()->user();
        if ($user->rol !== 'admin' && $seguimiento->horario->user_id !== $user->id) {
            abort(403);
        }
    }

    // ─── Importación Excel ──────────────────────────────────────────────────

    private function normalizarHeader(string $h): string
    {
        $h = strtolower(trim($h));
        return str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $h);
    }

    private function leerEncabezados(string $ruta): array
    {
        try {
            $spreadsheet = IOFactory::load($ruta);
            $sheet   = $spreadsheet->getActiveSheet();
            $headers = [];
            foreach ($sheet->getRowIterator(1, 1) as $row) {
                $cells = $row->getCellIterator();
                $cells->setIterateOnlyExistingCells(true);
                foreach ($cells as $cell) {
                    $val = trim((string) $cell->getValue());
                    if ($val !== '') {
                        $headers[] = $this->normalizarHeader($val);
                    }
                }
            }
            return $headers;
        } catch (\Throwable) {
            return [];
        }
    }

    private function validarFormatoExcel(string $ruta): ?string
    {
        $headers = $this->leerEncabezados($ruta);

        if (empty($headers)) {
            return 'Formato inválido: el archivo está vacío o no se pudieron leer los encabezados.';
        }

        $dias  = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        $tieneCurso    = count(array_intersect($headers, ['cursos','curso','materia','asignatura'])) > 0;
        $tieneDias     = count(array_intersect($headers, $dias)) > 0;
        $tieneDiaLargo = count(array_intersect($headers, ['dia','dia_semana'])) > 0;
        $tieneInicio   = count(array_intersect($headers, ['inicio','hora_inicio'])) > 0;

        if (($tieneCurso && $tieneDias) || ($tieneCurso && $tieneDiaLargo && $tieneInicio)) {
            return null;
        }

        return 'Formato de archivo inválido ❌ — El Excel no tiene las columnas requeridas. '
             . 'Se esperan columnas como CURSOS, PROFESOR, LUNES, MARTES… (formato amplio) '
             . 'o CURSO, DOCENTE, DIA, INICIO, FIN (formato largo). '
             . 'Columnas detectadas: ' . implode(', ', $headers);
    }

    private function esFormatoAncho(string $ruta): bool
    {
        $headers = $this->leerEncabezados($ruta);
        $dias    = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        return count(array_intersect($headers, $dias)) > 0;
    }

    public function importar(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo_excel.required' => 'Debes seleccionar un archivo para subir.',
            'archivo_excel.mimes'    => 'El archivo debe ser formato Excel (.xlsx, .xls o .csv).',
        ]);

        $ruta = $request->file('archivo_excel')->getPathname();

        $formatoError = $this->validarFormatoExcel($ruta);
        if ($formatoError !== null) {
            return back()->with('error', $formatoError);
        }

        try {
            $import = $this->esFormatoAncho($ruta) ? new HorariosImportWide : new HorariosImport;
            Excel::import($import, $request->file('archivo_excel'));

            $omitidas = $import->getOmitidas();

            if (!empty($omitidas)) {
                return back()
                    ->with('exito', 'Importación completada. ' . count($omitidas) . ' fila(s) omitida(s) por conflicto de horario.')
                    ->with('lista_errores', $omitidas);
            }

            return back()->with('exito', 'El archivo se procesó exitosamente sin conflictos.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->with('error', 'Hubo errores de validación en algunas filas del Excel.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    // ─── CRUD de Horarios ───────────────────────────────────────────────────

    public function editar($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $horario   = HorarioAsesoria::findOrFail($id);
        $profesores = User::where('rol', 'profesor')->orderBy('name')->get();

        return view('horarios.editar', compact('horario', 'profesores'));
    }

    public function actualizar(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $horario = HorarioAsesoria::findOrFail($id);

        $validated = $request->validate([
            'curso_nombre'   => 'required|string|max:255',
            'docente_nombre' => 'required|string|max:255',
            'dia_semana'     => 'required|string|max:20',
            'hora_inicio'    => 'required',
            'hora_fin'       => 'required',
            'user_id'        => 'nullable|exists:users,id',
            'modalidad'      => 'nullable|string|max:50',
            'lugar'          => 'nullable|string|max:255',
            'sede'           => 'nullable|string|max:255',
            'bloque'         => 'nullable|string|max:100',
            'aula'           => 'nullable|string|max:100',
        ]);

        $horario->update($validated);

        return redirect()->route('dashboard')->with('exito', 'Clase actualizada correctamente.');
    }

    public function eliminar($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        HorarioAsesoria::findOrFail($id)->delete();

        return redirect()->route('dashboard')->with('exito', 'La clase fue eliminada del sistema.');
    }

    public function vaciarClases()
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        Seguimiento::truncate();
        HorarioAsesoria::truncate();

        return back()->with('exito', 'Todas las clases y reservas fueron eliminadas correctamente.');
    }

    // ─── Vista de estudiantes / Asistencia ─────────────────────────────────

    public function verEstudiantes($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $user    = auth()->user();

        if ($user->rol !== 'admin' && $horario->user_id !== $user->id) abort(403);

        $reservas = Seguimiento::where('horario_id', $id)->with('estudiante')->get();

        return view('profesor.estudiantes', compact('horario', 'reservas'));
    }

    public function marcarAsistencia(Request $request, $id)
    {
        $seguimiento = Seguimiento::with('horario')->findOrFail($id);
        $this->autorizarSeguimiento($seguimiento);

        $seguimiento->update([
            'estado'     => 'Evaluada',
            'asistencia' => $request->boolean('asistencia'),
        ]);

        return back()->with('exito', $request->boolean('asistencia')
            ? 'Asistencia registrada.'
            : 'Inasistencia registrada.');
    }

    public function corregirAsistencia($id)
    {
        $seguimiento = Seguimiento::with('horario')->findOrFail($id);
        $this->autorizarSeguimiento($seguimiento);

        $seguimiento->update(['estado' => 'Programada', 'asistencia' => null]);

        return back()->with('exito', 'Estado restablecido a pendiente.');
    }

    public function eliminarReserva($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        Seguimiento::findOrFail($id)->delete();

        return back()->with('exito', 'Reserva eliminada.');
    }

    // ─── PDF de lista de asistencia ────────────────────────────────────────

    public function generarPdf($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $user    = auth()->user();

        if ($user->rol !== 'admin' && $horario->user_id !== $user->id) abort(403);

        $reservas = Seguimiento::where('horario_id', $id)->with('estudiante')->get();
        $pdf      = Pdf::loadView('profesor.pdf', compact('horario', 'reservas'));

        return $pdf->download('Lista_Asistencia_' . Str::slug($horario->curso_nombre) . '.pdf');
    }

    // ─── Reporte individual de evolución ───────────────────────────────────

    public function generarReporteIndividual(Request $request, $id)
    {
        $reserva = Seguimiento::with(['estudiante', 'horario'])->findOrFail($id);
        $this->autorizarSeguimiento($reserva);

        $reserva->update(['evolucion' => $request->input('evolucion')]);

        if ($request->input('accion') === 'guardar') {
            return back()->with('exito', 'Observaciones guardadas.');
        }

        $historial = Seguimiento::where('estudiante_id', $reserva->estudiante_id)
            ->where('horario_id', $reserva->horario_id)
            ->orderBy('fecha')
            ->get();

        $pdf = Pdf::loadView('profesor.reporte_individual', compact('reserva', 'historial'));

        return $pdf->download('Reporte_' . Str::slug($reserva->estudiante->nombre_completo) . '.pdf');
    }

    // ─── Seguimiento de estudiantes ─────────────────────────────────────────

    public function seguimientoIndex()
    {
        $user     = auth()->user();
        $horarios = $user->rol === 'admin'
            ? HorarioAsesoria::orderBy('curso_nombre')->withCount('seguimientos')->get()
            : HorarioAsesoria::where('user_id', $user->id)->orderBy('curso_nombre')->withCount('seguimientos')->get();

        return view('profesor.seguimiento', compact('horarios'));
    }

    public function seguimientoBuscar(Request $request)
    {
        $request->validate(['cedula' => 'required|string']);

        $user     = auth()->user();
        $horarios = $user->rol === 'admin'
            ? HorarioAsesoria::orderBy('curso_nombre')->withCount('seguimientos')->get()
            : HorarioAsesoria::where('user_id', $user->id)->orderBy('curso_nombre')->withCount('seguimientos')->get();

        $estudiante = Estudiante::where('cedula', $request->cedula)->first();

        if (!$estudiante) {
            return view('profesor.seguimiento', compact('horarios'))
                ->with('error', 'No se encontró ningún estudiante registrado con esa cédula.');
        }

        $query = Seguimiento::with('horario')->where('estudiante_id', $estudiante->id);

        if ($user->rol !== 'admin') {
            $query->whereHas('horario', fn($q) => $q->where('user_id', $user->id));
        }

        $historial = $query->orderByDesc('fecha')->get();

        if ($historial->isEmpty() && $user->rol !== 'admin') {
            return view('profesor.seguimiento', compact('horarios'))
                ->with('error', "El estudiante '{$estudiante->nombre_completo}' no tiene asesorías registradas en ninguno de tus cursos.");
        }

        return view('profesor.seguimiento', compact('estudiante', 'historial', 'horarios'));
    }

    // ─── API JSON para calendarios ──────────────────────────────────────────

    public function getHorariosJson()
    {
        $diasMap = [
            'Lunes' => 1, 'Martes' => 2, 'Miercoles' => 3, 'Miércoles' => 3,
            'Jueves' => 4, 'Viernes' => 5, 'Sabado' => 6, 'Sábado' => 6, 'Domingo' => 0,
        ];

        $eventos = HorarioAsesoria::select(
            'id', 'curso_nombre', 'docente_nombre', 'dia_semana',
            'hora_inicio', 'hora_fin', 'lugar', 'modalidad', 'sede', 'bloque', 'aula'
        )->get()->map(fn($h) => [
            'id'           => $h->id,
            'title'        => $h->curso_nombre . ' (' . $h->docente_nombre . ')',
            'startTime'    => $h->hora_inicio,
            'endTime'      => $h->hora_fin,
            'daysOfWeek'   => [$diasMap[$h->dia_semana] ?? 1],
            'color'        => '#002845',
            'textColor'    => '#ffffff',
            'extendedProps' => [
                'lugar'     => $h->lugar,
                'docente'   => $h->docente_nombre,
                'modalidad' => $h->modalidad,
                'sede'      => $h->sede,
                'bloque'    => $h->bloque,
                'aula'      => $h->aula,
            ],
        ]);

        return response()->json($eventos);
    }

    public function misHorariosJson()
    {
        $estudiante_id = session('estudiante_id');

        if (!$estudiante_id) return response()->json([]);

        $eventos = Seguimiento::with('horario')
            ->where('estudiante_id', $estudiante_id)
            ->get()
            ->filter(fn($r) => $r->horario)
            ->map(fn($r) => [
                'id'            => $r->id,
                'title'         => $r->horario->curso_nombre,
                'start'         => $r->fecha . 'T' . $r->horario->hora_inicio,
                'end'           => $r->fecha . 'T' . $r->horario->hora_fin,
                'color'         => '#10b981',
                'extendedProps' => [
                    'sede'      => $r->horario->sede,
                    'bloque'    => $r->horario->bloque,
                    'aula'      => $r->horario->aula,
                    'modalidad' => $r->horario->modalidad,
                ],
            ]);

        return response()->json($eventos->values());
    }

    // ─── Reservas de estudiantes ────────────────────────────────────────────

    public function reservar(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|integer|exists:horarios_asesoria,id',
            'fecha'      => 'required|date',
        ]);

        $estudiante_id = session('estudiante_id');

        if (!$estudiante_id) {
            if (!session('correo_pendiente')) {
                return back()->with('error', 'Debes iniciar sesión primero.');
            }

            $request->validate(['cedula' => 'required|string|max:20']);
            $estudiante = Estudiante::where('cedula', $request->cedula)->first();

            if (!$estudiante) {
                return back()->with('error', 'Cédula no encontrada.');
            }
            if ($estudiante->correo && $estudiante->correo !== session('correo_pendiente')) {
                return back()->with('error', 'Esta cédula ya está vinculada a otro correo.');
            }

            if (empty($estudiante->correo)) {
                $estudiante->update(['correo' => session('correo_pendiente')]);
            }

            session(['estudiante_id' => $estudiante->id, 'estudiante_nombre' => $estudiante->nombre_completo]);
            session()->forget(['correo_pendiente', 'google_nombre']);
            $estudiante_id = $estudiante->id;
        }

        $encuestaPendiente = Seguimiento::where('estudiante_id', $estudiante_id)
            ->where('estado', 'Evaluada')
            ->where('asistencia', true)
            ->where('encuesta_respondida', false)
            ->first();

        if ($encuestaPendiente) {
            return redirect()->route('encuesta.mostrar', $encuestaPendiente->id)
                ->with('error', 'Tienes una encuesta pendiente. Complétala antes de hacer una nueva reserva.');
        }

        $yaInscrito = Seguimiento::where('estudiante_id', $estudiante_id)
            ->where('horario_id', $request->horario_id)
            ->where('fecha', $request->fecha)
            ->where('estado', 'Programada')
            ->exists();

        if ($yaInscrito) {
            return back()->with('error', 'Ya tienes cupo reservado para esta clase en esa fecha.');
        }

        Seguimiento::create([
            'horario_id'    => $request->horario_id,
            'estudiante_id' => $estudiante_id,
            'fecha'         => $request->fecha,
            'hora_registro' => now()->toTimeString(),
            'estado'        => 'Programada',
        ]);

        return redirect('/estudiante')->with('exito', '¡Cupo reservado con éxito!');
    }

    public function cancelarReservaEstudiante($id)
    {
        if (!session('estudiante_id')) return redirect('/')->with('error', 'Debes iniciar sesión.');

        $reserva = Seguimiento::where('id', $id)
            ->where('estudiante_id', session('estudiante_id'))
            ->first();

        if (!$reserva) return back()->with('error', 'No se encontró la reserva.');

        $reserva->delete();

        return back()->with('exito', 'Reserva cancelada.');
    }

    public function cancelarDesdePublico(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|integer',
            'fecha'      => 'required|date',
        ]);

        if (!session('estudiante_id')) return back()->with('error', 'Debes iniciar sesión.');

        $reserva = Seguimiento::where('estudiante_id', session('estudiante_id'))
            ->where('horario_id', $request->horario_id)
            ->where('fecha', $request->fecha)
            ->where('estado', 'Programada')
            ->first();

        if (!$reserva) return back()->with('error', 'No tienes reserva activa para esta clase.');

        $reserva->delete();

        return back()->with('exito', 'Reserva cancelada.');
    }
}
