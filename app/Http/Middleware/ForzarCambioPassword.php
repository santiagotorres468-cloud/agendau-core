<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForzarCambioPassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->cambiar_password) {
            if (!$request->routeIs('password.forzar.*') && !$request->routeIs('logout')) {
                return redirect()->route('password.forzar.mostrar');
            }
        }

        return $next($request);
    }
}
