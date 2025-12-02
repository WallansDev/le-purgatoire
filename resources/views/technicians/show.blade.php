<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du technicien') }}
            </h2>
            <div>
                <a href="{{ route('technicians.edit', $technician) }}"
                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('technicians.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ $technician->full_name }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Entreprise</p>
                            <p class="text-gray-900">
                                @if ($technician->company)
                                    <a href="{{ route('companies.show', $technician->company) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ $technician->company->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $technician->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="text-gray-900">{{ $technician->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Note moyenne des interventions</p>
                            <p class="text-gray-900">
                                @if (!is_null($technician->average_rating))
                                    {{ number_format($technician->average_rating, 1) }}/5
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $technician->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $technician->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Taux de ponctualité</p>
                            <p class="text-gray-900">{{ number_format($technician->punctuality_rate * 100, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Interventions ({{ $technician->interventions->count() }})</h3>
                        <a href="{{ route('interventions.create') }}"
                            class="bg-indigo-500 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded">
                            Ajouter une intervention
                        </a>
                    </div>
                    @if ($technician->interventions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Titre
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Programmée le
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Commencée le
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Terminée le
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Note
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Retard
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($technician->interventions as $intervention)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('interventions.show', $intervention) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    {{ $intervention->title }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $intervention->scheduled_at?->format('d/m/Y H:i') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $intervention->started_at?->format('d/m/Y H:i') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $intervention->finished_at?->format('d/m/Y H:i') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if (!is_null($intervention->note))
                                                    {{ $intervention->note }}/5
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($intervention->was_late)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        {{ $intervention->delay_minutes }} min
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        À l'heure
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Aucune intervention pour ce technicien.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
