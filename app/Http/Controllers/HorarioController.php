<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\HorarioAsesoria;
use App\Models\Seguimiento;
use App\Models\User;
use App\Imports\HorariosImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class HorarioController extends Controller
{
    // ==========================================
    // PODERES EXCLUSIVOS DEL ADMINISTRADOR
    // ==========================================
    public function importar(Request $request)
    {
        if (auth()->user()->rol !== 'admin') { abort(403, '⛔ Acción no permitida.'); }
        
        $request->validate(['archivo_excel' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new HorariosImport, $request->file('archivo_excel'));
        return back()->with('exito', '¡Los horarios se han importado correctamente a la base de datos!');
    }

public function editar($id)
    {
        if (auth()->user()->rol !== 'admin') { abort(403, '⛔ Acción no permitida. Solo administradores.'); }
        
        $horario = HorarioAsesoria::findOrFail($id);
        
        // 🔥 FIX: Solo traer usuarios que tengan exactamente el rol de 'profesor'
        $profesores = User::where('rol', 'profesor')->get(); 
        
        return view('horarios.editar', compact('horario', 'profesores'));
    }

    public function actualizar(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') { abort(403, '⛔ Acción no permitida.'); }
        
        $horario = HorarioAsesoria::findOrFail($id);
        $horario->update([
            'curso_nombre'   => $request->curso_nombre,
            'docente_nombre' => $request->docente_nombre,
            'dia_semana'     => $request->dia_semana,
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'user_id'        => $request->user_id,
            'modalidad'      => $request->modalidad,
            'sede'           => $request->sede,
            'bloque'         => $request->bloque,
            'aula'           => $request->aula,
        ]);
        return redirect()->route('dashboard')->with('exito', '✅ Clase actualizada correctamente.');
    }

    public function eliminar($id)
    {
        if (auth()->user()->rol !== 'admin') { abort(403, '⛔ Acción no permitida.'); }
        
        HorarioAsesoria::findOrFail($id)->delete();
        return redirect()->route('dashboard')->with('exito', '¡La clase fue eliminada del sistema!');
    }

    // ==========================================
    // VISTAS Y GESTIÓN GENERAL (Admin y Profesores)
    // ==========================================
    public function verEstudiantes($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $user = auth()->user();

        if ($user->rol !== 'admin' && $horario->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver los estudiantes de esta clase.');
        }

        $reservas = Seguimiento::where('horario_id', $id)->with('estudiante')->get();
        return view('profesor.estudiantes', compact('horario', 'reservas'));
    }

    public function generarPdf($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $user = auth()->user();

        if ($user->rol !== 'admin' && $horario->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar esta lista.');
        }

        $reservas = Seguimiento::where('horario_id', $id)->with('estudiante')->get();
        $pdf = Pdf::loadView('profesor.pdf', compact('horario', 'reservas'));
        $nombreArchivo = 'Lista_Asistencia_' . Str::slug($horario->curso_nombre) . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    public function eliminarReserva($id)
    {
        Seguimiento::findOrFail($id)->delete();
        return back()->with('exito', '¡Reserva cancelada correctamente!');
    }

    public function marcarAsistencia(Request $request, $id)
    {
        $reserva = Seguimiento::findOrFail($id);
        $reserva->update(['estado' => 'Evaluada', 'asistencia' => $request->asistencia]);
        return back()->with('exito', $request->asistencia ? '✅ Asistencia registrada correctamente.' : '❌ Inasistencia registrada.');
    }

    public function corregirAsistencia($id)
    {
        $reserva = Seguimiento::findOrFail($id);
        $reserva->update(['estado' => 'Programada', 'asistencia' => null]);
        return back()->with('exito', 'Estado de asistencia restablecido. Ya puedes corregirlo.');
    }

    // ==========================================
    // REPORTES Y SEGUIMIENTO EXCLUSIVO
    // ==========================================
    public function generarReporteIndividual(Request $request, $id)
    {
        $reserva = Seguimiento::with(['estudiante', 'horario'])->findOrFail($id);
        $reserva->update(['evolucion' => $request->input('evolucion')]);
        $historial = Seguimiento::where('estudiante_id', $reserva->estudiante_id)
                                ->where('horario_id', $reserva->horario_id)
                                ->orderBy('fecha', 'asc')
                                ->get();
        $pdf = Pdf::loadView('profesor.reporte_individual', compact('reserva', 'historial'));
        return $pdf->download('Reporte_' . \Illuminate\Support\Str::slug($reserva->estudiante->nombre_completo) . '.pdf');
    }
    
    public function seguimientoIndex()
    {
        return view('profesor.seguimiento');
    }

    public function seguimientoBuscar(Request $request)
    {
        $request->validate(['cedula' => 'required|string']);
        $estudiante = Estudiante::where('cedula', $request->cedula)->first();
        if (!$estudiante) {
            return redirect()->route('seguimiento.index')->with('error', 'No se encontró ningún estudiante con esa cédula.');
        }

        $user = auth()->user();
        $query = Seguimiento::with(['horario'])->where('estudiante_id', $estudiante->id);
        if ($user->rol !== 'admin') {
            $query->whereHas('horario', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        $historial = $query->orderBy('fecha', 'desc')->get();
        return view('profesor.seguimiento', compact('estudiante', 'historial'));
    }

    // ==========================================
    // LÓGICA DEL ESTUDIANTE (ZONA PÚBLICA)
    // ==========================================
    public function getHorariosJson()
    {
        $horarios = HorarioAsesoria::all();
        $eventos = [];
        $diasMap = ['Lunes' => 1, 'Martes' => 2, 'Miercoles' => 3, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sabado' => 6, 'Sábado' => 6, 'Domingo' => 0];

        foreach ($horarios as $h) {
            $diaNum = $diasMap[$h->dia_semana] ?? 1;
            $eventos[] = [
                'id' => $h->id,
                'title' => $h->curso_nombre . ' (' . $h->docente_nombre . ')',
                'startTime' => $h->hora_inicio,
                'endTime' => $h->hora_fin,
                'daysOfWeek' => [$diaNum],
                'color' => '#002845',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'lugar' => $h->lugar, 'docente' => $h->docente_nombre, 'modalidad' => $h->modalidad, 
                    'sede' => $h->sede, 'bloque' => $h->bloque, 'aula' => $h->aula
                ]
            ];
        }
        return response()->json($eventos);
    }

    public function misHorariosJson()
    {
        $estudiante_id = session('estudiante_id');
        if (!$estudiante_id) return response()->json([]);

        $reservas = Seguimiento::where('estudiante_id', $estudiante_id)->get();
        $eventos = [];
        foreach ($reservas as $reserva) {
            $horario = HorarioAsesoria::find($reserva->horario_id);
            if ($horario) {
                $eventos[] = [
                    'id' => $reserva->id,
                    'title' => $horario->curso_nombre,
                    'start' => $reserva->fecha . 'T' . $horario->hora_inicio,
                    'end' => $reserva->fecha . 'T' . $horario->hora_fin,
                    'color' => '#10b981',
                    'extendedProps' => ['sede' => $horario->sede, 'bloque' => $horario->bloque, 'aula' => $horario->aula, 'modalidad' => $horario->modalidad]
                ];
            }
        }
        return response()->json($eventos);
    }

    public function reservar(Request $request)
    {
        $request->validate(['horario_id' => 'required|integer|exists:horarios_asesoria,id', 'fecha' => 'required|date']);
        $estudiante_id = session('estudiante_id');

        if (!$estudiante_id) {
            if (session('correo_pendiente')) {
                $request->validate(['cedula' => 'required|string|max:20']);
                $estudiante = Estudiante::where('cedula', $request->cedula)->first();
                if (!$estudiante) return back()->with('error', '⛔ Cédula no encontrada en el sistema.');
                if ($estudiante->correo && $estudiante->correo !== session('correo_pendiente')) return back()->with('error', '⛔ Cédula ya vinculada.');
                if (empty($estudiante->correo)) $estudiante->update(['correo' => session('correo_pendiente')]);
                
                session(['estudiante_id' => $estudiante->id, 'estudiante_nombre' => $estudiante->nombre_completo]);
                session()->forget(['correo_pendiente', 'google_nombre']); 
                $estudiante_id = $estudiante->id; 
            } else {
                return back()->with('error', '⛔ Inicia sesión con Google primero.');
            }
        }

        if (Seguimiento::where('estudiante_id', $estudiante_id)->where('horario_id', $request->horario_id)->where('fecha', $request->fecha)->where('estado', 'Programada')->exists()) {
            return back()->with('error', '⚠️ Ya tienes cupo para esta clase en esta fecha.');
        }

        Seguimiento::create(['horario_id' => $request->horario_id, 'estudiante_id' => $estudiante_id, 'fecha' => $request->fecha, 'hora_registro' => now()->toTimeString(), 'estado' => 'Programada']);
        return redirect('/estudiante')->with('exito', '✅ ¡Cupo reservado con éxito!');
    }

    public function cancelarReservaEstudiante($id)
    {
        if (!session('estudiante_id')) return redirect('/')->with('error', 'Debes iniciar sesión.');
        $reserva = Seguimiento::where('id', $id)->where('estudiante_id', session('estudiante_id'))->first();
        if ($reserva) { $reserva->delete(); return back()->with('exito', '🗑️ Reserva cancelada.'); }
        return back()->with('error', 'No se pudo cancelar.');
    }

    public function cancelarDesdePublico(Request $request)
    {
        $request->validate(['horario_id' => 'required|integer', 'fecha' => 'required|date']);
        if (!session('estudiante_id')) return back()->with('error', 'Debes iniciar sesión.');
        
        $reserva = Seguimiento::where('estudiante_id', session('estudiante_id'))->where('horario_id', $request->horario_id)->where('fecha', $request->fecha)->where('estado', 'Programada')->first();
        if ($reserva) { $reserva->delete(); return back()->with('exito', '🗑️ Reserva cancelada.'); }
        return back()->with('error', '⚠️ No tienes reserva activa para esta clase hoy.');
    }
}