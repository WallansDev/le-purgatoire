<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du groupe') }}
            </h2>
            <div>
                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'write', $group->organization))
                    <a href="{{ route('groups.edit', $group) }}"
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif
                <a href="{{ route('groups.index') }}"
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
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $group->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Organisation : 
                                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('organizations', 'read', $group->organization))
                                    <a href="{{ route('organizations.show', $group->organization) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $group->organization->name }}
                                    </a>
                                @else
                                    <span class="text-gray-700">{{ $group->organization->name }}</span>
                                @endif
                            </p>
                            <div class="mt-2 flex gap-2">
                                @if($group->is_default)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Groupe par défaut
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($group->description)
                        <div class="mb-6">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p class="text-gray-900">{{ $group->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Organisation</p>
                            <p class="text-gray-900">
                                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('organizations', 'read', $group->organization))
                                    <a href="{{ route('organizations.show', $group->organization) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $group->organization->name }}
                                    </a>
                                @else
                                    <span>{{ $group->organization->name }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nombre de membres</p>
                            <p class="text-gray-900">{{ $group->users->count() }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-2">Permissions du groupe</p>
                            <div class="space-y-2">
                                @php
                                    $resources = [
                                        'companies' => 'Entreprises',
                                        'technicians' => 'Techniciens',
                                        'interventions' => 'Interventions',
                                        'organizations' => 'Organisations',
                                        'groups' => 'Groupes',
                                    ];
                                    $hasAnyPermission = false;
                                @endphp
                                
                                @foreach($resources as $resource => $label)
                                    @php
                                        $read = $group->{$resource . '_read'};
                                        $write = $group->{$resource . '_write'};
                                        $delete = $group->{$resource . '_delete'};
                                        if ($read || $write || $delete) {
                                            $hasAnyPermission = true;
                                        }
                                    @endphp
                                    @if($read || $write || $delete)
                                        <div class="text-sm">
                                            <span class="font-medium text-gray-700">{{ $label }}:</span>
                                            @if($read)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 ml-1">Lecture</span>
                                            @endif
                                            @if($write)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-1">Modification</span>
                                            @endif
                                            @if($delete)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-1">Suppression</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                
                                @if($group->can_invite)
                                    @php $hasAnyPermission = true; @endphp
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700">Utilisateurs:</span>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 ml-1">Inviter</span>
                                    </div>
                                @endif
                                
                                @if(!$hasAnyPermission)
                                    <span class="text-sm text-gray-400">Aucune permission</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'write', $group->organization))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Ajouter un membre</h3>
                        </div>
                        
                        <form action="{{ route('groups.members.add', $group) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Utilisateur *</label>
                                    <select name="user_id" id="user_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionner un utilisateur</option>
                                        @foreach($allUsers as $user)
                                            @if(!in_array($user->id, $groupUserIds))
                                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        <p class="mt-2 text-sm text-gray-500">Les permissions sont définies au niveau du groupe pour chaque ressource.</p>
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Membres ({{ $group->users->count() }})</h3>
                    </div>
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($group->users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Utilisateur
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($group->users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if(auth()->user()->isOwner() || auth()->user()->hasPermissionInOrganization('groups', 'write', $group->organization))
                                                    <form action="{{ route('groups.members.remove', [$group, $user]) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet utilisateur du groupe ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Retirer</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Aucun membre dans ce groupe.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

