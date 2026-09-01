@php
    $billingEndsAt = $company->expires_at?->copy()->timezone('GMT')->locale('fr');
    $currencyLabel = strtoupper($paymentCurrency) === 'XOF' ? 'FCFA' : strtoupper($paymentCurrency);
    $isSandboxCurrency = strtoupper($paymentCurrency) !== 'XOF';
    $subscriptionStatus = match ($company->status) {
        'active' => ['label' => 'Actif', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'pending', 'pending_installation' => ['label' => "En attente d'installation", 'class' => 'bg-amber-50 text-amber-700 ring-amber-200', 'dot' => 'bg-amber-500'],
        default => ['label' => 'Suspendu', 'class' => 'bg-red-50 text-red-700 ring-red-200', 'dot' => 'bg-red-500'],
    };
@endphp

<x-client-layout title="SOLUTCLOUD — Mon abonnement" page-title="Abonnement">
    <div class="mb-7">
        <div class="flex flex-wrap items-center gap-3">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#2b909a]">Abonnement</p>
            @if ($isSandboxCurrency)
                <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wide text-amber-700 ring-1 ring-inset ring-amber-200">Mode de test {{ strtoupper($paymentCurrency) }}</span>
            @endif
        </div>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Gérer mon abonnement</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Consultez votre offre, choisissez une durée et accédez au paiement sécurisé Moneroo.</p>
    </div>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-labelledby="current-subscription-title">
        <div class="grid min-w-0 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]">
            <div class="min-w-0 p-5 sm:p-7 lg:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[.16em] text-[#2b909a]">Offre actuelle</p>
                        <h2 id="current-subscription-title" class="mt-2 text-2xl font-extrabold text-slate-950">Abonnement actuel : Offre {{ $offerDetails['label'] }} (mensuel)</h2>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold ring-1 ring-inset {{ $subscriptionStatus['class'] }}"><span class="h-2 w-2 rounded-full {{ $subscriptionStatus['dot'] }}"></span>{{ $subscriptionStatus['label'] }}</span>
                </div>

                <p class="mt-4 text-sm leading-6 text-slate-600">
                    @if ($billingEndsAt)
                        La période de facturation actuelle se termine le <strong class="text-slate-900">{{ $billingEndsAt->translatedFormat('j F Y à H\hi') }} GMT</strong>.
                    @else
                        La date de fin de la période de facturation n’est pas encore définie.
                    @endif
                </p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ($offerDetails['details'] as $detail)
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-extrabold uppercase tracking-[.12em] text-slate-400">{{ $detail['label'] }}</p>
                            <p class="mt-1 text-sm font-semibold leading-6 text-slate-700">{{ $detail['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="min-w-0 border-t border-slate-200 bg-[#f7fafb] p-5 sm:p-7 lg:border-l lg:border-t-0 lg:p-8">
                <span class="flex items-center justify-center text-[#207b84]"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.4 8.3-8 9-4.6-.7-8-4-8-9V7l8-4z"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <h3 class="mt-5 text-lg font-extrabold text-slate-950">SOLUTCLOUD {{ $offerDetails['label'] }}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $offerDetails['audience'] }}</p>
                @if (filled($payment?->description))
                    <div class="mt-6 border-t border-slate-200 pt-5">
                        <p class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Description / Notes additionnelles</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $payment->description }}</p>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 lg:p-8" aria-labelledby="renewal-title">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.16em] text-[#2b909a]">Renouvellement</p>
                <h2 id="renewal-title" class="mt-2 text-2xl font-extrabold text-slate-950">Choisissez votre période</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">La nouvelle période sera ajoutée à votre échéance actuelle après confirmation du paiement.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 text-xs font-bold text-slate-500"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>Paiement sécurisé par Moneroo</span>
        </div>

        @if ($renewalPlans->isEmpty())
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">Les périodes de renouvellement ne sont pas encore disponibles pour cette offre.</div>
        @else
            <form method="POST" action="{{ route('client.subscription.checkout') }}" class="mt-7" x-data="{ selectedId: null, selectedDuration: null, selectedAmount: null, formatAmount(value) { return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(value) } }">
                @csrf
                <input type="hidden" name="action" value="renewal">
                <input type="hidden" name="plan_id" :value="selectedId">

                <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
                    @foreach ($renewalPlans as $plan)
                        <button
                            type="button"
                            @click="selectedId = {{ $plan['id'] }}; selectedDuration = {{ $plan['duration'] }}; selectedAmount = {{ $plan['amount'] }}"
                            :aria-pressed="selectedId === {{ $plan['id'] }}"
                            class="group min-w-0 rounded-2xl border p-4 text-left transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2"
                            :class="selectedId === {{ $plan['id'] }} ? 'border-[#2b909a] bg-[#eef9fa] shadow-md shadow-[#2b909a]/10' : 'border-slate-200 bg-white hover:-translate-y-0.5 hover:border-[#2b909a]/50 hover:shadow-md'"
                        >
                            <span class="flex items-center justify-between gap-2">
                                <span class="text-lg font-extrabold text-slate-950">{{ $plan['duration'] }} mois</span>
                                <span class="flex h-5 w-5 items-center justify-center rounded-full border transition" :class="selectedId === {{ $plan['id'] }} ? 'border-[#2b909a] bg-[#2b909a] text-white' : 'border-slate-300 text-transparent'">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </span>
                            <span class="mt-4 block text-xl font-extrabold text-[#207b84]">{{ number_format($plan['amount'], 0, ',', ' ') }} {{ $currencyLabel }}</span>
                            <span class="mt-1 block text-xs text-slate-400">Total de la période</span>
                        </button>
                    @endforeach
                </div>

                <div class="mt-7 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Votre sélection</p>
                        <p class="mt-1 font-extrabold text-slate-900" x-text="selectedId ? `${selectedDuration} mois — ${formatAmount(selectedAmount)} {{ $currencyLabel }}` : 'Sélectionnez une durée'"></p>
                    </div>
                    <button type="submit" :disabled="!selectedId" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#2b909a] px-6 text-sm font-extrabold text-white shadow-lg shadow-[#2b909a]/15 transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none sm:w-auto">
                        Continuer vers le paiement
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </form>
        @endif
    </section>

    @if ($pendingUpgrade)
        <section class="mt-6 rounded-3xl border border-blue-200 bg-blue-50 p-5 sm:p-7" aria-labelledby="pending-upgrade-title">
            <p class="text-xs font-extrabold uppercase tracking-[.14em] text-blue-700">Évolution d’offre</p>
            <h2 id="pending-upgrade-title" class="mt-2 text-xl font-extrabold text-slate-950">Votre passage à SOLUTCLOUD BUSINESS est en cours de traitement</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Votre espace reste sur START jusqu’à la finalisation par notre équipe. Merci de patienter : aucune nouvelle demande ni aucun nouveau paiement ne sont nécessaires.</p>
        </section>
    @elseif (strtolower($company->package) === 'start' && $upgradePlans->isNotEmpty())
        <section class="relative mt-6 overflow-hidden rounded-3xl bg-[#0a3034] p-5 text-white shadow-xl shadow-[#0a3034]/10 sm:p-7 lg:p-8" aria-labelledby="upgrade-title">
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#2b909a]/40 blur-3xl"></div>
            <div class="relative">
                <div class="max-w-3xl">
                    <p class="text-xs font-extrabold uppercase tracking-[.16em] text-[#8bd4da]">Évolution de votre solution</p>
                    <h2 id="upgrade-title" class="mt-2 text-2xl font-extrabold">Passez à l’offre BUSINESS</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-200">Profitez de plus d’utilisateurs, du CRM, des projets et de sauvegardes quotidiennes. Après confirmation du paiement, notre équipe finalisera votre passage à BUSINESS.</p>
                </div>

                <form method="POST" action="{{ route('client.subscription.checkout') }}" class="mt-7" x-data="{ selectedId: null, selectedDuration: null, selectedAmount: null, formatAmount(value) { return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(value) } }">
                    @csrf
                    <input type="hidden" name="action" value="upgrade">
                    <input type="hidden" name="plan_id" :value="selectedId">

                    <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
                        @foreach ($upgradePlans as $plan)
                            <button
                                type="button"
                                @click="selectedId = {{ $plan['id'] }}; selectedDuration = {{ $plan['duration'] }}; selectedAmount = {{ $plan['amount'] }}"
                                :aria-pressed="selectedId === {{ $plan['id'] }}"
                                class="rounded-2xl border p-4 text-left transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/40"
                                :class="selectedId === {{ $plan['id'] }} ? 'border-[#64d1d9] bg-white text-slate-950 shadow-xl' : 'border-white/15 bg-white/[.06] text-white hover:border-white/35 hover:bg-white/10'"
                            >
                                <span class="flex items-center justify-between gap-2"><span class="text-lg font-extrabold">{{ $plan['duration'] }} mois</span><span class="flex h-5 w-5 items-center justify-center rounded-full border transition" :class="selectedId === {{ $plan['id'] }} ? 'border-[#2b909a] bg-[#2b909a] text-white' : 'border-white/40 text-transparent'"><svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></span>
                                <span class="mt-4 block text-xl font-extrabold" :class="selectedId === {{ $plan['id'] }} ? 'text-[#207b84]' : 'text-[#8bd4da]'">{{ number_format($plan['amount'], 0, ',', ' ') }} {{ $currencyLabel }}</span>
                                <span class="mt-1 block text-xs" :class="selectedId === {{ $plan['id'] }} ? 'text-slate-400' : 'text-slate-400'">Total de la période</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-7 flex flex-col gap-4 rounded-2xl border border-white/10 bg-black/10 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                        <div><p class="text-xs font-extrabold uppercase tracking-[.12em] text-slate-400">Votre sélection BUSINESS</p><p class="mt-1 font-extrabold" x-text="selectedId ? `${selectedDuration} mois — ${formatAmount(selectedAmount)} {{ $currencyLabel }}` : 'Sélectionnez une durée'"></p></div>
                        <button type="submit" :disabled="!selectedId" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-white px-6 text-sm font-extrabold text-[#176f78] transition hover:bg-[#e5f5f6] focus:outline-none focus:ring-2 focus:ring-white/50 disabled:cursor-not-allowed disabled:bg-white/20 disabled:text-white/50 sm:w-auto">Passer à BUSINESS<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div>
                </form>
            </div>
        </section>
    @endif
</x-client-layout>
