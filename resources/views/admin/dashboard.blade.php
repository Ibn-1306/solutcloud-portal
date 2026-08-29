<x-admin-layout
    title="SOLUTCLOUD — Administration"
    page-title="Tableau de bord"
    description="Vue d’ensemble des clients, instances et échéances."
>
    <section class="py-2 sm:py-4" aria-labelledby="admin-welcome-title">
        <div class="flex flex-col gap-7 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-4xl">
                <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#2b909a]">Centre de pilotage</p>
                <h2 id="admin-welcome-title" class="mt-4 text-3xl font-extrabold leading-[1.22] tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">
                    <span class="box-decoration-clone bg-[#2b909a] px-2 py-1 text-white">Bienvenue dans</span>
                    <span> SOLUTCLOUD Gestion</span>
                </h2>
                <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Suivez vos clients, confirmez les installations et gérez les opérations commerciales depuis un espace unique.</p>
            </div>
            <div class="grid w-full grid-cols-1 gap-3 sm:w-auto sm:grid-cols-2">
                <a href="{{ route('admin.payments.index') }}#payment-form" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#2b909a] px-5 text-center text-sm font-extrabold text-white transition hover:bg-[#237781] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2">Voir paiement</a>
                <a href="#new-instance" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-center text-sm font-extrabold text-slate-800 transition hover:border-[#2b909a] hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2">Créer une instance</a>
            </div>
        </div>
    </section>

    <style>
        @keyframes dashboard-alert-arrow {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(7px); }
        }

        @keyframes dashboard-alert-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(43, 144, 154, .24); }
            50% { box-shadow: 0 0 0 8px rgba(43, 144, 154, 0); }
        }

        .dashboard-action-card--active .dashboard-action-arrow {
            animation: dashboard-alert-arrow 1.15s ease-in-out infinite;
        }

        .dashboard-action-card--active .dashboard-action-dot {
            animation: dashboard-alert-pulse 1.8s ease-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-action-card--active .dashboard-action-arrow,
            .dashboard-action-card--active .dashboard-action-dot {
                animation: none;
            }
        }
    </style>

    @php
        $operationalPriorityCount = $newCommercialRequestCount + $availablePayments->count() + $pendingCount + $pendingDemoRequestCount;
        $latestCommercialRequest = $newCommercialRequests->first();
        $nextPaidPayment = $availablePayments->first();
    @endphp

    <section class="mt-7" aria-labelledby="admin-priorities-title" aria-live="polite">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.16em] text-[#2b909a]">Alertes opérationnelles</p>
                <h2 id="admin-priorities-title" class="mt-1 text-xl font-extrabold text-slate-950">Priorités à traiter</h2>
            </div>
            <span class="w-fit rounded-full px-3 py-1 text-xs font-extrabold {{ $operationalPriorityCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $operationalPriorityCount > 0 ? $operationalPriorityCount.' action(s) requise(s)' : 'Tout est à jour' }}
            </span>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a
                href="{{ route('admin.orders.index') }}"
                @class([
                    'dashboard-action-card group relative overflow-hidden rounded-3xl border p-5 transition duration-300 focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 focus:ring-offset-2',
                    'dashboard-action-card--active border-[#2b909a]/35 bg-gradient-to-br from-[#edf9fa] to-white shadow-lg shadow-[#2b909a]/10 hover:-translate-y-0.5' => $newCommercialRequestCount > 0,
                    'border-slate-200 bg-white hover:border-[#2b909a]/30' => $newCommercialRequestCount === 0,
                ])
            >
                <div class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#2b909a] text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M7 3h10a2 2 0 012 2v16l-7-4-7 4V5a2 2 0 012-2z"/><path d="M9 8h6M9 12h6" stroke-linecap="round"/></svg>
                    </span>
                    <span class="dashboard-action-dot flex h-9 min-w-9 items-center justify-center rounded-full px-2 text-sm font-black {{ $newCommercialRequestCount > 0 ? 'bg-[#2b909a] text-white' : 'bg-slate-100 text-slate-500' }}">{{ $newCommercialRequestCount }}</span>
                </div>
                <p class="mt-5 text-xs font-extrabold uppercase tracking-[.13em] text-[#207b84]">Commandes et devis</p>
                <h3 class="mt-2 text-lg font-extrabold leading-snug text-slate-950">
                    {{ $newCommercialRequestCount > 0 ? ($newCommercialRequestCount === 1 ? 'Nouvelle demande à traiter' : 'Nouvelles demandes à traiter') : 'Aucune nouvelle demande' }}
                </h3>
                <p class="mt-2 min-h-10 text-sm leading-5 text-slate-600">
                    @if ($latestCommercialRequest)
                        {{ $latestCommercialRequest->commercialReference() }} · {{ $latestCommercialRequest->company_name ?: $latestCommercialRequest->fullname }} · {{ $latestCommercialRequest->offer ?: 'Offre à préciser' }}
                    @else
                        Toutes les demandes commerciales reçues ont déjà un paiement associé.
                    @endif
                </p>
                <span class="mt-5 inline-flex items-center gap-3 text-sm font-extrabold text-[#207b84]">
                    Ouvrir les commandes
                    <svg class="dashboard-action-arrow h-5 w-7" viewBox="0 0 28 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 10h22M17 3l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>

            <a
                href="{{ $nextPaidPayment ? route('admin.dashboard', ['payment' => $nextPaidPayment->id]).'#new-instance' : route('admin.payments.index') }}"
                @class([
                    'dashboard-action-card group relative overflow-hidden rounded-3xl border p-5 transition duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:ring-offset-2',
                    'dashboard-action-card--active border-emerald-300 bg-gradient-to-br from-emerald-50 to-white shadow-lg shadow-emerald-600/10 hover:-translate-y-0.5' => $availablePayments->isNotEmpty(),
                    'border-slate-200 bg-white hover:border-emerald-300' => $availablePayments->isEmpty(),
                ])
            >
                <div class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3M15 15l2 2 4-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="dashboard-action-dot flex h-9 min-w-9 items-center justify-center rounded-full px-2 text-sm font-black {{ $availablePayments->isNotEmpty() ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $availablePayments->count() }}</span>
                </div>
                <p class="mt-5 text-xs font-extrabold uppercase tracking-[.13em] text-emerald-700">Paiements confirmés</p>
                <h3 class="mt-2 text-lg font-extrabold leading-snug text-slate-950">
                    {{ $availablePayments->isNotEmpty() ? ($availablePayments->count() === 1 ? 'Accès client à préparer' : 'Accès clients à préparer') : 'Aucun accès en attente' }}
                </h3>
                <p class="mt-2 min-h-10 text-sm leading-5 text-slate-600">
                    @if ($nextPaidPayment)
                        {{ $nextPaidPayment->reference }} · {{ $nextPaidPayment->company_name }} · {{ number_format($nextPaidPayment->amount, 0, ',', ' ') }} {{ $nextPaidPayment->currency }}
                    @else
                        Aucun paiement initial confirmé n’attend la création de son instance.
                    @endif
                </p>
                <span class="mt-5 inline-flex items-center gap-3 text-sm font-extrabold text-emerald-700">
                    {{ $nextPaidPayment ? 'Préparer les accès' : 'Ouvrir les paiements' }}
                    <svg class="dashboard-action-arrow h-5 w-7" viewBox="0 0 28 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 10h22M17 3l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>

            <a
                href="#instances-title"
                @class([
                    'dashboard-action-card group relative overflow-hidden rounded-3xl border p-5 transition duration-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:ring-offset-2',
                    'dashboard-action-card--active border-amber-300 bg-gradient-to-br from-amber-50 to-white shadow-lg shadow-amber-600/10 hover:-translate-y-0.5' => $pendingCount > 0,
                    'border-slate-200 bg-white hover:border-amber-300' => $pendingCount === 0,
                ])
            >
                <div class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01M12 7h6M12 17h6" stroke-linecap="round"/></svg>
                    </span>
                    <span class="dashboard-action-dot flex h-9 min-w-9 items-center justify-center rounded-full px-2 text-sm font-black {{ $pendingCount > 0 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $pendingCount }}</span>
                </div>
                <p class="mt-5 text-xs font-extrabold uppercase tracking-[.13em] text-amber-700">Installation ERP</p>
                <h3 class="mt-2 text-lg font-extrabold leading-snug text-slate-950">
                    {{ $pendingCount > 0 ? ($pendingCount === 1 ? 'Instance à finaliser' : 'Instances à finaliser') : 'Installations finalisées' }}
                </h3>
                <p class="mt-2 min-h-10 text-sm leading-5 text-slate-600">
                    {{ $pendingCount > 0 ? 'Renseignez les accès ERP puis envoyez-les aux clients concernés.' : 'Aucune instance ne se trouve actuellement en cours d’installation.' }}
                </p>
                <span class="mt-5 inline-flex items-center gap-3 text-sm font-extrabold text-amber-700">
                    Voir les instances
                    <svg class="dashboard-action-arrow h-5 w-7" viewBox="0 0 28 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 10h22M17 3l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>

            <a
                href="{{ route('admin.demos.index') }}#demo-requests"
                @class([
                    'dashboard-action-card group relative overflow-hidden rounded-3xl border p-5 transition duration-300 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:ring-offset-2',
                    'dashboard-action-card--active border-violet-300 bg-gradient-to-br from-violet-50 to-white shadow-lg shadow-violet-600/10 hover:-translate-y-0.5' => $pendingDemoRequestCount > 0,
                    'border-slate-200 bg-white hover:border-violet-300' => $pendingDemoRequestCount === 0,
                ])
            >
                <div class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-600 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M9 9l6 3-6 3V9z" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="dashboard-action-dot flex h-9 min-w-9 items-center justify-center rounded-full px-2 text-sm font-black {{ $pendingDemoRequestCount > 0 ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $pendingDemoRequestCount }}</span>
                </div>
                <p class="mt-5 text-xs font-extrabold uppercase tracking-[.13em] text-violet-700">Demandes de démo</p>
                <h3 class="mt-2 text-lg font-extrabold leading-snug text-slate-950">
                    {{ $pendingDemoRequestCount > 0 ? ($pendingDemoRequestCount === 1 ? 'Demande de démo à traiter' : 'Demandes de démo à traiter') : 'Aucune demande en attente' }}
                </h3>
                <p class="mt-2 min-h-10 text-sm leading-5 text-slate-600">
                    @if ($latestPendingDemoRequest)
                        {{ $latestPendingDemoRequest->company_name ?: $latestPendingDemoRequest->fullname }} · {{ $latestPendingDemoRequest->email }}
                    @else
                        Toutes les demandes de démonstration ont reçu leurs accès.
                    @endif
                </p>
                <span class="mt-5 inline-flex items-center gap-3 text-sm font-extrabold text-violet-700">
                    Voir les demandes
                    <svg class="dashboard-action-arrow h-5 w-7" viewBox="0 0 28 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 10h22M17 3l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="admin-overview-title">
        <div class="mb-4">
            <h2 id="admin-overview-title" class="text-xl font-extrabold text-slate-950">Ma vue générale</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-3xl border border-[#2b909a]/35 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between"><p class="text-xs font-extrabold uppercase tracking-[.13em] text-slate-400">Commandes</p><span class="text-2xl font-black text-slate-950">{{ $totalCommercialRequestCount }}</span></div>
                <dl class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-center">
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Commandes</dt><dd class="mt-1 font-extrabold text-slate-800">{{ $orderCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Devis</dt><dd class="mt-1 font-extrabold text-slate-800">{{ $quoteRequestCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-[#207b84]">À traiter</dt><dd class="mt-1 font-extrabold text-[#207b84]">{{ $newCommercialRequestCount }}</dd></div>
                </dl>
            </article>

            <article class="rounded-3xl border border-emerald-300 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between"><p class="text-xs font-extrabold uppercase tracking-[.13em] text-slate-400">Paiements</p><span class="text-2xl font-black text-slate-950">{{ $totalPaymentCount }}</span></div>
                <dl class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-center">
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Payés</dt><dd class="mt-1 font-extrabold text-emerald-700">{{ $paidPaymentCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">En cours</dt><dd class="mt-1 font-extrabold text-amber-700">{{ $pendingPaymentCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Accès</dt><dd class="mt-1 font-extrabold text-[#207b84]">{{ $availablePayments->count() }}</dd></div>
                </dl>
            </article>

            <article class="rounded-3xl border border-blue-300 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between"><p class="text-xs font-extrabold uppercase tracking-[.13em] text-slate-400">Instances</p><span class="text-2xl font-black text-slate-950">{{ $totalCount }}</span></div>
                <dl class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-center">
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Actives</dt><dd class="mt-1 font-extrabold text-emerald-700">{{ $activeCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Installation</dt><dd class="mt-1 font-extrabold text-amber-700">{{ $pendingCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Suspendues</dt><dd class="mt-1 font-extrabold text-red-700">{{ $suspendedCount }}</dd></div>
                </dl>
            </article>

            <article class="rounded-3xl border {{ $pendingDemoRequestCount > 0 ? 'border-amber-300' : 'border-violet-300' }} bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between"><p class="text-xs font-extrabold uppercase tracking-[.13em] text-slate-400">Démonstrations</p><span class="text-2xl font-black text-slate-950">{{ $totalDemoRequestCount }}</span></div>
                <dl class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-center">
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Demandes</dt><dd class="mt-1 font-extrabold text-slate-800">{{ $totalDemoRequestCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-amber-700">À traiter</dt><dd class="mt-1 font-extrabold text-amber-700">{{ $pendingDemoRequestCount }}</dd></div>
                    <div><dt class="text-[10px] font-bold uppercase text-slate-400">Accès créés</dt><dd class="mt-1 font-extrabold text-violet-700">{{ $demoCount }}</dd></div>
                </dl>
            </article>
        </div>
    </section>
    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Indicateurs administrateur">
        @php
            $metrics = [
                ['label' => 'Total entreprises', 'value' => $totalCount, 'note' => 'Comptes enregistrés', 'color' => 'text-[#207b84]', 'bg' => 'bg-[#e5f5f6]', 'icon' => 'company'],
                ['label' => 'Instances actives', 'value' => $activeCount, 'note' => 'Clients opérationnels', 'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'icon' => 'server'],
                ['label' => 'Échéances sous 7 jours', 'value' => $alerts, 'note' => 'À surveiller', 'color' => $alerts > 0 ? 'text-red-700' : 'text-slate-600', 'bg' => $alerts > 0 ? 'bg-red-50' : 'bg-slate-100', 'icon' => 'alert'],
            ];
        @endphp
        @foreach ($metrics as $metric)
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-xs font-extrabold uppercase tracking-[.13em] text-slate-400">{{ $metric['label'] }}</p><p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $metric['value'] }}</p><p class="mt-1 text-xs font-semibold text-slate-500">{{ $metric['note'] }}</p></div>
                    <span class="flex shrink-0 items-center justify-center {{ $metric['color'] }}">
                        @if ($metric['icon'] === 'company')<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1M14 10h1M9 14h1M14 14h1M10 21v-3h4v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($metric['icon'] === 'clock')<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
                        @elseif ($metric['icon'] === 'server')<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01M11 7h7M11 17h7" stroke-linecap="round"/></svg>
                        @else<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 4l9 16H3l9-16z" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke-linecap="round"/></svg>@endif
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    @if (session('status'))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" role="status"><svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-extrabold">Une vérification est nécessaire.</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section id="new-instance" class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-labelledby="instance-create-title">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <div class="flex items-start gap-3">
                <span class="flex shrink-0 items-center justify-center text-[#207b84]">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="4" width="13" height="6" rx="2"/><rect x="3" y="14" width="13" height="6" rx="2"/>
                        <path d="M7 7h.01M7 17h.01M11 7h2M11 17h2M20 8v8M16 12h8" stroke-linecap="round"/>
                    </svg>
                </span>
                <div><h2 id="instance-create-title" class="text-lg font-extrabold text-slate-950">Créer une instance payée</h2><p class="mt-1 text-xs leading-5 text-slate-500">Seuls les paiements confirmés par Moneroo sont proposés.</p></div>
            </div>
        </div>

        <div class="p-5 sm:p-7">
            @if ($availablePayments->isEmpty())
                <p class="py-2 text-sm leading-6 text-amber-700"><strong class="font-extrabold">Aucun paiement disponible.</strong> Créez un paiement et attendez sa confirmation avant de lancer l’installation.</p>
            @else
                <form action="{{ route('admin.companies.store') }}" method="POST" id="instance-create-form">
                    @csrf
                    <div class="grid min-w-0 gap-5 lg:grid-cols-2">
                        <div class="min-w-0">
                            <label for="instance-payment" class="mb-2 block text-xs font-extrabold uppercase tracking-[.1em] text-slate-500">Paiement confirmé</label>
                            <select id="instance-payment" name="payment_id" class="min-h-12 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-[#2b909a] focus:ring-[#2b909a]" required>
                                <option value="">Sélectionner un paiement payé</option>
                                @foreach ($availablePayments as $payment)
                                    <option value="{{ $payment->id }}" data-company="{{ $payment->company_name }}" data-email="{{ $payment->customer_email }}" data-package="{{ $payment->package }}" @selected((string) old('payment_id', $selectedPaymentId) === (string) $payment->id)>{{ $payment->reference }} · {{ $payment->company_name }} · {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label id="label-domain" for="input-domain" class="mb-2 block text-xs font-extrabold uppercase tracking-[.1em] text-slate-500">Identifiant d’instance</label>
                            <input type="text" id="input-domain" name="domain" value="{{ old('domain') }}" class="min-h-12 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-[#2b909a] focus:ring-[#2b909a]" required placeholder="entreprise">
                            <p id="hint-domain" class="mt-2 break-all text-xs text-slate-400">Adresse : <span class="font-bold text-[#207b84]">...</span>.solutcloud.com</p>
                        </div>
                        <div class="min-w-0 rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:col-span-2">
                            <p class="text-[11px] font-extrabold uppercase tracking-[.12em] text-slate-400">Client sélectionné</p><p id="selected-company" class="mt-2 break-words font-extrabold text-slate-900">—</p><p id="selected-email" class="mt-1 break-all text-xs text-slate-500">Sélectionnez un paiement</p>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-[#2b909a]/15 bg-[#2b909a]/[.04] p-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="max-w-3xl text-xs leading-5 text-slate-600">Le client recevra les informations d’installation puis le lien sécurisé pour créer son mot de passe.</p>
                        <button type="submit" class="min-h-12 shrink-0 rounded-xl bg-[#2b909a] px-6 text-sm font-extrabold text-white shadow-lg shadow-[#2b909a]/15 transition hover:bg-[#217b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30">Créer et notifier</button>
                    </div>
                </form>
            @endif
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-labelledby="instances-title">
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-7">
            <div><p class="text-xs font-extrabold uppercase tracking-[.15em] text-[#2b909a]">Infrastructure clients</p><h2 id="instances-title" class="mt-1 text-lg font-extrabold text-slate-950">Instances Déployées</h2></div>
            <span class="shrink-0 rounded-full bg-[#e5f5f6] px-3 py-1 text-xs font-extrabold text-[#207b84]">{{ $companies->count() }}</span>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($companies as $company)
                <article class="min-w-0 rounded-2xl border border-slate-200 p-4">
                    <div class="flex min-w-0 items-start justify-between gap-3"><div class="min-w-0"><h3 class="break-words font-extrabold text-slate-950">{{ $company->name }}</h3><p class="mt-1 break-all text-xs font-semibold text-[#207b84]">{{ $company->instance_url }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-extrabold uppercase {{ $company->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($company->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ $company->status === 'active' ? 'Actif' : ($company->status === 'pending' ? 'Installation' : 'Suspendu') }}</span></div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-slate-400">Offre</dt><dd class="mt-1 font-extrabold uppercase text-slate-700">{{ $company->package }}</dd></div><div><dt class="text-slate-400">Échéance</dt><dd class="mt-1 font-extrabold text-slate-700">{{ $company->expires_at?->format('d/m/Y') ?: '—' }}</dd></div><div class="col-span-2"><dt class="text-slate-400">Contact</dt><dd class="mt-1 break-all font-semibold text-slate-700">{{ $company->email }}</dd></div></dl>
                    <div class="mt-4">
                        @if ($company->status === 'pending')
                            <button type="button" data-finalize-company data-company-id="{{ $company->id }}" data-company-name="{{ $company->name }}" class="min-h-11 w-full rounded-xl bg-[#2b909a] px-4 text-xs font-extrabold uppercase text-white">Finaliser l’activation</button>
                        @elseif ($company->status === 'active')
                            <form action="{{ route('admin.suspend', $company->id) }}" method="POST" onsubmit="return confirm('Suspendre cet accès ?')">@csrf<button class="min-h-11 w-full rounded-xl border border-red-200 bg-red-50 px-4 text-xs font-extrabold uppercase text-red-700">Suspendre</button></form>
                        @else
                            <form action="{{ route('admin.activate', $company->id) }}" method="POST" class="grid grid-cols-[1fr_auto] gap-2">@csrf<select name="duration" class="min-h-11 rounded-xl border-slate-300 text-sm" required>@foreach ([0 => '0 mois · échéance inchangée', 1 => '1 mois', 2 => '2 mois', 3 => '3 mois', 6 => '6 mois', 12 => '12 mois'] as $duration => $label)<option value="{{ $duration }}">{{ $label }}</option>@endforeach</select><button class="min-h-11 rounded-xl bg-emerald-600 px-4 text-xs font-extrabold uppercase text-white">Réactiver</button></form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm font-semibold text-slate-400">Aucune instance déployée pour le moment.</div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="admin-data-table min-w-[1050px] w-full text-sm">
                <thead class="bg-[#2b909a] text-white"><tr><th class="px-6 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Entreprise</th><th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Contact</th><th class="px-5 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest">Offre</th><th class="px-5 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest">Statut</th><th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Échéance</th><th class="px-6 py-4 text-right text-[10px] font-extrabold uppercase tracking-widest">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($companies as $company)
                        <tr class="align-middle transition hover:bg-[#2b909a]/[.03]">
                            <td class="px-6 py-5"><p class="font-extrabold text-slate-900">{{ $company->name }}</p><p class="mt-1 max-w-64 break-all text-xs text-[#207b84]">{{ $company->instance_url }}</p></td>
                            <td class="px-5 py-5"><p class="break-all font-semibold text-slate-700">{{ $company->email }}</p><p class="mt-1 text-xs text-slate-400">{{ $company->phone ?: 'Non renseigné' }}</p></td>
                            <td class="px-5 py-5 text-center"><span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-extrabold uppercase text-slate-700">{{ $company->package }}</span></td>
                            <td class="px-5 py-5 text-center"><span class="rounded-full px-3 py-1 text-[10px] font-extrabold uppercase {{ $company->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($company->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ $company->status === 'active' ? 'Actif' : ($company->status === 'pending' ? 'Installation' : 'Suspendu') }}</span></td>
                            <td class="px-5 py-5"><p class="font-bold text-slate-700">{{ $company->expires_at?->format('d/m/Y') ?: '—' }}</p>@if ($company->expires_at)<p class="mt-1 text-xs {{ now()->gt($company->expires_at) ? 'font-bold text-red-600' : 'text-slate-400' }}">{{ now()->gt($company->expires_at) ? 'Expirée' : ((int) now()->diffInDays($company->expires_at)).' jours restants' }}</p>@endif</td>
                            <td class="px-6 py-5 text-right">
                                @if ($company->status === 'pending')
                                    <button type="button" data-finalize-company data-company-id="{{ $company->id }}" data-company-name="{{ $company->name }}" class="min-h-9 rounded-lg bg-[#2b909a] px-3 text-[10px] font-extrabold uppercase text-white">Finaliser</button>
                                @elseif ($company->status === 'active')
                                    <form action="{{ route('admin.suspend', $company->id) }}" method="POST" onsubmit="return confirm('Suspendre cet accès ?')">@csrf<button class="min-h-9 rounded-lg border border-red-200 bg-red-50 px-3 text-[10px] font-extrabold uppercase text-red-700">Suspendre</button></form>
                                @else
                                    <form action="{{ route('admin.activate', $company->id) }}" method="POST" class="inline-flex items-center gap-2">@csrf<select name="duration" class="min-h-9 rounded-lg border-slate-300 py-1 text-xs" required>@foreach ([0 => '0 mois · échéance inchangée', 1 => '1 mois', 2 => '2 mois', 3 => '3 mois', 6 => '6 mois', 12 => '12 mois'] as $duration => $label)<option value="{{ $duration }}">{{ $label }}</option>@endforeach</select><button class="min-h-9 rounded-lg bg-emerald-600 px-3 text-[10px] font-extrabold uppercase text-white">Réactiver</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-16 text-center text-sm font-semibold text-slate-400">Aucune instance déployée pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <dialog id="modal-finalize" class="m-auto w-[min(520px,calc(100vw-2rem))] rounded-3xl border-0 p-0 shadow-2xl backdrop:bg-slate-950/60 backdrop:backdrop-blur-sm">
        <div class="bg-white p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-extrabold uppercase tracking-[.16em] text-[#2b909a]">Installation ERP</p><h2 class="mt-2 text-xl font-extrabold text-slate-950">Finaliser l’instance</h2><p id="finalize-client-name" class="mt-1 text-sm text-slate-500"></p></div><button type="button" data-close-finalize class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-label="Fermer"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg></button></div>
            <form id="form-finalize" method="POST" class="mt-6">@csrf<div class="space-y-4"><div><label for="erp_login" class="mb-2 block text-xs font-extrabold uppercase tracking-[.1em] text-slate-500">Identifiant ERP créé</label><input id="erp_login" type="text" name="erp_login" class="min-h-12 w-full rounded-xl border-slate-300 text-sm focus:border-[#2b909a] focus:ring-[#2b909a]" required></div><div><label for="erp_password" class="mb-2 block text-xs font-extrabold uppercase tracking-[.1em] text-slate-500">Mot de passe ERP créé</label><input id="erp_password" type="text" name="erp_password" class="min-h-12 w-full rounded-xl border-slate-300 text-sm focus:border-[#2b909a] focus:ring-[#2b909a]" required></div></div><div class="mt-6 grid gap-3 sm:grid-cols-2"><button type="button" data-close-finalize class="min-h-12 rounded-xl border border-slate-200 px-5 text-sm font-extrabold text-slate-600">Annuler</button><button type="submit" class="min-h-12 rounded-xl bg-[#2b909a] px-5 text-sm font-extrabold text-white">Activer et envoyer l’e-mail</button></div></form>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentSelect = document.getElementById('instance-payment');
            const inputDomain = document.getElementById('input-domain');
            const labelDomain = document.getElementById('label-domain');
            const hintDomain = document.getElementById('hint-domain');
            const selectedCompany = document.getElementById('selected-company');
            const selectedEmail = document.getElementById('selected-email');
            const finalizeModal = document.getElementById('modal-finalize');
            const finalizeForm = document.getElementById('form-finalize');
            const finalizeName = document.getElementById('finalize-client-name');

            const slugify = (text) => text.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9-]+/g, '').replace(/--+/g, '-').replace(/^-|-$/g, '');
            const updateDomainHint = (packageName) => {
                if (!inputDomain || !hintDomain) return;
                hintDomain.innerHTML = packageName === 'premium'
                    ? 'Domaine final : <span class="font-bold text-[#207b84]">' + (inputDomain.value || 'entreprise.com') + '</span>'
                    : 'Adresse : <span class="font-bold text-[#207b84]">' + (inputDomain.value || '...') + '</span>.solutcloud.com';
            };
            const fillPayment = () => {
                if (!paymentSelect || !inputDomain) return;
                const option = paymentSelect.options[paymentSelect.selectedIndex];
                const packageName = option?.dataset.package || '';
                const companyName = option?.dataset.company || '';
                selectedCompany.textContent = companyName || '—';
                selectedEmail.textContent = option?.dataset.email || 'Sélectionnez un paiement';
                labelDomain.textContent = packageName === 'premium' ? 'Nom de domaine dédié' : 'Identifiant d’instance';
                inputDomain.placeholder = packageName === 'premium' ? 'entreprise.com' : 'entreprise';
                if (option?.value) inputDomain.value = packageName === 'premium' ? '' : slugify(companyName);
                updateDomainHint(packageName);
            };
            paymentSelect?.addEventListener('change', fillPayment);
            inputDomain?.addEventListener('input', () => updateDomainHint(paymentSelect?.options[paymentSelect.selectedIndex]?.dataset.package || ''));
            if (paymentSelect?.value) fillPayment();

            document.querySelectorAll('[data-finalize-company]').forEach((button) => button.addEventListener('click', () => {
                finalizeName.textContent = 'Client : ' + button.dataset.companyName;
                finalizeForm.action = "{{ url('/admin/companies') }}/" + button.dataset.companyId + '/finalize';
                finalizeModal.showModal();
            }));
            document.querySelectorAll('[data-close-finalize]').forEach((button) => button.addEventListener('click', () => finalizeModal.close()));
            finalizeModal?.addEventListener('click', (event) => { if (event.target === finalizeModal) finalizeModal.close(); });
        });
    </script>
</x-admin-layout>
