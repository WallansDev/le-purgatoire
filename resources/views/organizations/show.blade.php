<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'organisation') }}
            </h2>
            <div>
                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('organizations', 'write', $organization))
                    <a href="{{ route('organizations.edit', $organization) }}"
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif
                <a href="{{ route('organizations.index') }}"
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
                    <div class="flex items-center gap-6 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex-1">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $organization->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Slug : {{ $organization->slug }}</p>
                            <span class="mt-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $organization->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $organization->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    @if($organization->description)
                        <div class="mb-6">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p class="text-gray-900">{{ $organization->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $organization->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="text-gray-900">{{ $organization->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adresse</p>
                            <p class="text-gray-900">
                                {{ $organization->address_line1 ?? '' }}
                                @if ($organization->address_line2)
                                    {{ $organization->address_line2 }}
                                @endif
                                @if ($organization->postal_code)
                                    {{ $organization->postal_code }}
                                @endif
                                @if ($organization->city)
                                    {{ $organization->city }}
                                @endif
                                @if (!$organization->address_line1 && !$organization->postal_code && !$organization->city)
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pays</p>
                            <p class="text-gray-900">{{ $organization->country ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Groupes ({{ $organization->groups->count() }})</h3>
                        @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'write', $organization))
                            <a href="{{ route('groups.create', ['organization_id' => $organization->id]) }}"
                                class="bg-indigo-500 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded">
                                Ajouter un groupe
                            </a>
                        @endif
                    </div>

                    @if ($organization->groups->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Membres
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Par défaut
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($organization->groups as $group)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('groups.show', $group) }}"
                                                    class="text-blue-600 hover:text-blue-900 font-medium">
                                                    {{ $group->name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-500">{{ $group->description ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $group->users_count ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($group->is_default)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Oui
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'read', $organization))
                                                    <a href="{{ route('groups.show', $group) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                                @endif
                                                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'write', $organization))
                                                    <a href="{{ route('groups.edit', $group) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Aucun groupe pour cette organisation.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

