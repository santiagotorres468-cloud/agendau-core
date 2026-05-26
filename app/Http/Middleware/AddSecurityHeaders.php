<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Alpine.js y SweetAlert2 usan new Function() internamente → unsafe-eval requerido
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' cdn.tailwindcss.com fonts.googleapis.com fonts.bunny.net",
            "font-src 'self' fonts.googleapis.com fonts.gstatic.com fonts.bunny.net data:",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-src 'self' calendar.google.com",
            "object-src 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
