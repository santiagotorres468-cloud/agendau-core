<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index() {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function actualizarRol(Request $request, $id) {
        if (auth()->user()->rol !== 'admin') { abort(403); }
        $usuario = User::findOrFail($id);
        $usuario->update(['rol' => $request->rol]);
        return back()->with('exito', '✅ Rol actualizado correctamente.');
    }

    
    public function eliminarUsuario($id) {
        if (auth()->user()->rol !== 'admin') { abort(403); }
        
        // 🔥 FORZAMOS LA ACTUALIZACIÓN DIRECTA PARA EVITAR BLOQUEOS DE LARAVEL
        User::where('id', $id)->update(['rol' => 'inactivo']);

        return back()->with('exito', '✅ Usuario DESACTIVADO (enviado a inactivos).');
    }

    public function reactivar(Request $request, $id) {
        if (auth()->user()->rol !== 'admin') { abort(403); }
        
        $usuario = User::findOrFail($id);
        $usuario->rol = $request->rol ?? 'profesor';
        $usuario->save();

        return back()->with('exito', '✅ Usuario REACTIVADO.');
    }

    public function destruir($id) {
        if (auth()->user()->rol !== 'admin') { abort(403); }
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return back()->with('exito', '✅ Usuario borrado definitivamente.');
    }
}