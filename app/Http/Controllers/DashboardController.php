<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Intervention;
use App\Models\Technician;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalInterventions = Intervention::count();
        $completedInterventions = Intervention::where('is_completed', true)->count();
        $pendingInterventions = $totalInterventions - $completedInterventions;
        $lateInterventions = Intervention::where('was_late', true)->count();

        $stats = [
            'companies' => Company::count(),
            'technicians' => Technician::count(),
            'activeTechnicians' => Technician::where('is_active', true)->count(),
            'interventions' => $totalInterventions,
            'completedInterventions' => $completedInterventions,
            'pendingInterventions' => $pendingInterventions,
            'completionRate' => $totalInterventions > 0 ? round(($completedInterventions / $totalInterventions) * 100, 1) : null,
            'onTimeRate' => $totalInterventions > 0 ? round((1 - ($lateInterventions / $totalInterventions)) * 100, 1) : null,
        ];

        $upcomingInterventions = Intervention::with('technician.company')
            ->where('is_completed', false)
            ->whereDate('scheduled_at', '>=', Carbon::now()->startOfDay())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recentInterventions = Intervention::with('technician.company')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $topTechnicians = Technician::with('company')
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

        return view('dashboard', [
            'stats' => $stats,
            'upcomingInterventions' => $upcomingInterventions,
            'recentInterventions' => $recentInterventions,
            'topTechnicians' => $topTechnicians,
        ]);
    }
}


