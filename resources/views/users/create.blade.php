<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="first_name" :value="__('Prénom *')" />
                                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="last_name" :value="__('Nom *')" />
                                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="email" :value="__('Email *')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Téléphone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="password" :value="__('Mot de passe initial *')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmation du mot de passe *')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            </div>
                        </div>

                        <div class="flex items-center">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Administrateur</span>
                            </label>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Groupes</h3>
                            <p class="text-sm text-gray-500 mb-4">Sélectionnez les groupes auxquels cet utilisateur doit appartenir. Les permissions sont définies au niveau du groupe.</p>
                            
                            @php
                                $groupsByOrganization = $groups->groupBy('organization_id');
                            @endphp
                            
                            @foreach($organizations as $organization)
                                @if($groupsByOrganization->has($organization->id))
                                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                                        <h4 class="font-semibold text-gray-800 mb-3">{{ $organization->name }}</h4>
                                        <div class="space-y-3">
                                            @foreach($groupsByOrganization[$organization->id] as $group)
                                                <div class="border border-gray-200 rounded p-3">
                                                    <label class="flex items-start">
                                                        <input type="checkbox" 
                                                               name="group_ids[]" 
                                                               value="{{ $group->id }}"
                                                               class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                               {{ old("group_ids") && in_array($group->id, old("group_ids")) ? 'checked' : '' }}>
                                                        <div class="ml-3 flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-medium text-gray-900">{{ $group->name }}</span>
                                                                @if($group->is_default)
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Par défaut</span>
                                                                @endif
                                                            </div>
                                                            @if($group->description)
                                                                <p class="text-sm text-gray-500 mt-1">{{ $group->description }}</p>
                                                            @endif
                                                            <div class="mt-2 flex gap-2 flex-wrap">
                                                                @if($group->can_read)
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lecture</span>
                                                                @endif
                                                                @if($group->can_write)
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Écriture</span>
                                                                @endif
                                                                @if($group->can_delete)
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suppression</span>
                                                                @endif
                                                                @if($group->can_invite)
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Inviter</span>
                                                                @endif
                                                                @if(!$group->can_read && !$group->can_write && !$group->can_delete && !$group->can_invite)
                                                                    <span class="text-xs text-gray-400">Aucune permission</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            @if($groups->isEmpty())
                                <p class="text-sm text-gray-500">Aucun groupe disponible pour vous. Vous devez appartenir à un groupe avec permission d'écriture dans une organisation pour pouvoir inviter des utilisateurs.</p>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500">L'utilisateur devra changer ce mot de passe dès sa première connexion.</p>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">Annuler</a>
                            <x-primary-button>
                                {{ __('Créer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>





