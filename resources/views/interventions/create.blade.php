<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une intervention') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('interventions.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="technician_id" :value="__('Technicien *')" />
                                <select id="technician_id" name="technician_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionner un technicien</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ old('technician_id') == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->full_name }} - {{ $technician->company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('technician_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="scheduled_at" :value="__('Date et heure programmée *')" />
                                <x-text-input id="scheduled_at" class="block mt-1 w-full" type="datetime-local" name="scheduled_at" :value="old('scheduled_at')" required />
                                <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="title" :value="__('Titre *')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="started_at" :value="__('Date et heure de début')" />
                                <x-text-input id="started_at" class="block mt-1 w-full" type="datetime-local" name="started_at" :value="old('started_at')" />
                                <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="finished_at" :value="__('Date et heure de fin')" />
                                <x-text-input id="finished_at" class="block mt-1 w-full" type="datetime-local" name="finished_at" :value="old('finished_at')" />
                                <x-input-error :messages="$errors->get('finished_at')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="note" :value="__('Note (0-5)')" />
                                <x-text-input id="note" class="block mt-1 w-full" type="number" name="note" :value="old('note')" min="0" max="5" />
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Adresse')" />
                                <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Ex: 123 Rue de la République, 75001 Paris">{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="is_completed" :value="__('Intervention terminée ?')" />
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" id="is_completed" name="is_completed" value="1" {{ old('is_completed') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Oui, l'intervention est terminée</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('is_completed')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2" id="non_completion_reason_field" style="display: none;">
                                <x-input-label for="non_completion_reason" :value="__('Raison de non-complétion *')" />
                                <textarea id="non_completion_reason" name="non_completion_reason" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Expliquez pourquoi l'intervention n'a pas été terminée...">{{ old('non_completion_reason') }}</textarea>
                                <x-input-error :messages="$errors->get('non_completion_reason')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes concernant l\'intervention')" />
                                <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Notes internes sur l'intervention...">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="client_comments" :value="__('Commentaires du client')" />
                                <textarea id="client_comments" name="client_comments" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Commentaires du client sur l'intervention...">{{ old('client_comments') }}</textarea>
                                <x-input-error :messages="$errors->get('client_comments')" class="mt-2" />
                            </div>

                        </div>

                        <script>
                            document.getElementById('is_completed').addEventListener('change', function() {
                                const reasonField = document.getElementById('non_completion_reason_field');
                                if (!this.checked) {
                                    reasonField.style.display = 'block';
                                    document.getElementById('non_completion_reason').setAttribute('required', 'required');
                                } else {
                                    reasonField.style.display = 'none';
                                    document.getElementById('non_completion_reason').removeAttribute('required');
                                }
                            });
                            
                            // Initialiser l'état au chargement de la page
                            document.addEventListener('DOMContentLoaded', function() {
                                const checkbox = document.getElementById('is_completed');
                                const reasonField = document.getElementById('non_completion_reason_field');
                                if (!checkbox.checked) {
                                    reasonField.style.display = 'block';
                                }
                            });
                        </script>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('interventions.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
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

