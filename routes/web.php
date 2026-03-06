<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\UsuarioController;

// ========================================================
// 🌍 ZONA PÚBLICA: Pantalla del Estudiante
// ========================================================

Route::get('/', function () {
    return view('welcome'); 
})->name('inicio');

// Ruta que el calendario lee para dibujar los cuadros de colores
Route::get('/api/horarios', [App\Http\Controllers\HorarioController::class, 'getHorariosJson'])->name('api.horarios');

// Ruta donde el estudiante envía su formulario de reserva
Route::post('/reservar', [App\Http\Controllers\HorarioController::class, 'reservar'])->name('reservar.clase');


// ========================================================
// 🔐 ZONA PRIVADA: Requiere inicio de sesión
// ========================================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // --- 1. DASHBOARD DINÁMICO (FILTRADO POR ROL) ---
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // SEGURIDAD: Si es admin, ve TODO. 
        // Si es profesor, aplicamos filtro de user_id para evitar que vea clases ajenas.
        if ($user->rol === 'admin') {
            $horarios = \App\Models\HorarioAsesoria::orderBy('dia_semana')->get();
        } else {
            $horarios = \App\Models\HorarioAsesoria::where('user_id', $user->id)
                                                   ->orderBy('dia_semana')
                                                   ->get();
        }

        return view('dashboard', compact('horarios'));
    })->name('dashboard');

    // --- 2. GESTIÓN DE ASISTENCIA (DOCENTES Y ADMIN) ---
    // Ver lista de estudiantes de una clase
    Route::get('/horarios/{id}/estudiantes', [HorarioController::class, 'verEstudiantes'])->name('horarios.estudiantes');
    
    // Marcar si asistió o no
    Route::put('/reservas/{id}/asistencia', [HorarioController::class, 'marcarAsistencia'])->name('reservas.asistencia');
    
    // Corregir error de dedo en la asistencia
    Route::put('/reservas/{id}/corregir', [HorarioController::class, 'corregirAsistencia'])->name('reservas.corregir');

    // Eliminar una reserva (Remover estudiante de la lista)
    Route::delete('/reservas/{id}', [HorarioController::class, 'eliminarReserva'])->name('reservas.eliminar');


    // --- 3. PODERES ADMINISTRATIVOS (SOLO ADMIN) ---
    // Laravel permite proteger rutas específicas dentro del grupo
    Route::middleware(['can:admin-only'])->group(function () {
        // Importar datos desde Excel
        Route::post('/horarios/importar', [HorarioController::class, 'importar'])->name('horarios.importar');
        
        // CRUD de Horarios
        Route::get('/horarios/{id}/editar', [HorarioController::class, 'editar'])->name('horarios.editar');
        Route::put('/horarios/{id}', [HorarioController::class, 'actualizar'])->name('horarios.actualizar');
        Route::delete('/horarios/{id}', [HorarioController::class, 'eliminar'])->name('horarios.eliminar');

        // Gestión de Roles de Usuarios
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::put('/usuarios/{id}/rol', [UsuarioController::class, 'actualizarRol'])->name('usuarios.actualizarRol');

        
    });

});