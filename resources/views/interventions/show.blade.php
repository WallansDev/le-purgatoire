<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'intervention') }}
            </h2>
            <div>
                <a href="{{ route('interventions.edit', $intervention) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('interventions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ $intervention->title }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Technicien</p>
                            <p class="text-gray-900">
                                @if($intervention->technician)
                                    <a href="{{ route('technicians.show', $intervention->technician) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $intervention->technician->full_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Entreprise</p>
                            <p class="text-gray-900">
                                @if($intervention->technician?->company)
                                    <a href="{{ route('companies.show', $intervention->technician->company) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $intervention->technician->company->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Programmée le</p>
                            <p class="text-gray-900">{{ $intervention->scheduled_at?->format('d/m/Y à H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Commencée le</p>
                            <p class="text-gray-900">{{ $intervention->started_at?->format('d/m/Y à H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Terminée le</p>
                            <p class="text-gray-900">{{ $intervention->finished_at?->format('d/m/Y à H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Note</p>
                            <p class="text-gray-900">
                                @if($intervention->note)
                                    {{ $intervention->note }}/5
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        @if($intervention->address)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Adresse</p>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $intervention->address }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Retard</p>
                            <p class="text-gray-900">
                                @if($intervention->was_late)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $intervention->delay_minutes }} minutes
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        À l'heure
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <p class="text-gray-900">
                                @if($intervention->is_completed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Complète
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Non terminée
                                    </span>
                                @endif
                            </p>
                        </div>
                        @if($intervention->tags->isNotEmpty())
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Tags</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($intervention->tags as $tag)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border" style="border-color: {{ $tag->color ?? '#c7d2fe' }}; color: {{ $tag->color ?? '#4f46e5' }}">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($intervention->description)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Description</p>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $intervention->description }}</p>
                        </div>
                    @endif

                    @if(!$intervention->is_completed && $intervention->non_completion_reason)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Raison de non-complétion</p>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $intervention->non_completion_reason }}</p>
                        </div>
                    @endif

                    @if($intervention->notes)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Notes concernant l'intervention</p>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $intervention->notes }}</p>
                        </div>
                    @endif

                    @if($intervention->client_comments)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Commentaires du client</p>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $intervention->client_comments }}</p>
                        </div>
                    @endif

                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

