<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\EncuestaController;
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
    if (session('estudiante_id')) {
        $pendiente = \App\Models\Seguimiento::where('estudiante_id', session('estudiante_id'))
            ->where('estado', 'Evaluada')
            ->where('asistencia', true)
            ->where('encuesta_respondida', false)
            ->first();

        if ($pendiente) {
            return redirect()->route('encuesta.mostrar', $pendiente->id);
        }
    }
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

// --- ENCUESTA POST-TUTORÍA (acceso por sesión de estudiante, no por Sanctum) ---
Route::get('/encuesta/{id}', [EncuestaController::class, 'mostrar'])->name('encuesta.mostrar');
Route::post('/encuesta/{id}', [EncuestaController::class, 'guardar'])->name('encuesta.guardar');

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

        if ($user->rol === 'admin') {
            $horarios = \App\Models\HorarioAsesoria::withCount('seguimientos')->orderBy('dia_semana')->get();
            $usuarios = \App\Models\User::all();
            return view('dashboard', compact('horarios', 'usuarios'));
        }

        $horarios = \App\Models\HorarioAsesoria::where('user_id', $user->id)
                        ->withCount('seguimientos')
                        ->orderBy('dia_semana')
                        ->get();
        $usuarios = collect();
        return view('admin.dashboard', compact('horarios', 'usuarios'));
    })->name('dashboard');

    // --- 2. GESTIÓN DE HORARIOS Y ASISTENCIA (Docentes y Admins) ---
    Route::controller(HorarioController::class)->group(function () {
        
        // 🔥 VACIAR CLASES (Debe ir ANTES de las rutas con {id}) 🔥
        Route::delete('/horarios/vaciar/base-de-datos', 'vaciarClases')->name('horarios.vaciar');
        
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

        // CRUD de Horarios
        Route::post('/horarios/importar', 'importar')->name('horarios.importar');
        Route::get('/horarios/{id}/editar', 'editar')->name('horarios.editar');
        Route::put('/horarios/{id}', 'actualizar')->name('horarios.actualizar');
        Route::delete('/horarios/{id}', 'eliminar')->name('horarios.eliminar');

        // Reportes Globales PDF
        Route::get('/reportes/docente', [EncuestaController::class, 'reporteDocente'])->name('reportes.docente');
        Route::get('/reportes/curso', [EncuestaController::class, 'reporteCurso'])->name('reportes.curso');
    });

    // --- Encuestas (EncuestaController) ---
    Route::get('/admin/encuestas', [EncuestaController::class, 'dashboard'])->name('admin.encuestas');

    // --- 3. CONTROL DE ROLES Y USUARIOS (Solo Admin) ---
    Route::controller(UsuarioController::class)->group(function () {
        Route::get('/usuarios', 'index')->name('usuarios.index');
        Route::put('/usuarios/{id}/rol', 'actualizarRol')->name('usuarios.actualizarRol');
        Route::delete('/usuarios/{id}', 'eliminarUsuario')->name('usuarios.eliminar'); 
        Route::put('/usuarios/{id}/reactivar', 'reactivar')->name('usuarios.reactivar');
        Route::delete('/usuarios/{id}/destruir', 'destruir')->name('usuarios.destruir');
    });

});