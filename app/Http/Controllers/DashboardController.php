<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Intervention;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = auth()->user();
        
        // Construire la requête de base pour les techniciens accessibles
        $technicianQuery = Technician::query();
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $technicianQuery->whereHas('company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        
        // Construire la requête de base pour les entreprises accessibles
        $companyQuery = Company::query();
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            if (empty($organizationIds)) {
                // Si l'utilisateur n'a accès à aucune organisation, ne rien retourner
                $companyQuery->whereRaw('1 = 0');
            } else {
                $companyQuery->whereHas('organizations', function ($q) use ($organizationIds) {
                    $q->whereIn('organizations.id', $organizationIds);
                });
            }
        }
        
        // Construire la requête de base pour les interventions accessibles
        $interventionQuery = Intervention::query();
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $interventionQuery->whereHas('technician.company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        
        $totalInterventions = (clone $interventionQuery)->count();
        $completedInterventions = (clone $interventionQuery)->where('is_completed', true)->count();
        $pendingInterventions = $totalInterventions - $completedInterventions;
        $lateInterventions = (clone $interventionQuery)->where('was_late', true)->count();

        $stats = [
            'companies' => (clone $companyQuery)->count(),
            'technicians' => (clone $technicianQuery)->count(),
            'activeTechnicians' => (clone $technicianQuery)->where('is_active', true)->count(),
            'interventions' => $totalInterventions,
            'completedInterventions' => $completedInterventions,
            'pendingInterventions' => $pendingInterventions,
            'completionRate' => $totalInterventions > 0 ? round(($completedInterventions / $totalInterventions) * 100, 1) : null,
            'onTimeRate' => $totalInterventions > 0 ? round((1 - ($lateInterventions / $totalInterventions)) * 100, 1) : null,
        ];

        $upcomingInterventions = (clone $interventionQuery)
            ->with('technician.company')
            ->where('is_completed', false)
            ->whereDate('scheduled_at', '>=', Carbon::now()->startOfDay())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recentInterventions = (clone $interventionQuery)
            ->with('technician.company')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // Filtrer par entreprise si sélectionnée
        $topTechniciansQuery = clone $technicianQuery;
        if ($request->filled('company_id')) {
            $topTechniciansQuery->where('company_id', $request->get('company_id'));
        }
        
        $topTechnicians = $topTechniciansQuery
            ->with('company')
            ->withCount('interventions')
            ->withAvg('interventions as avg_service_note', 'service_note')
            ->withCount([
                'interventions as on_time_count' => fn ($q) => $q->where('was_late', false),
            ])
            ->get()
            ->map(function (Technician $technician) {
                $interventionCount = $technician->interventions_count;
                $volumeScore = min($interventionCount, 50) / 50; // cap à 50 interventions
                $averageNote = (float) ($technician->avg_service_note ?? 0);
                $punctualityRate = $interventionCount > 0 ? $technician->on_time_count / $interventionCount : 1;

                $punctualityPenalty = $punctualityRate < 0.75
                    ? (0.75 - $punctualityRate) * 0.25
                    : 0;

                $score = round((
                    ($volumeScore * 0.4) +
                    (($averageNote / 5) * 0.35) -
                    $punctualityPenalty
                ) * 100, 1);

                $technician->setAttribute('metrics', [
                    'average_note' => round($averageNote, 2),
                    'punctuality_rate' => round($punctualityRate * 100, 1),
                    'score' => $score,
                ]);

                return $technician;
            })
            ->sort(function (Technician $a, Technician $b) {
                $scoreComparison = $b->metrics['score'] <=> $a->metrics['score'];

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                return $b->metrics['punctuality_rate'] <=> $a->metrics['punctuality_rate'];
            })
            ->take(10);

        // Récupérer les entreprises pour le filtre
        $companies = (clone $companyQuery)->orderBy('name')->get();

        return view('dashboard', [
            'stats' => $stats,
            'upcomingInterventions' => $upcomingInterventions,
            'recentInterventions' => $recentInterventions,
            'topTechnicians' => $topTechnicians,
            'companies' => $companies,
        ]);
    }
}

