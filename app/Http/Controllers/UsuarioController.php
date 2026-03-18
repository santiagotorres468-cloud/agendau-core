<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function actualizarRol(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, '⛔ Solo los administradores pueden cambiar roles.');
        }

        $request->validate(['rol' => 'required|in:admin,profesor']);
        
        $usuario = User::findOrFail($id);
        $usuario->rol = $request->rol;
        $usuario->save(); 

        return back()->with('exito', '✅ Rol de ' . $usuario->name . ' actualizado a ' . strtoupper($request->rol));
    }

    public function eliminarUsuario($id)
    {
        if (auth()->user()->rol !== 'admin') { abort(403); }

        $usuario = User::findOrFail($id);

        // 🛡️ Seguridad: No dejar que te borres a ti mismo
        if ($usuario->id === auth()->id()) {
            return back()->with('error', '⛔ No puedes desactivar tu propia cuenta.');
        }

        $usuario->delete();
        return back()->with('exito', '🗑️ El usuario ha sido eliminado correctamente.');
    }
}