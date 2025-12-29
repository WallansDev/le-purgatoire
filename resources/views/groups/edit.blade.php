<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le groupe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('groups.update', $group) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="organization_id" :value="__('Organisation *')" />
                                <select id="organization_id" name="organization_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Sélectionner une organisation</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ old('organization_id', $group->organization_id) == $organization->id ? 'selected' : '' }}>
                                            {{ $organization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Nom *')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $group->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $group->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $group->is_default) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Groupe par défaut</span>
                                </label>
                                <p class="mt-1 text-sm text-gray-500">Le groupe par défaut sera automatiquement assigné aux nouveaux membres de l'organisation</p>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions du groupe</h3>
                            <p class="text-sm text-gray-500 mb-4">Définissez les permissions que ce groupe accorde à ses membres pour chaque ressource.</p>
                            
                            @php
                                $resources = [
                                    'companies' => 'Entreprises',
                                    'technicians' => 'Techniciens',
                                    'interventions' => 'Interventions',
                                    'organizations' => 'Organisations',
                                    'groups' => 'Groupes',
                                ];
                            @endphp
                            
                            <div class="space-y-6">
                                @foreach($resources as $resource => $label)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $label }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <input type="hidden" name="{{ $resource }}_read" value="0">
                                                <input type="checkbox" name="{{ $resource }}_read" value="1" {{ old($resource . '_read', $group->{$resource . '_read'}) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Lecture</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <input type="hidden" name="{{ $resource }}_write" value="0">
                                                <input type="checkbox" name="{{ $resource }}_write" value="1" {{ old($resource . '_write', $group->{$resource . '_write'}) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Modification</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <input type="hidden" name="{{ $resource }}_delete" value="0">
                                                <input type="checkbox" name="{{ $resource }}_delete" value="1" {{ old($resource . '_delete', $group->{$resource . '_delete'}) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Suppression</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">Utilisateurs</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-3">
                                        <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                            <input type="hidden" name="can_invite" value="0">
                                            <input type="checkbox" name="can_invite" value="1" {{ old('can_invite', $group->can_invite) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">Inviter des utilisateurs</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('groups.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
                            <x-primary-button>
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

