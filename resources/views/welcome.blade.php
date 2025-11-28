<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Backoffice') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gradient-to-b from-slate-50 to-white text-slate-900 dark:from-slate-950 dark:to-slate-900">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-slate-200/70 dark:border-slate-800">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold text-slate-900 dark:text-white">
                        <x-application-logo class="w-8 h-8 text-indigo-600" />
                        <span>{{ config('app.name', 'Backoffice') }}</span>
                    </a>
                    <nav class="hidden gap-6 text-sm font-medium text-slate-600 dark:text-slate-200 md:flex">
                        <a href="{{ route('companies.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Entreprises</a>
                        <a href="{{ route('technicians.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Techniciens</a>
                        <a href="{{ route('interventions.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Interventions</a>
                    </nav>
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('profile.edit') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-500 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-100 dark:hover:border-indigo-400">
                                {{ Auth::user()->name }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 dark:text-slate-200 dark:hover:text-indigo-400">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition">
                                    Créer un compte
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <section class="mx-auto flex max-w-6xl flex-col items-center gap-10 px-6 py-16 lg:flex-row lg:py-24">
                    <div class="w-full lg:w-1/2">
                        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500">Backoffice interventions</p>
                        <h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-900 dark:text-white lg:text-5xl">
                            Pilotez vos entreprises, techniciens et interventions en un clin d’œil.
                        </h1>
                        <p class="mt-6 text-lg text-slate-600 dark:text-slate-300">
                            Centralisez les informations clés, suivez les indicateurs et lancez vos prochaines actions depuis un seul point d’entrée optimisé pour les équipes terrain.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-500 transition">
                                    Ouvrir le tableau de bord
                                </a>
                                <a href="{{ route('interventions.create') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-6 py-3 font-semibold text-slate-700 hover:border-indigo-500 hover:text-indigo-600 transition dark:border-slate-700 dark:text-slate-100 dark:hover:border-indigo-400">
                                    Planifier une intervention
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-500 transition">
                                    Se connecter
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-6 py-3 font-semibold text-slate-700 hover:border-indigo-500 hover:text-indigo-600 transition dark:border-slate-700 dark:text-slate-100 dark:hover:border-indigo-400">
                                        Créer un accès
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="w-full rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-100 dark:bg-slate-900 dark:ring-slate-800 lg:w-1/2">
                        <dl class="grid grid-cols-2 gap-6 text-center text-sm font-semibold text-slate-600 dark:text-slate-300">
                            <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Entreprises suivies</dt>
                                <dd class="mt-2 text-3xl text-slate-900 dark:text-white">+120</dd>
                            </div>
                            <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Techniciens actifs</dt>
                                <dd class="mt-2 text-3xl text-slate-900 dark:text-white">48</dd>
                            </div>
                            <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Interventions du mois</dt>
                                <dd class="mt-2 text-3xl text-slate-900 dark:text-white">312</dd>
                            </div>
                            <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Taux de satisfaction</dt>
                                <dd class="mt-2 text-3xl text-slate-900 dark:text-white">4,7/5</dd>
                            </div>
                        </dl>
                        <p class="mt-6 text-sm text-slate-500 dark:text-slate-400">
                            Ces chiffres sont fournis à titre d’exemple. Connectez-vous pour visualiser vos indicateurs réels en temps réel.
                        </p>
                    </div>
                </section>

                <section class="bg-white py-16 dark:bg-slate-950/40">
                    <div class="mx-auto max-w-6xl px-6">
                        <div class="flex flex-col gap-4 text-center">
                            <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500">Navigation rapide</p>
                            <h2 class="text-3xl font-semibold text-slate-900 dark:text-white">Tout ce qu’il faut pour agir vite</h2>
                            <p class="text-slate-600 dark:text-slate-300">Accédez directement aux modules clés du backoffice.</p>
                        </div>
                        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            <a href="{{ route('companies.index') }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <x-heroicon-o-building-office-2 class="h-6 w-6" />
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">Entreprises</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Fiches détaillées, contacts clés et indicateurs de portefeuille.</p>
                            </a>
                            <a href="{{ route('technicians.index') }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition">
                                    <x-heroicon-o-user-group class="h-6 w-6" />
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">Techniciens</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Suivez l’activité, la disponibilité et les performances individuelles.</p>
                            </a>
                            <a href="{{ route('interventions.index') }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition">
                                    <x-heroicon-o-wrench-screwdriver class="h-6 w-6" />
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">Interventions</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Planifiez, contrôlez les retards et consultez les notes données.</p>
                            </a>
                            <a href="{{ route('welcome') }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition">
                                    <x-heroicon-o-chart-bar class="h-6 w-6" />
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">Tableau de bord</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Visualisez les KPIs en temps réel et priorisez vos actions.</p>
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200/70 bg-white py-6 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400">
                © {{ date('Y') }} {{ config('app.name', 'Backoffice') }} — Gestion des interventions et des équipes terrain.
            </footer>
        </div>
    </body>
</html>

