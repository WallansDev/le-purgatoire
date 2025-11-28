<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier s'il y a des utilisateurs dans la base de données
        $userCount = User::count();

        // Routes à exclure du middleware (routes système)
        $excludedRoutes = ['up', 'owner.setup.*'];

        // Si aucun utilisateur n'existe et que ce n'est pas une route exclue
        if ($userCount === 0 && !$request->routeIs($excludedRoutes)) {
            return redirect()->route('owner.setup.create');
        }

        // Si un owner existe déjà et qu'on essaie d'accéder à la page de setup
        if ($userCount > 0 && $request->routeIs('owner.setup.*')) {
            abort(404);
        }

        return $next($request);
    }
}

