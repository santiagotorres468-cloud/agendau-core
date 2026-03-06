<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\RateLimiter; // <-- IMPORTANTE
use Illuminate\Cache\RateLimiting\Limit;     // <-- IMPORTANTE
use Illuminate\Http\Request;                 // <-- IMPORTANTE

// ========================================================
// 🛡️ FIX SEGURIDAD: LIMITADOR DE INTENTOS DE LOGIN
// ========================================================
// Forzamos el limitador aquí para que el sistema de autenticación lo encuentre inmediatamente.
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email.$request->ip());
});

// ========================================================
// 🌍 ZONA PÚBLICA: Pantalla del Estudiante
// ========================================================

Route::get('/', function () {
    return view('welcome'); 
})->name('inicio');

// Ruta para el Panel de Gestión del Estudiante
Route::get('/estudiante', function () {
    return view('estudiante');
})->name('estudiante.panel');

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
    Route::get('/horarios/{id}/estudiantes', [HorarioController::class, 'verEstudiantes'])->name('horarios.estudiantes');
    Route::put('/reservas/{id}/asistencia', [HorarioController::class, 'marcarAsistencia'])->name('reservas.asistencia');
    Route::put('/reservas/{id}/corregir', [HorarioController::class, 'corregirAsistencia'])->name('reservas.corregir');
    Route::delete('/reservas/{id}', [HorarioController::class, 'eliminarReserva'])->name('reservas.eliminar');
    Route::get('/horarios/{id}/pdf', [HorarioController::class, 'generarPdf'])->name('horarios.pdf');
    Route::post('/reservas/{id}/reporte', [HorarioController::class, 'generarReporteIndividual'])->name('reservas.reporte');

    // --- 3. MÓDULO DE SEGUIMIENTO INDIVIDUAL (DOCENTES Y ADMIN) ---
    Route::get('/seguimiento', [HorarioController::class, 'seguimientoIndex'])->name('seguimiento.index');
    Route::get('/seguimiento/buscar', [HorarioController::class, 'seguimientoBuscar'])->name('seguimiento.buscar');

    // --- 4. PODERES ADMINISTRATIVOS (SOLO ADMIN) ---
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