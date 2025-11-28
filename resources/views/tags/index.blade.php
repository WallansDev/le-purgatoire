<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tags') }}
            </h2>
            <a href="{{ route('tags.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouveau tag
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
                    <form method="GET" action="{{ route('tags.index') }}" class="mb-4">
                        <div class="flex gap-4">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un tag..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Rechercher</button>
                            @if(request('search'))
                                <a href="{{ route('tags.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Réinitialiser</a>
                            @endif
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interventions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tags as $tag)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                @if($tag->color)
                                                    <span class="w-3 h-3 rounded-full border" style="background-color: {{ $tag->color }}"></span>
                                                @endif
                                                <a href="{{ route('tags.show', $tag) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                    {{ $tag->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ \Illuminate\Support\Str::limit($tag->description, 80) ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $tag->interventions_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('tags.show', $tag) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                            <a href="{{ route('tags.edit', $tag) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Supprimer ce tag ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Aucun tag enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


