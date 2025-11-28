<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une entreprise') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nom *')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="siret" :value="__('SIRET')" />
                                <x-text-input id="siret" class="block mt-1 w-full" type="text" name="siret" :value="old('siret')" />
                                <x-input-error :messages="$errors->get('siret')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="address_line1" :value="__('Adresse ligne 1')" />
                                <x-text-input id="address_line1" class="block mt-1 w-full" type="text" name="address_line1" :value="old('address_line1')" />
                                <x-input-error :messages="$errors->get('address_line1')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="address_line2" :value="__('Adresse ligne 2')" />
                                <x-text-input id="address_line2" class="block mt-1 w-full" type="text" name="address_line2" :value="old('address_line2')" />
                                <x-input-error :messages="$errors->get('address_line2')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="postal_code" :value="__('Code postal')" />
                                <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" />
                                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="city" :value="__('Ville')" />
                                <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="country" :value="__('Pays')" />
                                <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country')" />
                                <x-input-error :messages="$errors->get('country')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="logo_path" :value="__('Chemin du logo')" />
                                <x-text-input id="logo_path" class="block mt-1 w-full" type="text" name="logo_path" :value="old('logo_path')" />
                                <x-input-error :messages="$errors->get('logo_path')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_name" :value="__('Nom du contact')" />
                                <x-text-input id="contact_name" class="block mt-1 w-full" type="text" name="contact_name" :value="old('contact_name')" />
                                <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_email" :value="__('Email du contact')" />
                                <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email')" />
                                <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_phone" :value="__('Téléphone du contact')" />
                                <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone')" />
                                <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('companies.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
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

