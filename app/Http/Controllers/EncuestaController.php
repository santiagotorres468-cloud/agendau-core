<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encuesta;
use App\Models\Seguimiento;
use App\Models\HorarioAsesoria;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class EncuestaController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // LADO ESTUDIANTE: Mostrar y guardar encuesta
    // ─────────────────────────────────────────────────────────────

    /**
     * Muestra el formulario de encuesta para una sesión evaluada.
     * Ruta: GET /encuesta/{seguimiento_id}
     */
    public function mostrar($seguimientoId)
    {
        if (!session('estudiante_id')) {
            return redirect('/')->with('error', '⛔ Debes iniciar sesión para responder la encuesta.');
        }

        $reserva = Seguimiento::with(['horario', 'estudiante'])
            ->where('id', $seguimientoId)
            ->where('estudiante_id', session('estudiante_id'))
            ->firstOrFail();

        // Validar que la sesión fue evaluada y que asistió
        if (!$reserva->puedeResponderEncuesta()) {
            return redirect('/estudiante')->with('error', '⚠️ Esta encuesta no está disponible o ya fue respondida.');
        }

        return view('encuesta.formulario', compact('reserva'));
    }

    /**
     * Guarda las respuestas de la encuesta.
     * Ruta: POST /encuesta/{seguimiento_id}
     */
    public function guardar(Request $request, $seguimientoId)
    {
        if (!session('estudiante_id')) {
            return redirect('/')->with('error', '⛔ Debes iniciar sesión.');
        }

        $request->validate([
            'p1_claridad'    => 'required|integer|min:1|max:5',
            'p2_puntualidad' => 'required|integer|min:1|max:5',
            'p3_dominio_tema'=> 'required|integer|min:1|max:5',
            'p4_utilidad'    => 'required|integer|min:1|max:5',
            'p5_ambiente'    => 'required|integer|min:1|max:5',
            'comentario'     => 'nullable|string|max:500',
        ], [
            'p1_claridad.required'    => 'Por favor califica la claridad del docente.',
            'p2_puntualidad.required' => 'Por favor califica la puntualidad.',
            'p3_dominio_tema.required'=> 'Por favor califica el dominio del tema.',
            'p4_utilidad.required'    => 'Por favor califica la utilidad de la sesión.',
            'p5_ambiente.required'    => 'Por favor califica el ambiente/espacio.',
        ]);

        $reserva = Seguimiento::where('id', $seguimientoId)
            ->where('estudiante_id', session('estudiante_id'))
            ->firstOrFail();

        if (!$reserva->puedeResponderEncuesta()) {
            return redirect('/estudiante')->with('error', '⚠️ Esta encuesta ya fue respondida o no está disponible.');
        }

        $data = $request->only([
            'p1_claridad', 'p2_puntualidad', 'p3_dominio_tema', 'p4_utilidad', 'p5_ambiente',
            'resumen_sesion', 'aspectos_mejorar', 'comentario',
        ]);
        $data['promedio'] = Encuesta::calcularPromedio($data);

        Encuesta::create([
            'seguimiento_id'  => $reserva->id,
            'estudiante_id'   => $reserva->estudiante_id,
            'horario_id'      => $reserva->horario_id,
            ...$data,
        ]);

        // Marcar como respondida para que no aparezca de nuevo
        $reserva->update(['encuesta_respondida' => true]);

        return redirect('/estudiante')->with('exito', '¡Gracias por tu opinión! Tu encuesta fue registrada correctamente.');
    }

    // ─────────────────────────────────────────────────────────────
    // LADO ADMIN: Reportes con datos de encuestas
    // ─────────────────────────────────────────────────────────────

    /**
     * Informe de satisfacción por docente (PDF + datos de encuesta).
     * Ruta: GET /admin/reporte-docente/{docente_id}
     * (Reemplaza el método en HorarioController para incluir encuestas)
     */
    public function reporteDocente(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate(['docente_id' => 'required|exists:users,id']);
        $docente = User::findOrFail($request->docente_id);

        $horarios = HorarioAsesoria::where('user_id', $docente->id)
            ->with(['seguimientos.encuesta'])
            ->get();

        // ── Estadísticas agregadas ──
        $totalSesiones   = $horarios->sum(fn($h) => $h->seguimientos->count());
        $totalAsistencias = $horarios->sum(fn($h) => $h->seguimientos->where('asistencia', true)->count());
        $totalEncuestas  = $horarios->sum(fn($h) => $h->seguimientos->filter(fn($s) => $s->encuesta)->count());

        // Promedio general de todas las encuestas de este docente
        $encuestas = Encuesta::whereIn('horario_id', $horarios->pluck('id'))->get();

        $promedioGeneral  = $encuestas->avg('promedio') ?? 0;
        $promedioClaro    = $encuestas->avg('p1_claridad') ?? 0;
        $promedioPuntual  = $encuestas->avg('p2_puntualidad') ?? 0;
        $promedioDominio  = $encuestas->avg('p3_dominio_tema') ?? 0;
        $promedioUtilidad = $encuestas->avg('p4_utilidad') ?? 0;
        $promedioAmbiente = $encuestas->avg('p5_ambiente') ?? 0;
        $comentarios      = $encuestas->whereNotNull('comentario')->pluck('comentario');

        $pdf = Pdf::loadView('admin.reporte_docente', compact(
            'docente', 'horarios', 'totalSesiones', 'totalAsistencias', 'totalEncuestas',
            'promedioGeneral', 'promedioClaro', 'promedioPuntual', 'promedioDominio',
            'promedioUtilidad', 'promedioAmbiente', 'comentarios', 'encuestas'
        ));

        return $pdf->download('Reporte_Docente_' . Str::slug($docente->name) . '.pdf');
    }

    /**
     * Informe de satisfacción por materia/curso (PDF + encuestas).
     * Ruta: GET /admin/reporte-curso
     */
    public function reporteCurso(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate(['curso_nombre' => 'required|string']);
        $curso = $request->curso_nombre;

        $horarios = HorarioAsesoria::where('curso_nombre', $curso)
            ->with(['seguimientos.encuesta'])
            ->get();

        $totalInscritos   = $horarios->sum(fn($h) => $h->seguimientos->count());
        $totalAsistencias = $horarios->sum(fn($h) => $h->seguimientos->where('asistencia', true)->count());
        $totalEncuestas   = $horarios->sum(fn($h) => $h->seguimientos->filter(fn($s) => $s->encuesta)->count());

        $encuestas = Encuesta::whereIn('horario_id', $horarios->pluck('id'))->get();

        $promedioGeneral  = round($encuestas->avg('promedio') ?? 0, 2);
        $promedioClaro    = round($encuestas->avg('p1_claridad') ?? 0, 2);
        $promedioPuntual  = round($encuestas->avg('p2_puntualidad') ?? 0, 2);
        $promedioDominio  = round($encuestas->avg('p3_dominio_tema') ?? 0, 2);
        $promedioUtilidad = round($encuestas->avg('p4_utilidad') ?? 0, 2);
        $promedioAmbiente = round($encuestas->avg('p5_ambiente') ?? 0, 2);
        $comentarios      = $encuestas->whereNotNull('comentario')->pluck('comentario');

        $pdf = Pdf::loadView('admin.reporte_curso', compact(
            'curso', 'horarios', 'totalInscritos', 'totalAsistencias', 'totalEncuestas',
            'promedioGeneral', 'promedioClaro', 'promedioPuntual', 'promedioDominio',
            'promedioUtilidad', 'promedioAmbiente', 'comentarios', 'encuestas'
        ));

        return $pdf->download('Reporte_Curso_' . Str::slug($curso) . '.pdf');
    }

    /**
     * Vista de dashboard de encuestas para el admin (sin PDF).
     * Ruta: GET /admin/encuestas
     */
    public function dashboard()
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $docentesConPromedio = User::where('rol', 'profesor')
            ->with('horarios')
            ->orderBy('name')
            ->get()
            ->map(function ($d) {
                $horarioIds = $d->horarios->pluck('id');
                $d->promedio_encuesta = round(Encuesta::whereIn('horario_id', $horarioIds)->avg('promedio') ?? 0, 2);
                $d->total_encuestas   = Encuesta::whereIn('horario_id', $horarioIds)->count();
                return $d;
            });

        $cursos = HorarioAsesoria::select('curso_nombre')
            ->distinct()
            ->orderBy('curso_nombre')
            ->pluck('curso_nombre');

        return view('admin.encuestas_dashboard', compact('docentesConPromedio', 'cursos'));
    }
}