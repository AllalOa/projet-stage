<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté possède le(s) rôle(s) requis.
     * Usage dans les routes : middleware('role:postulant') ou middleware('role:admin,president-council')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Accès non autorisé. Votre rôle ne permet pas d\'accéder à cette page.');
        }

        return $next($request);
    }
}
