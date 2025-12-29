<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Non authentifié.');
        }

        // Le propriétaire a tous les droits
        if ($user->isOwner()) {
            return $next($request);
        }

        // Vérifier la permission selon le type
        $hasPermission = match($permission) {
            'companies.read' => $user->canReadCompanies(),
            'companies.write' => $user->canWriteCompanies(),
            'companies.delete' => $user->canDeleteCompanies(),
            'technicians.read' => $user->canReadTechnicians(),
            'technicians.write' => $user->canWriteTechnicians(),
            'technicians.delete' => $user->canDeleteTechnicians(),
            'interventions.read' => $user->canReadInterventions(),
            'interventions.write' => $user->canWriteInterventions(),
            'interventions.delete' => $user->canDeleteInterventions(),
            'groups.read' => $user->canReadGroups(),
            'groups.write' => $user->canWriteGroups(),
            'groups.delete' => $user->canDeleteGroups(),
            'organizations.read' => $user->canReadOrganizations(),
            'organizations.write' => $user->canWriteOrganizations(),
            'organizations.delete' => $user->canDeleteOrganizations(),
            default => false,
        };

        if (!$hasPermission) {
            abort(403, 'Vous n\'avez pas la permission d\'effectuer cette action.');
        }

        return $next($request);
    }
}

