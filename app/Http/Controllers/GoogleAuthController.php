<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Estudiante;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // 🛡️ Validar que sea del Pascual Bravo
            if (!str_ends_with($googleUser->email, '@pascualbravo.edu.co')) {
                return redirect('/')->with('error', 'Acceso denegado. Solo puedes ingresar con tu correo institucional (@pascualbravo.edu.co).');
            }

            $estudiante = Estudiante::where('correo', $googleUser->email)->first();

            // Si no existe, se crea automáticamente con los datos de Google
            if (!$estudiante) {
                $estudiante = Estudiante::create([
                    'nombre_completo' => $googleUser->name,
                    'correo'          => $googleUser->email,
                    'cedula'          => 'GOOGLE-' . uniqid(),
                    'esta_activo'     => true,
                ]);
            }

            if (!$estudiante->esta_activo) {
                return redirect('/')->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
            }

            session([
                'estudiante_id'     => $estudiante->id,
                'estudiante_nombre' => $estudiante->nombre_completo,
            ]);

            return redirect('/estudiante')->with('exito', 'Sesión iniciada correctamente. ¡Bienvenido a tu panel!');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Ocurrió un error al conectar con Google.');
        }
    }

    // 🔥 FUNCIÓN EXCLUSIVA PARA VINCULAR LA CÉDULA 🔥
    public function vincular(Request $request)
    {
        $request->validate(['cedula' => 'required|string|max:20']);

        $correo_pendiente = session('correo_pendiente');
        
        if (!$correo_pendiente) {
            return redirect('/')->with('error', 'No hay ninguna cuenta pendiente por vincular.');
        }

        $estudiante = Estudiante::where('cedula', $request->cedula)->first();

        // 🛡️ SEGURIDAD: Si el estudiante no existe, se rechaza
        if (!$estudiante) {
            return back()->with('error', 'Cédula no encontrada. Debes estar registrado en la base de datos de la institución.');
        }

        // Si existe, lo vinculamos
        $estudiante->update(['correo' => $correo_pendiente]);

        // Iniciamos su sesión
        session([
            'estudiante_id' => $estudiante->id,
            'estudiante_nombre' => $estudiante->nombre_completo
        ]);
        session()->forget(['correo_pendiente', 'google_nombre']);

        return redirect('/estudiante')->with('exito', 'Identidad verificada. Tu cuenta ha sido vinculada correctamente.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['estudiante_id', 'estudiante_nombre', 'correo_pendiente', 'google_nombre']);
        return redirect('/')->with('exito', 'Has cerrado sesión correctamente.');
    }
}