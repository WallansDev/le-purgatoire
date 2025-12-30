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
                            <p class="text-sm text-gray-500 mb-4">Sélectionnez les permissions que ce groupe accorde à ses membres.</p>

                            <div class="space-y-4">
                                @if(isset($permissions) && $permissions->count() > 0)
                                    @foreach($permissions->groupBy('resource') as $resource => $resourcePermissions)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <h4 class="text-md font-semibold text-gray-800 mb-3 capitalize">{{ $resource }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                @foreach($resourcePermissions as $permission)
                                                    <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $group->permissions->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                        <span class="ml-2 text-sm text-gray-700">{{ $permission->display_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <p class="text-sm text-yellow-800">Aucune permission disponible. Veuillez exécuter le seeder des permissions.</p>
                                    </div>
                                @endif
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

