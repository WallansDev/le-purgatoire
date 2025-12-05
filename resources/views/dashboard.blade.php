<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <span class="text-sm text-gray-500">
                Dernière mise à jour {{ now()->translatedFormat('d F Y H:i') }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @php
                    $cards = [
                        [
                            'title' => 'Interventions',
                            'value' => $stats['interventions'],
                            'subtitle' => $stats['completedInterventions'].' complétées',
                        ],
                        [
                            'title' => 'Entreprises',
                            'value' => $stats['companies'],
                            'subtitle' => 'Techniciens : '.$stats['technicians'],
                        ],
                        [
                            'title' => 'Complétion',
                            'value' => $stats['completionRate'] !== null ? $stats['completionRate'].'%' : '—',
                            'subtitle' => $stats['pendingInterventions'].' non terminées',
                        ],
                        [
                            'title' => 'Ponctualité',
                            'value' => $stats['onTimeRate'] !== null ? $stats['onTimeRate'].'%' : '—',
                            'subtitle' => $stats['activeTechnicians'].' techniciens actifs',
                        ],
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <p class="text-sm font-medium text-gray-500">{{ $card['title'] }}</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $card['value'] }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ $card['subtitle'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Interventions à venir</h3>
                        <a href="{{ route('interventions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir tout</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($upcomingInterventions as $intervention)
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">{{ $intervention->scheduled_at?->format('d/m/Y H:i') ?? 'Non planifiée' }}</p>
                                        <p class="text-base font-semibold text-gray-900">{{ $intervention->title }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $intervention->technician?->full_name ?? 'Technicien non assigné' }}
                                            @if($intervention->technician?->company)
                                                • {{ $intervention->technician->company->name }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Programmée
                                    </span>
                                </div>
                                @if($intervention->non_completion_reason)
                                    <p class="mt-3 text-sm text-gray-500">
                                        {{ \Illuminate\Support\Str::limit($intervention->non_completion_reason, 120) }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Aucune intervention à venir.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Dernières activités</h3>
                        <a href="{{ route('interventions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir tout</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentInterventions as $intervention)
                            <div class="flex items-start gap-4">
                                <div class="mt-1">
                                    @if($intervention->is_completed)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            Complète
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">
                                            Non terminée
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-base font-semibold text-gray-900">
                                        <a href="{{ route('interventions.show', $intervention) }}" class="hover:text-indigo-600">
                                            {{ $intervention->title }}
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Mise à jour le {{ $intervention->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                        — {{ $intervention->technician?->full_name ?? 'Technicien inconnu' }}
                                    </p>
                                    @if($intervention->notes)
                                        <p class="mt-2 text-sm text-gray-600">
                                            {{ \Illuminate\Support\Str::limit($intervention->notes, 120) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Pas encore d’activité.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Top 10 Meilleurs techniciens</h3>
                    <a href="{{ route('technicians.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Gérer les techniciens</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Technicien</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Interventions</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Note service moy.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ponctualité</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Score</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $id_top10 = 0 @endphp
                            @forelse($topTechnicians as $technician)
                                @php $id_top10++ @endphp
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <a class="hover:text-indigo-600">
                                                {{ $id_top10}}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <a href="{{ route('technicians.show', $technician) }}" class="hover:text-indigo-600">
                                                {{ $technician->full_name }}
                                            </a>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            {{ $technician->company->name ?? 'Entreprise inconnue' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ $technician->interventions_count }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ number_format($technician->metrics['average_note'], 2) }}/5
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-700">{{ $technician->metrics['punctuality_rate'] }}%</span>
                                            <div class="w-24 h-2 bg-gray-100 rounded-full">
                                                <div class="h-2 bg-indigo-500 rounded-full" style="width: {{ $technician->metrics['punctuality_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
                                            {{ $technician->metrics['score'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
                                        Pas encore de données suffisantes pour établir un classement.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


