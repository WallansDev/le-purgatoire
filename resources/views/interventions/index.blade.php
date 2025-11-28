<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Interventions') }}
            </h2>
            <a href="{{ route('interventions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouvelle intervention
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('interventions.index') }}" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Rechercher par technicien ou client..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <input type="date" 
                                       name="date_search" 
                                       value="{{ request('date_search') }}" 
                                       placeholder="Date de programmation..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <select name="tag" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Filtrer par tag</option>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ (string) request('tag') === (string) $tag->id ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Rechercher
                                </button>
                                @if(request('search') || request('date_search') || request('tag'))
                                    <a href="{{ route('interventions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Réinitialiser
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Titre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Technicien
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Entreprise
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Programmée le
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tags
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Complétion
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Note
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($interventions as $intervention)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $intervention->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($intervention->technician)
                                                    <a href="{{ route('technicians.show', $intervention->technician) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $intervention->technician->full_name }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($intervention->technician?->company)
                                                    <a href="{{ route('companies.show', $intervention->technician->company) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $intervention->technician->company->name }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ $intervention->scheduled_at?->format('d/m/Y H:i') ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($intervention->tags as $tag)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border" style="border-color: {{ $tag->color ?? '#c7d2fe' }}; color: {{ $tag->color ?? '#4f46e5' }}">
                                                        {{ $tag->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($intervention->finished_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Terminée
                                                </span>
                                            @elseif($intervention->started_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En cours
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Programmée
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($intervention->is_completed)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                    Complète
                                                </span>
                                            @else
                                                <div>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                                        Non terminée
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($intervention->note !== null)
                                                    {{ $intervention->note }}/5
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('interventions.show', $intervention) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                            <a href="{{ route('interventions.edit', $intervention) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                            <form action="{{ route('interventions.destroy', $intervention) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette intervention ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                            Aucune intervention trouvée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $interventions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

