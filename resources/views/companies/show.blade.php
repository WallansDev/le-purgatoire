<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'entreprise') }}
            </h2>
            <div>
                <a href="{{ route('companies.edit', $company) }}"
                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('companies.index') }}"
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
                        @if($company->logo_path)
                            <div class="flex-shrink-0">
                                @if($company->logo_url)
                                    <img src="{{ $company->logo_url }}" 
                                         alt="Logo de {{ $company->name }}" 
                                         class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-gray-50 p-2"
                                         onerror="console.error('Failed to load logo. Path: {{ $company->logo_path }}, URL: {{ $company->logo_url }}'); this.onerror=null; this.parentElement.innerHTML='<div class=\'w-24 h-24 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center\'><svg class=\'w-12 h-12 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                @else
                                    <div class="w-24 h-24 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center" title="Logo URL could not be generated. Path: {{ $company->logo_path }}">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $company->name }}</h3>
                            @if($company->siret)
                                <p class="text-sm text-gray-500 mt-1">SIRET : {{ $company->siret }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">SIRET</p>
                            <p class="text-gray-900">{{ $company->siret ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ville</p>
                            <p class="text-gray-900">{{ $company->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adresse</p>
                            <p class="text-gray-900">
                                {{ $company->address_line1 ?? '' }}
                                @if ($company->address_line2)
                                    {{ $company->address_line2 }}
                                @endif
                                @if ($company->postal_code)
                                    {{ $company->postal_code }}
                                @endif
                                @if (!$company->address_line1 && !$company->postal_code)
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pays</p>
                            <p class="text-gray-900">{{ $company->country ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Contact</p>
                            <p class="text-gray-900">{{ $company->contact_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $company->contact_email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="text-gray-900">{{ $company->contact_phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Techniciens ({{ $company->technicians->count() }})</h3>
                        <a href="{{ route('technicians.create') }}"
                            class="bg-indigo-500 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded">
                            Ajouter un technicien
                        </a>
                    </div>

                    @if ($company->technicians->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Téléphone
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Interventions
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($company->technicians as $technician)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('technicians.show', $technician) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    {{ $technician->full_name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $technician->email ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $technician->phone ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $technician->interventions_count ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $technician->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $technician->is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Aucun technicien pour cette entreprise.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
