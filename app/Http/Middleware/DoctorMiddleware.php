<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorMiddleware
{
    /**
     * Manejar una solicitud entrante.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Permitir solo acceso a doctores
        if (Auth::user()->role === 'doctor') {
            return $next($request);
        }

        // Si no es doctor, devolver error 403
        abort(403, 'Acceso no autorizado. Solo los doctores pueden realizar esta acción.');
    }
}
