@php($client = auth()->user())

<x-client-layout title="SOLUTCLOUD — Mon compte" page-title="Compte">
    <div class="mb-7">
        <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#2b909a]">Informations personnelles</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Mon compte</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Consultez vos informations et protégez l’accès à votre espace client.</p>
    </div>

    <div class="grid min-w-0 gap-6 xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
        <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="identity-title">
            <div class="flex min-w-0 items-center gap-4 border-b border-slate-100 pb-6">
                <span class="flex shrink-0 items-center justify-center text-[#207b84]">
                    <svg class="h-11 w-11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0113 0" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 id="identity-title" class="truncate text-xl font-extrabold text-slate-950">{{ $client->name }}</h2>
                    <p class="mt-1 truncate text-sm text-slate-500">Client SOLUTCLOUD</p>
                </div>
            </div>

            <dl class="mt-6 divide-y divide-slate-100">
                <div class="py-4 first:pt-0">
                    <dt class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Nom complet</dt>
                    <dd class="mt-1 break-words text-sm font-bold text-slate-800">{{ $client->name }}</dd>
                </div>
                <div class="py-4">
                    <dt class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">E-mail de connexion</dt>
                    <dd class="mt-1 break-all text-sm font-bold text-slate-800">{{ $client->email }}</dd>
                </div>
                <div class="py-4">
                    <dt class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Entreprise</dt>
                    <dd class="mt-1 break-words text-sm font-bold text-slate-800">{{ $company->name }}</dd>
                </div>
                <div class="py-4">
                    <dt class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Téléphone</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">{{ $company->phone ?: 'Non renseigné' }}</dd>
                </div>
                <div class="py-4 pb-0">
                    <dt class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Offre</dt>
                    <dd class="mt-2 inline-flex rounded-full bg-[#e5f5f6] px-3 py-1 text-xs font-extrabold text-[#207b84]">SOLUTCLOUD {{ strtoupper($company->package) }}</dd>
                </div>
            </dl>
        </section>

        <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="security-title">
            <div class="flex items-start gap-4 border-b border-slate-100 pb-6">
                <span class="flex shrink-0 items-center justify-center text-[#207b84]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 018 0v3M12 14v3" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <h2 id="security-title" class="text-xl font-extrabold text-slate-950">Sécurité du compte</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Utilisez un mot de passe unique que vous n’employez sur aucun autre service.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('client.password.update') }}" class="mt-6 space-y-5" x-data="{ currentVisible: false, passwordVisible: false, confirmationVisible: false }">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-2 block text-sm font-extrabold text-slate-700">Mot de passe actuel</label>
                    <div class="relative">
                        <input id="current_password" name="current_password" :type="currentVisible ? 'text' : 'password'" autocomplete="current-password" required class="block min-h-12 w-full rounded-xl border-slate-300 pr-12 text-sm shadow-sm transition focus:border-[#2b909a] focus:ring-[#2b909a]">
                        <button type="button" @click="currentVisible = ! currentVisible" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 transition hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2b909a]/30" :aria-label="currentVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" :aria-pressed="currentVisible.toString()">
                            <svg x-show="! currentVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            <svg x-cloak x-show="currentVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3l18 18M10.6 10.7a2 2 0 002.7 2.7M9.9 5.2A10.8 10.8 0 0112 5c6 0 9.5 7 9.5 7a15.7 15.7 0 01-2.2 3M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a9.8 9.8 0 004.1-.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    @error('current_password')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-extrabold text-slate-700">Nouveau mot de passe</label>
                        <div class="relative">
                            <input id="password" name="password" :type="passwordVisible ? 'text' : 'password'" autocomplete="new-password" required class="block min-h-12 w-full rounded-xl border-slate-300 pr-12 text-sm shadow-sm transition focus:border-[#2b909a] focus:ring-[#2b909a]">
                            <button type="button" @click="passwordVisible = ! passwordVisible" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 transition hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2b909a]/30" :aria-label="passwordVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" :aria-pressed="passwordVisible.toString()">
                                <svg x-show="! passwordVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg x-cloak x-show="passwordVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3l18 18M10.6 10.7a2 2 0 002.7 2.7M9.9 5.2A10.8 10.8 0 0112 5c6 0 9.5 7 9.5 7a15.7 15.7 0 01-2.2 3M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a9.8 9.8 0 004.1-.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-extrabold text-slate-700">Confirmer le mot de passe</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" :type="confirmationVisible ? 'text' : 'password'" autocomplete="new-password" required class="block min-h-12 w-full rounded-xl border-slate-300 pr-12 text-sm shadow-sm transition focus:border-[#2b909a] focus:ring-[#2b909a]">
                            <button type="button" @click="confirmationVisible = ! confirmationVisible" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 transition hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2b909a]/30" :aria-label="confirmationVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" :aria-pressed="confirmationVisible.toString()">
                                <svg x-show="! confirmationVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg x-cloak x-show="confirmationVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3l18 18M10.6 10.7a2 2 0 002.7 2.7M9.9 5.2A10.8 10.8 0 0112 5c6 0 9.5 7 9.5 7a15.7 15.7 0 01-2.2 3M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a9.8 9.8 0 004.1-.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 text-xs leading-5 text-slate-500">Votre mot de passe doit être suffisamment long, difficile à deviner et différent de vos informations personnelles.</div>

                <div class="flex justify-end pt-1">
                    <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-[#2b909a] px-6 text-sm font-extrabold text-white shadow-lg shadow-[#2b909a]/15 transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2 sm:w-auto">Mettre à jour le mot de passe</button>
                </div>
            </form>
        </section>
    </div>
</x-client-layout>
