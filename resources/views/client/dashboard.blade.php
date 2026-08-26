@php
    $status = match ($company->status) {
        'active' => ['label' => 'Active', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'pending', 'pending_installation' => ['label' => "En attente d'installation", 'class' => 'bg-amber-50 text-amber-700 ring-amber-200', 'dot' => 'bg-amber-500'],
        'suspended' => ['label' => 'Suspendue', 'class' => 'bg-red-50 text-red-700 ring-red-200', 'dot' => 'bg-red-500'],
        'expired' => ['label' => 'Expirée', 'class' => 'bg-red-50 text-red-700 ring-red-200', 'dot' => 'bg-red-500'],
        default => ['label' => ucfirst((string) $company->status), 'class' => 'bg-slate-100 text-slate-700 ring-slate-200', 'dot' => 'bg-slate-400'],
    };
    $expiresAt = $company->expires_at?->copy()->locale('fr');
    $daysRemaining = $company->expires_at ? max(0, (int) now()->startOfDay()->diffInDays($company->expires_at->copy()->startOfDay(), false)) : null;
@endphp

<x-client-layout title="SOLUTCLOUD — Tableau de bord" page-title="Tableau de bord">
    <section class="py-2 sm:py-4" aria-labelledby="client-welcome-title">
        <div class="max-w-4xl">
            <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#2b909a]">Bienvenue {{ $company->name }}</p>
            <h1 id="client-welcome-title" class="mt-4 text-3xl font-extrabold leading-[1.22] tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">
                <span class="box-decoration-clone bg-[#2b909a] px-2 py-1 text-white">Mon espace SOLUTCLOUD</span>
            </h1>
            <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Gérez votre solution, votre abonnement et vos services depuis un espace simple et sécurisé.</p>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="overview-title">
        <div class="mb-5">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#2b909a]">Vue d’ensemble</p>
            <h2 id="overview-title" class="mt-1 text-2xl font-extrabold text-slate-950">Tout ce dont vous avez besoin</h2>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            <article class="flex min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <span class="flex shrink-0 items-center justify-center text-[#207b84]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1M14 10h1M9 14h1M14 14h1M10 21v-3h4v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $status['class'] }}"><span class="h-2 w-2 rounded-full {{ $status['dot'] }}"></span>{{ $status['label'] }}</span>
                </div>
                <p class="mt-7 text-xs font-extrabold uppercase tracking-[.15em] text-slate-400">Mon entreprise</p>
                <h3 class="mt-2 break-words text-xl font-extrabold text-slate-950">{{ $company->name }}</h3>
                <dl class="mt-5 space-y-3 border-t border-slate-100 pt-5 text-sm">
                    <div class="flex min-w-0 items-start justify-between gap-4"><dt class="text-slate-500">E-mail</dt><dd class="min-w-0 break-all text-right font-semibold text-slate-800">{{ $company->email }}</dd></div>
                    <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Téléphone</dt><dd class="text-right font-semibold text-slate-800">{{ $company->phone ?: 'Non renseigné' }}</dd></div>
                </dl>
            </article>

            <article class="flex min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <span class="flex shrink-0 items-center justify-center text-[#207b84]"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 15l2 2 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">ERP sécurisé</span>
                </div>
                <p class="mt-7 text-xs font-extrabold uppercase tracking-[.15em] text-slate-400">Mon logiciel de gestion</p>
                <h3 class="mt-2 text-xl font-extrabold text-slate-950">SOLUTCLOUD Gestion</h3>
                <p class="mt-3 min-h-[44px] text-sm leading-6 text-slate-500">
                    @if ($company->status === 'active') Votre espace de gestion est disponible et prêt à être utilisé.
                    @elseif (in_array($company->status, ['pending', 'pending_installation'], true)) Votre environnement est en cours de préparation par notre équipe.
                    @else L’accès dépend du statut actuel de votre instance. @endif
                </p>
                @if ($company->status === 'active')
                    <a href="{{ $company->instance_url }}" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#2b909a] px-5 text-sm font-extrabold text-white transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2">Ouvrir mon logiciel<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 5h5v5M10 14L19 5M19 14v5H5V5h5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                @else
                    <div class="mt-6 flex min-h-11 items-center justify-center rounded-xl bg-slate-100 px-4 text-center text-sm font-bold text-slate-500">Accès bientôt disponible</div>
                @endif
            </article>

            <article class="flex min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" x-data="{ offerOpen: false }" @keydown.escape.window="offerOpen = false">
                <div class="flex items-start justify-between gap-4">
                    <span class="flex shrink-0 items-center justify-center text-[#207b84]"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/></svg></span>
                    <span class="rounded-full bg-[#e5f5f6] px-3 py-1 text-xs font-extrabold text-[#207b84]">{{ $offerDetails['label'] }}</span>
                </div>
                <p class="mt-7 text-xs font-extrabold uppercase tracking-[.15em] text-slate-400">Mon abonnement</p>
                <h3 class="mt-2 text-xl font-extrabold text-slate-950">Offre {{ $offerDetails['label'] }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ $offerDetails['audience'] }}</p>
                <div class="mt-5 rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Prochaine échéance</p>
                    <p class="mt-1 font-extrabold text-slate-900">{{ $expiresAt ? $expiresAt->translatedFormat('j F Y') : 'Non définie' }}</p>
                    @if ($daysRemaining !== null)<p class="mt-1 text-xs text-slate-500">{{ $daysRemaining }} jour{{ $daysRemaining > 1 ? 's' : '' }} restant{{ $daysRemaining > 1 ? 's' : '' }}</p>@endif
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <button type="button" @click="offerOpen = true" class="min-h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 transition hover:border-[#2b909a]/50 hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30">Voir l’offre</button>
                    <a href="{{ route('client.renew') }}" class="flex min-h-11 items-center justify-center rounded-xl bg-[#2b909a] px-3 text-center text-sm font-extrabold text-white transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30">Gérer</a>
                </div>

                <template x-teleport="body">
                    <div x-cloak x-show="offerOpen" class="fixed inset-0 z-[70] flex items-end justify-center p-0 sm:items-center sm:p-5" role="dialog" aria-modal="true" aria-labelledby="offer-modal-title">
                        <div x-show="offerOpen" x-transition.opacity class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="offerOpen = false"></div>
                        <div x-show="offerOpen" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-8 opacity-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100" x-transition:leave-end="translate-y-8 opacity-0 sm:scale-95" class="relative z-10 max-h-[92dvh] w-full overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl sm:max-w-xl sm:rounded-3xl sm:p-7">
                            <div class="flex items-start justify-between gap-5">
                                <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#2b909a]">Votre offre</p><h2 id="offer-modal-title" class="mt-2 text-2xl font-extrabold text-slate-950">SOLUTCLOUD {{ $offerDetails['label'] }}</h2><p class="mt-2 text-sm text-slate-500">{{ $offerDetails['audience'] }}</p></div>
                                <button type="button" @click="offerOpen = false" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30" aria-label="Fermer"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg></button>
                            </div>
                            <dl class="mt-7 divide-y divide-slate-100 rounded-2xl border border-slate-200">
                                @foreach ($offerDetails['details'] as $detail)
                                    <div class="grid gap-1 p-4 sm:grid-cols-[130px_1fr] sm:gap-4"><dt class="text-xs font-extrabold uppercase tracking-wide text-slate-400">{{ $detail['label'] }}</dt><dd class="text-sm font-semibold leading-6 text-slate-700">{{ $detail['value'] }}</dd></div>
                                @endforeach
                            </dl>
                            @if (filled($payment?->description))
                                <div class="mt-5 rounded-2xl bg-slate-50 p-4"><p class="text-xs font-extrabold uppercase tracking-wide text-slate-400">Description / Notes additionnelles</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $payment->description }}</p></div>
                            @endif
                            <button type="button" @click="offerOpen = false" class="mt-6 min-h-12 w-full rounded-xl bg-[#2b909a] px-5 text-sm font-extrabold text-white transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30">Fermer</button>
                        </div>
                    </div>
                </template>
            </article>
        </div>
    </section>
</x-client-layout>
