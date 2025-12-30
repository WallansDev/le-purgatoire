<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Groupes -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Colonne Entreprises (gauche) -->
                        {{-- <div class="w-full md:w-1/2">
                            <section class="space-y-6">
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Mes Entreprises') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Les entreprises auxquelles vous avez accès via vos groupes.
                                    </p>
                                </header>

                                @if ($companies->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach ($companies as $company)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-start space-x-3">
                                                    @if ($company->logo_path)
                                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ $company->logo_url }}" alt="{{ $company->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-lg bg-gray-300 flex items-center justify-center">
                                                            <span class="text-xs font-medium text-gray-600">{{ substr($company->name, 0, 2) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-sm font-medium text-gray-900">{{ $company->name }}</h3>
                                                        @if ($company->siret)
                                                            <p class="text-xs text-gray-500">SIRET: {{ $company->siret }}</p>
                                                        @endif
                                                        @if ($company->city)
                                                            <p class="text-xs text-gray-500">{{ $company->city }}</p>
                                                        @endif
                                                        @if ($company->contact_name)
                                                            <p class="text-xs text-gray-500">Contact: {{ $company->contact_name }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Aucune entreprise trouvée.</p>
                                @endif
                            </section>
                        </div> --}}
                        <!-- Colonne Groupes (droite) -->
                        {{-- <div class="w-full md:w-1/2"> --}}
                        <div class="w-full">
                            <section class="space-y-6">
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Mes Groupes') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Les groupes auxquels vous appartenez.
                                    </p>
                                </header>
                                @if ($groups->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach ($groups as $group)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h3 class="text-sm font-medium text-gray-900">
                                                            {{ $group->name }}</h3>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $group->organization->name }}</p>
                                                    </div>
                                                    @if ($group->is_default)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Défaut
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($group->description)
                                                    <p class="mt-2 text-xs text-gray-600">{{ $group->description }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Aucun groupe trouvé.</p>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if (!$user->isOwner())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @else
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section class="space-y-6">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Delete Account') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Le compte propriétaire ne peut pas être supprimé.
                                </p>
                            </header>
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
