<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    // Mostrar la lista de usuarios
    public function index()
    {
        // Doble seguridad: Si alguien adivina la URL y no es admin, lo expulsamos
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        $usuarios = User::all();
        return view('admin.usuarios', compact('usuarios'));
    }

    // Guardar el nuevo rol en la base de datos
    public function actualizarRol(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso denegado.');
        }

        $usuario = User::findOrFail($id);
        
        // Protección: Evitar que el Super Admin (ID 1) se quite sus propios poderes por error
        if ($usuario->id === 1 && $request->rol !== 'admin') {
            return back()->with('error', 'No puedes quitarle los privilegios al administrador principal del sistema.');
        }

        $usuario->update([
            'rol' => $request->rol
        ]);

        return back()->with('exito', 'El rol del usuario ha sido actualizado correctamente.');
    }
}