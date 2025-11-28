<header x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <!-- Logo -->
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 text-lg font-semibold text-gray-900">
            <x-application-logo class="text-indigo-600 w-[35.2px] h-[35.2px]" />
            <span>{{ config('app.name', 'Backoffice') }}</span>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden gap-6 text-sm font-medium text-gray-700 md:flex">
            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : '' }}">
                Tableau de bord
            </a>
            <a href="{{ route('companies.index') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('companies.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Entreprises
            </a>
            <a href="{{ route('technicians.index') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('technicians.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Techniciens
            </a>
            <a href="{{ route('interventions.index') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('interventions.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Interventions
            </a>
        </nav>

        <!-- User Menu -->
        <div class="flex items-center gap-3">
            <!-- Settings Dropdown -->
            <div class="hidden md:flex md:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-800 hover:text-indigo-600 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('tags.index')">
                            {{ __('Tags') }}
                        </x-dropdown-link>
                        <hr>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Menu -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-gray-200 bg-gray-50">
        <div class="px-6 py-4 space-y-3">
            <a href="{{ route('dashboard') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : '' }}">
                Tableau de bord
            </a>
            <a href="{{ route('companies.index') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('companies.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Entreprises
            </a>
            <a href="{{ route('technicians.index') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('technicians.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Techniciens
            </a>
            <a href="{{ route('interventions.index') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('interventions.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Interventions
            </a>
            <a href="{{ route('tags.index') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('tags.*') ? 'text-indigo-600 font-semibold' : '' }}">
                Tags
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="mb-3">
                <div class="font-medium text-base text-gray-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                    {{ __('Profile') }}
                </a>
                <a href="{{ route(name: 'tags.index') }}" class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                    {{ __('Tags') }}
                </a>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="block text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</header>
