<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $tag->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('tags.edit', $tag) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Supprimer ce tag ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        @if($tag->color)
                            <span class="w-6 h-6 rounded-full border" style="background-color: {{ $tag->color }}"></span>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Nom du tag</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $tag->name }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-gray-800">{{ $tag->description ?? 'Aucune description' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Interventions associées ({{ $tag->interventions->count() }})</h3>
                        <a href="{{ route('interventions.index', ['tag' => $tag->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Filtrer les interventions
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technicien</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programmée le</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tag->interventions as $intervention)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <a href="{{ route('interventions.show', $intervention) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $intervention->title }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $intervention->technician?->full_name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $intervention->scheduled_at?->format('d/m/Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($intervention->is_completed)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                    Complète
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                                    Non terminée
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                            Aucune intervention associée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


