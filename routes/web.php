<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\RateLimiter; 
use Illuminate\Cache\RateLimiting\Limit;     
use Illuminate\Http\Request;                 

// ========================================================
// 🛡️ FIX SEGURIDAD: LIMITADOR DE INTENTOS DE LOGIN
// ========================================================
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email.$request->ip());
});

// ========================================================
// 🌍 ZONA PÚBLICA: Pantallas Abiertas y Estudiantes
// ========================================================
Route::get('/', function () {
    return view('welcome'); 
})->name('inicio');

Route::get('/estudiante', function () {
    return view('estudiante');
})->name('estudiante.panel');

// --- RUTAS DE GOOGLE AUTH ---
Route::controller(GoogleAuthController::class)->group(function () {
    Route::get('/auth/google/redirect', 'redirect')->name('google.login');
    Route::get('/auth/google/callback', 'callback');
    Route::post('/estudiante/vincular', 'vincular')->name('estudiante.vincular');
    Route::post('/estudiante/logout', 'logout')->name('estudiante.logout');
});

// --- RUTAS PÚBLICAS DE HORARIOS (Estudiantes) ---
Route::controller(HorarioController::class)->group(function () {
    Route::get('/api/horarios', 'getHorariosJson')->name('api.horarios');
    Route::get('/api/mis-horarios', 'misHorariosJson')->name('api.mis.horarios');
    Route::post('/reservar', 'reservar')->name('reservar.clase');
    Route::delete('/estudiante/reservas/{id}', 'cancelarReservaEstudiante')->name('estudiante.reservas.cancelar');
    Route::post('/estudiante/reservas/cancelar-publico', 'cancelarDesdePublico')->name('estudiante.cancelar.publico');
});

// ========================================================
// 🔐 ZONA PRIVADA: Requiere inicio de sesión (Docentes y Admin)
// ========================================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // --- 1. DASHBOARD DINÁMICO ---
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $usuarios = []; 
        if ($user->rol === 'admin') {
            $horarios = \App\Models\HorarioAsesoria::orderBy('dia_semana')->get();
            $usuarios = \App\Models\User::all(); // 🔥 Traemos a TODOS
        } else {
            $horarios = \App\Models\HorarioAsesoria::where('user_id', $user->id)->orderBy('dia_semana')->get();
        }
        
        // 💡 NOTA: Si moviste tu archivo de vista a la carpeta 'admin', 
        // deberías cambiar esto a view('admin.dashboard');
        return view('dashboard', compact('horarios', 'usuarios'));
    })->name('dashboard');

    // --- 2. GESTIÓN DE HORARIOS Y ASISTENCIA (Docentes y Admins) ---
    Route::controller(HorarioController::class)->group(function () {
        // Asistencia
        Route::get('/horarios/{id}/estudiantes', 'verEstudiantes')->name('horarios.estudiantes');
        Route::put('/reservas/{id}/asistencia', 'marcarAsistencia')->name('reservas.asistencia');
        Route::put('/reservas/{id}/corregir', 'corregirAsistencia')->name('reservas.corregir');
        Route::delete('/reservas/{id}', 'eliminarReserva')->name('reservas.eliminar');
        Route::get('/horarios/{id}/pdf', 'generarPdf')->name('horarios.pdf');
        Route::post('/reservas/{id}/reporte', 'generarReporteIndividual')->name('reservas.reporte');

        // Seguimiento
        Route::get('/seguimiento', 'seguimientoIndex')->name('seguimiento.index');
        Route::get('/seguimiento/buscar', 'seguimientoBuscar')->name('seguimiento.buscar');

        // CRUD de Horarios (Solo Admin)
        Route::post('/horarios/importar', 'importar')->name('horarios.importar');
        Route::get('/horarios/{id}/editar', 'editar')->name('horarios.editar');
        Route::put('/horarios/{id}', 'actualizar')->name('horarios.actualizar');
        Route::delete('/horarios/{id}', 'eliminar')->name('horarios.eliminar');
    });

    // --- 3. CONTROL DE ROLES Y USUARIOS (Solo Admin) ---
    Route::controller(UsuarioController::class)->group(function () {
        Route::get('/usuarios', 'index')->name('usuarios.index');
        Route::put('/usuarios/{id}/rol', 'actualizarRol')->name('usuarios.actualizarRol');
        Route::delete('/usuarios/{id}', 'eliminarUsuario')->name('usuarios.eliminar'); // 🔥 Dejamos solo uno
    });

});