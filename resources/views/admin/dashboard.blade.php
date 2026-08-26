<x-admin-layout
    title="SOLUTCLOUD — Administration"
    page-title="Tableau de bord"
    description="Vue d’ensemble des clients, instances et échéances."
>
    <section class="relative overflow-hidden rounded-3xl bg-[#0a3034] px-5 py-7 text-white shadow-xl shadow-[#0a3034]/10 sm:px-8 sm:py-9">
        <div class="pointer-events-none absolute -right-20 -top-28 h-72 w-72 rounded-full bg-[#2b909a]/40 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#8bd4da]">Centre de pilotage</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Bienvenue dans SOLUTCLOUD Gestion</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200 sm:text-base">Suivez vos clients, confirmez les installations et gérez les opérations commerciales depuis un espace unique.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:flex">
                <a href="{{ route('admin.payments.index') }}#payment-form" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-4 text-center text-sm font-extrabold text-[#176f78] transition hover:bg-[#e5f5f6] focus:outline-none focus:ring-2 focus:ring-white/50">Nouveau paiement</a>
                <a href="#new-instance" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/20 bg-white/[.08] px-4 text-center text-sm font-extrabold text-white transition hover:bg-white/[.14] focus:outline-none focus:ring-2 focus:ring-white/50">Créer une instance</a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicateurs administrateur">
        @php
            $metrics = [
                ['label' => 'Total entreprises', 'value' => $totalCount, 'note' => 'Comptes enregistrés', 'color' => 'text-[#207b84]', 'bg' => 'bg-[#e5f5f6]', 'icon' => 'company'],
                ['label' => "En attente d'installation", 'value' => $pendingCount, 'note' => 'À préparer', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'icon' => 'clock'],
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
            <a href="{{ route('admin.payments.index') }}" class="inline-flex min-h-10 w-fit items-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-extrabold text-slate-700 transition hover:border-[#2b909a]/40 hover:text-[#207b84]">Ouvrir Paiement</a>
        </div>

        <div class="p-5 sm:p-7">
            @if ($availablePayments->isEmpty())
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-800"><strong>Aucun paiement disponible.</strong> Créez un paiement et attendez sa confirmation avant de lancer l’installation.</div>
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
                            <form action="{{ route('admin.activate', $company->id) }}" method="POST" class="grid grid-cols-[1fr_auto] gap-2">@csrf<select name="duration" class="min-h-11 rounded-xl border-slate-300 text-sm" required>@foreach ([1,2,3,6,12] as $duration)<option value="{{ $duration }}">{{ $duration }} mois</option>@endforeach</select><button class="min-h-11 rounded-xl bg-emerald-600 px-4 text-xs font-extrabold uppercase text-white">Réactiver</button></form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm font-semibold text-slate-400">Aucune instance déployée pour le moment.</div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-[1050px] w-full text-sm">
                <thead class="bg-[#0d282b] text-white"><tr><th class="px-6 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Entreprise</th><th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Contact</th><th class="px-5 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest">Offre</th><th class="px-5 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest">Statut</th><th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-widest">Échéance</th><th class="px-6 py-4 text-right text-[10px] font-extrabold uppercase tracking-widest">Actions</th></tr></thead>
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
                                    <form action="{{ route('admin.activate', $company->id) }}" method="POST" class="inline-flex items-center gap-2">@csrf<select name="duration" class="min-h-9 rounded-lg border-slate-300 py-1 text-xs" required>@foreach ([1,2,3,6,12] as $duration)<option value="{{ $duration }}">{{ $duration }} mois</option>@endforeach</select><button class="min-h-9 rounded-lg bg-emerald-600 px-3 text-[10px] font-extrabold uppercase text-white">Réactiver</button></form>
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
