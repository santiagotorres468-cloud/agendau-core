<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CambiarPasswordController extends Controller
{
    public function mostrar()
    {
        return view('auth.cambiar_password');
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        auth()->user()->update([
            'password'         => Hash::make($request->password),
            'cambiar_password' => false,
        ]);

        return redirect()->route('dashboard')->with('exito', 'Contraseña actualizada correctamente. Bienvenido al sistema.');
    }
}
