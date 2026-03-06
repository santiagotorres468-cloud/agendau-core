<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\HorariosImport;
use Maatwebsite\Excel\Facades\Excel;

class HorarioController extends Controller
{
    public function importar(Request $request)
    {
        // Validamos que sí o sí suban un archivo de Excel
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        // La magia de la librería: lee el archivo y lo manda a nuestro Importador
        Excel::import(new HorariosImport, $request->file('archivo_excel'));

        // Devuelve al administrador a la página anterior con un mensaje de éxito
        return back()->with('exito', '¡Los horarios se han importado correctamente a la base de datos!');
    }
    // Agrega esto debajo de tu función 'importar'
public function verEstudiantes($id)
{
    $horario = \App\Models\HorarioAsesoria::findOrFail($id);
    $user = auth()->user();

    // SEGURIDAD: Si no es admin Y la clase no es suya, bloqueamos el acceso.
    if ($user->rol !== 'admin' && $horario->user_id !== $user->id) {
        abort(403, 'No tienes permiso para ver los estudiantes de esta clase.');
    }

    $reservas = \App\Models\Seguimiento::where('horario_id', $id)
                    ->with('estudiante')
                    ->get();

    return view('profesor.estudiantes', compact('horario', 'reservas'));
}
    // Muestra la vista con el formulario para editar
// 1. Mostrar el formulario de edición con la lista de profesores
    public function editar($id)
    {
        $horario = \App\Models\HorarioAsesoria::findOrFail($id);
        
        // Traemos a todos los usuarios que sean 'profesor' o 'admin' para llenar la lista desplegable
        $profesores = \App\Models\User::whereIn('rol', ['profesor', 'admin'])->get();

        return view('horarios.editar', compact('horario', 'profesores'));
    }

    // 2. Guardar los cambios en la base de datos
    public function actualizar(Request $request, $id)
    {
        $horario = \App\Models\HorarioAsesoria::findOrFail($id);
        
        $horario->update([
            'curso_nombre'   => $request->curso_nombre,
            'docente_nombre' => $request->docente_nombre,
            'dia_semana'     => $request->dia_semana,
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'lugar'          => $request->lugar,
            'user_id'        => $request->user_id, // ¡AQUÍ GUARDAMOS EL ID DEL PROFESOR!
        ]);

        return redirect()->route('dashboard')->with('exito', '✅ Clase actualizada y profesor asignado correctamente.');
    }

    // Borra la clase de la base de datos
    public function eliminar($id)
    {
        \App\Models\HorarioAsesoria::findOrFail($id)->delete();
        return redirect()->route('dashboard')->with('exito', '¡La clase fue eliminada del sistema!');
    }
    // Borra la reserva del estudiante
    public function eliminarReserva($id)
    {
        // Buscamos la reserva en la tabla seguimientos y la borramos
        \App\Models\Seguimiento::findOrFail($id)->delete();
        
        // Devolvemos al profesor a la misma pantalla con un mensaje de éxito
        return back()->with('exito', '¡El estudiante fue removido de la clase correctamente!');
    }
    // Registrar la asistencia del estudiante
    public function marcarAsistencia(Request $request, $id)
    {
        $reserva = \App\Models\Seguimiento::findOrFail($id);
        
        // Cambiamos el estado para saber que ya fue evaluado y guardamos si asistió (1) o no (0)
        $reserva->update([
            'estado' => 'Evaluada',
            'asistencia' => $request->asistencia
        ]);
        
        $mensaje = $request->asistencia ? '✅ Asistencia registrada correctamente.' : '❌ Inasistencia registrada.';
        
        return back()->with('exito', $mensaje);
    }
public function corregirAsistencia($id)
{
    $reserva = \App\Models\Seguimiento::findOrFail($id);
    
    // Devolvemos el estado a 'Programada' y limpiamos la asistencia
    $reserva->update([
        'estado' => 'Programada',
        'asistencia' => null
    ]);
    
    return back()->with('exito', 'Estado de asistencia restablecido. Ya puedes corregirlo.');
}
// --- LÓGICA DEL ESTUDIANTE (ZONA PÚBLICA) ---

    // 1. Enviar los horarios al Calendario
    public function getHorariosJson()
    {
        $horarios = \App\Models\HorarioAsesoria::all();
        $eventos = [];
        
        // Mapeo para que FullCalendar entienda los días (Domingo=0, Lunes=1, etc.)
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
                'daysOfWeek' => [$diaNum], // Esto hace que la clase se repita todas las semanas
                'color' => '#002845', // Color azul oscuro institucional
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'lugar' => $h->lugar,
                    'docente' => $h->docente_nombre
                ]
            ];
        }
        
        return response()->json($eventos);
    }

    // 2. Guardar la reserva del estudiante
// 2. Guardar la reserva del estudiante (Con Validación de la U)
public function reservar(Request $request)
    {
        // 1. Buscamos al estudiante por su cédula (esto seguro ya lo tienes)
        $estudiante = \App\Models\Estudiante::where('cedula', $request->cedula)->first();

        if (!$estudiante) {
            return back()->with('error', '⛔ Cédula no encontrada en el sistema.');
        }

        // =========================================================
        // 2. NUEVO CANDADO: Evitar doble reserva en la misma clase
        // =========================================================
        $reservaExistente = \App\Models\Seguimiento::where('estudiante_id', $estudiante->id)
                                ->where('horario_id', $request->horario_id)
                                ->exists();

        if ($reservaExistente) {
            return back()->with('error', '⚠️ Ya tienes un cupo reservado para esta clase. No puedes inscribirte dos veces.');
        }
        // =========================================================

        // 3. Si pasa el candado, creamos la reserva normalmente
        \App\Models\Seguimiento::create([
            'horario_id' => $request->horario_id,
            'estudiante_id' => $estudiante->id,
            'fecha' => now()->toDateString(),
            'estado' => 'Programada'
        ]);

        return back()->with('exito', '✅ ¡Cupo reservado con éxito!');
    }

}
