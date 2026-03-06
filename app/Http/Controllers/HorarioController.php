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
use Illuminate\Support\Str; // <-- NUEVO: Importado para limpiar nombres de archivos

class HorarioController extends Controller
{
    // ==========================================
    // IMPORTACIÓN Y GESTIÓN DE PROFESORES
    // ==========================================
    public function importar(Request $request)
    {
        $request->validate(['archivo_excel' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new HorariosImport, $request->file('archivo_excel'));
        return back()->with('exito', '¡Los horarios se han importado correctamente a la base de datos!');
    }

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

    // ==========================================
    // GENERACIÓN DE PDF PARA EL PROFESOR
    // ==========================================
    public function generarPdf($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $user = auth()->user();

        // Candado de seguridad
        if ($user->rol !== 'admin' && $horario->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar esta lista.');
        }

        $reservas = Seguimiento::where('horario_id', $id)->with('estudiante')->get();

        // Generamos el PDF
        $pdf = Pdf::loadView('profesor.pdf', compact('horario', 'reservas'));
        
        // CORRECCIÓN: Usamos Str::slug para evitar errores con tildes o caracteres raros en el nombre del archivo
        $nombreArchivo = 'Lista_Asistencia_' . Str::slug($horario->curso_nombre) . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    public function editar($id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        $profesores = User::whereIn('rol', ['profesor', 'admin'])->get();
        return view('horarios.editar', compact('horario', 'profesores'));
    }

    public function actualizar(Request $request, $id)
    {
        $horario = HorarioAsesoria::findOrFail($id);
        
        $horario->update([
            'curso_nombre'   => $request->curso_nombre,
            'docente_nombre' => $request->docente_nombre,
            'dia_semana'     => $request->dia_semana,
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'user_id'        => $request->user_id,
            // LOS 4 NUEVOS CAMPOS MÁGICOS
            'modalidad'      => $request->modalidad,
            'sede'           => $request->sede,
            'bloque'         => $request->bloque,
            'aula'           => $request->aula,
        ]);
        
        return redirect()->route('dashboard')->with('exito', '✅ Clase actualizada con su nueva modalidad y ubicación.');
    }

    public function eliminar($id)
    {
        $horario = HorarioAsesoria::findOrFail($id)->delete();
        return redirect()->route('dashboard')->with('exito', '¡La clase fue eliminada del sistema!');
    }

    // ==========================================
    // GESTIÓN DE ASISTENCIAS Y RESERVAS (ADMIN/PROFESOR)
    // ==========================================
    public function eliminarReserva($id)
    {
        Seguimiento::findOrFail($id)->delete();
        return back()->with('exito', '¡El estudiante fue removido de la clase correctamente!');
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
    // LÓGICA DEL ESTUDIANTE (ZONA PÚBLICA)
    // ==========================================
    public function getHorariosJson()
    {
        $horarios = HorarioAsesoria::all();
        $eventos = [];
        
        $diasMap = [
            'Lunes' => 1, 'Martes' => 2, 'Miercoles' => 3, 'Miércoles' => 3,
            'Jueves' => 4, 'Viernes' => 5, 'Sabado' => 6, 'Sábado' => 6, 'Domingo' => 0
        ];

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
                    'lugar' => $h->lugar,
                    'docente' => $h->docente_nombre,
                    'modalidad' => $h->modalidad, 
                    'sede' => $h->sede,
                    'bloque' => $h->bloque,
                    'aula' => $h->aula
                ]
            ];
        }
        return response()->json($eventos);
    }

public function reservar(Request $request)
    {
        // 1. Agregamos 'fecha' a la validación obligatoria
        $request->validate([
            'cedula' => 'required|string|max:20',
            'horario_id' => 'required|integer|exists:horarios_asesoria,id',
            'fecha' => 'required|date', // <-- NUEVO: Exigimos la fecha del calendario
        ]);

        $estudiante = Estudiante::where('cedula', $request->cedula)->first();

        if (!$estudiante) {
            return back()->with('error', '⛔ Cédula no encontrada en el sistema.');
        }

        // 2. Bloqueamos solo si ya tiene una reserva activa PARA ESA FECHA ESPECÍFICA
        $reservaExistente = Seguimiento::where('estudiante_id', $estudiante->id)
                                ->where('horario_id', $request->horario_id)
                                ->where('fecha', $request->fecha) // <-- Validamos que no repita el mismo día
                                ->where('estado', 'Programada') 
                                ->exists();

        if ($reservaExistente) {
            return back()->with('error', '⚠️ Ya tienes un cupo activo reservado para esta clase en esa fecha específica.');
        }

        // 3. Guardamos la reserva con la fecha real del calendario
        Seguimiento::create([
            'horario_id' => $request->horario_id,
            'estudiante_id' => $estudiante->id,
            'fecha' => $request->fecha, // <-- EL CAMBIO MAESTRO: Usamos la fecha seleccionada, no el 'now()'
            'hora_registro' => now()->toTimeString(),
            'estado' => 'Programada'
        ]);

        return back()->with('exito', '✅ ¡Cupo reservado con éxito!');
    }

    public function generarReporteIndividual(Request $request, $id)
    {
        // 1. Buscamos la reserva exacta a la que le dimos clic
        $reserva = Seguimiento::with(['estudiante', 'horario'])->findOrFail($id);
        
        // 2. Guardamos el comentario del profesor que viene de la ventanita
        $reserva->update([
            'evolucion' => $request->input('evolucion')
        ]);

        // 3. Buscamos TODO el historial de este estudiante en esta clase específica
        $historial = Seguimiento::where('estudiante_id', $reserva->estudiante_id)
                                ->where('horario_id', $reserva->horario_id)
                                ->orderBy('fecha', 'asc')
                                ->get();

        // 4. Generamos el PDF con una vista nueva que crearemos
        $pdf = Pdf::loadView('profesor.reporte_individual', compact('reserva', 'historial'));
        
        // 5. ¡PUM! Descarga con el nombre del estudiante limpio
        $nombreArchivo = 'Reporte_' . \Illuminate\Support\Str::slug($reserva->estudiante->nombre_completo) . '.pdf';
        return $pdf->download($nombreArchivo);
    }
    
    // ==========================================
    // MÓDULO DE SEGUIMIENTO EXCLUSIVO POR CÉDULA
    // ==========================================
    public function seguimientoIndex()
    {
        // Solo muestra la pantalla en blanco con el buscador
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
        
        // Buscamos todas las reservas de este estudiante
        $query = Seguimiento::with(['horario'])->where('estudiante_id', $estudiante->id);
        
        // Si no es admin, filtramos para que el profe solo vea las asistencias de SUS propias clases
        if ($user->rol !== 'admin') {
            $query->whereHas('horario', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $historial = $query->orderBy('fecha', 'desc')->get();

        return view('profesor.seguimiento', compact('estudiante', 'historial'));
    }
}