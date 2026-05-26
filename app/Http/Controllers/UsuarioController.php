<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Estudiante;

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

    public function resetPassword($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $usuario = User::findOrFail($id);

        $tempPassword = 'Pascual' . rand(1000, 9999) . '!';

        $usuario->update([
            'password'        => Hash::make($tempPassword),
            'cambiar_password' => true,
        ]);

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Contraseña temporal asignada: ' . $tempPassword . ' — El usuario deberá cambiarla al ingresar.');
    }

    public function crearUsuario(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'rol'   => 'required|in:profesor,admin',
        ]);

        $tempPassword = 'Pascual' . rand(1000, 9999) . '!';

        User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($tempPassword),
            'rol'              => $request->rol,
            'cambiar_password' => true,
        ]);

        return redirect()->to(route('dashboard').'?tab=usuarios')
            ->with('exito', 'Usuario creado. Contraseña temporal: ' . $tempPassword . ' — Deberá cambiarla al iniciar sesión.');
    }

    public function crearEstudiante(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $request->validate([
            'nombre_completo'    => 'required|string|max:255',
            'cedula'             => 'required|string|max:20|unique:estudiantes,cedula',
            'correo'             => 'nullable|email|max:255|unique:estudiantes,correo',
            'programa_academico' => 'nullable|string|max:255',
        ]);

        Estudiante::create([
            'nombre_completo'    => $request->nombre_completo,
            'cedula'             => $request->cedula,
            'correo'             => $request->correo ?: null,
            'programa_academico' => $request->programa_academico,
            'esta_activo'        => true,
        ]);

        return redirect()->to(route('dashboard').'?tab=estudiantes')
            ->with('exito', 'Estudiante registrado correctamente.');
    }

    public function toggleEstudiante($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $estudiante = Estudiante::findOrFail($id);
        $estudiante->update(['esta_activo' => !$estudiante->esta_activo]);

        $estado = $estudiante->esta_activo ? 'activado' : 'desactivado';

        return redirect()->to(route('dashboard').'?tab=estudiantes')
            ->with('exito', "Estudiante {$estado} correctamente.");
    }

    public function actualizarEstudiante(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') abort(403);

        $estudiante = Estudiante::findOrFail($id);

        $request->validate([
            'nombre_completo'    => 'required|string|max:255',
            'cedula'             => 'required|string|max:20|unique:estudiantes,cedula,' . $id,
            'correo'             => 'nullable|email|max:255|unique:estudiantes,correo,' . $id,
            'programa_academico' => 'nullable|string|max:255',
        ]);

        $estudiante->update([
            'nombre_completo'    => $request->nombre_completo,
            'cedula'             => $request->cedula,
            'correo'             => $request->correo ?: null,
            'programa_academico' => $request->programa_academico,
        ]);

        return redirect()->to(route('dashboard').'?tab=estudiantes')
            ->with('exito', 'Datos del estudiante actualizados correctamente.');
    }

}