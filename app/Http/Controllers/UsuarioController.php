<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        return redirect()->route('dashboard');
    }

    public function actualizarRol(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate([
            'rol' => 'required|in:admin,profesor,inactivo',
        ]);

        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id() && $request->rol !== 'admin') {
            return redirect()->to(route('dashboard').'?tab=usuarios')
                ->with('error', 'No puedes cambiar tu propio rol de administrador.');
        }

        $usuario->update(['rol' => $request->rol]);

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Rol actualizado correctamente.');
    }

    public function eliminarUsuario($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        if ((int)$id === auth()->id()) {
            return redirect()->to(route('dashboard').'?tab=usuarios')
                ->with('error', 'No puedes desactivarte a ti mismo.');
        }

        User::where('id', $id)->update(['rol' => 'inactivo']);

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Usuario desactivado y movido a inactivos.');
    }

    /**
     * Reactivar usuario inactivo con un rol específico.
     */
    public function reactivar(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate([
            'rol' => 'required|in:admin,profesor',
        ]);

        $usuario = User::findOrFail($id);
        $usuario->update(['rol' => $request->rol]);

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Usuario reactivado como "' . ucfirst($request->rol) . '".');
    }

    /**
     * Eliminar definitivamente de la base de datos.
     */
    public function destruir($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        if ((int)$id === auth()->id()) {
            return redirect()->to(route('dashboard').'?tab=usuarios')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        User::findOrFail($id)->delete();

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Usuario eliminado definitivamente.');
    }
}