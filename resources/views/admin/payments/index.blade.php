<x-admin-layout
    title="SOLUTCLOUD — Paiement"
    page-title="Paiement"
    description="Création des liens Moneroo et suivi des règlements clients."
>
    <style>
        .payment-card { background:#fff; border:1px solid #e8eef0; border-radius:16px; box-shadow:0 10px 32px rgba(15,23,42,.055); }
        .payment-input { width:100%; border:1px solid #dbe5e7; border-radius:10px; padding:.72rem .85rem; font-size:.875rem; color:#1f2937; background:#fff; }
        .payment-input:focus { border-color:#2b909a !important; box-shadow:0 0 0 3px rgba(43,144,154,.13); outline:none; }
        .payment-watermark { position:fixed; inset:16% 12%; z-index:0; opacity:.028; pointer-events:none; background:url('{{ asset('img/favicon.png') }}') center/contain no-repeat; }
        .payment-table th { white-space:nowrap; }
        @media(max-width:768px){ .payment-watermark{inset:30% 15%;} }
    </style>

    <div class="relative min-h-screen py-8">
        <div class="payment-watermark" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="payment-card border-l-4 border-l-gray-800 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Total paiements</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $totalCount }}</p>
                </div>
                <div class="payment-card border-l-4 border-l-amber-500 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">En attente</p>
                    <p class="mt-2 text-2xl font-black text-amber-600">{{ $pendingCount }}</p>
                </div>
                <div class="payment-card border-l-4 border-l-emerald-500 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Payés</p>
                    <p class="mt-2 text-2xl font-black text-emerald-700">{{ $paidCount }}</p>
                </div>
            </div>

            @if($paymentCurrency !== 'XOF')
                <p class="px-1 text-sm leading-6 text-[#176f78]">
                    <strong class="font-extrabold">Mode de test Moneroo :</strong> la passerelle prédéfinie Sandbox utilise {{ $paymentCurrency }}. Les montants saisis ici sont fictifs et ne remplacent pas les tarifs commerciaux en XOF.
                </p>
            @endif

            <section class="payment-card overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/70 px-6 py-5">
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-800">Créer un paiement</h3>
                    <p class="mt-1 text-xs text-gray-500">Renseignez le montant convenu. Le lien Moneroo sera créé puis envoyé au client par e-mail.</p>
                </div>

                <form method="POST" action="{{ route('admin.payments.store') }}" class="p-6 lg:p-8" id="payment-form">
                    @csrf
                    <div class="mb-6">
                        <label for="website_lead_id" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Commande ou demande associée</label>
                        <select id="website_lead_id" name="website_lead_id" class="payment-input">
                            <option value="">Création manuelle</option>
                            @foreach($commercialRequests as $lead)
                                <option
                                    value="{{ $lead->id }}"
                                    data-name="{{ $lead->fullname }}"
                                    data-email="{{ $lead->email }}"
                                    data-phone="{{ $lead->phone }}"
                                    data-company="{{ $lead->company_name }}"
                                    data-package="{{ strtolower($lead->offer ?? '') }}"
                                    data-description="{{ $lead->clientNotes() }}"
                                    @selected((string) old('website_lead_id', $preselectedLeadId) === (string) $lead->id)
                                >
                                    {{ $lead->commercialReference() }} · {{ $lead->company_name ?: $lead->fullname }} · {{ $lead->offer ?: 'Sans offre' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="customer_name" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Nom du client</label>
                            <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="payment-input" required>
                        </div>
                        <div>
                            <label for="customer_email" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">E-mail</label>
                            <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" class="payment-input" placeholder="client@email.com" required>
                        </div>
                        <div>
                            <label for="customer_phone" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Téléphone</label>
                            <input id="customer_phone" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" class="payment-input" placeholder="Numéro de téléphone" autocomplete="tel" data-phone-input>
                        </div>
                        <div>
                            <label for="company_name" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Entreprise</label>
                            <input id="company_name" name="company_name" value="{{ old('company_name') }}" class="payment-input" required>
                        </div>
                        <div>
                            <label for="package" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Offre</label>
                            <select id="package" name="package" class="payment-input" required>
                                <option value="" @selected(! old('package'))>Sélectionner une offre</option>
                                <option value="start" @selected(old('package') === 'start')>START</option>
                                <option value="business" @selected(old('package') === 'business')>BUSINESS</option>
                                <option value="premium" @selected(old('package') === 'premium')>PREMIUM</option>
                            </select>
                        </div>
                        <div>
                            <label for="amount" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">{{ $paymentCurrency === 'XOF' ? 'Montant convenu' : 'Montant fictif de test' }} ({{ $paymentCurrency }})</label>
                            <input id="amount" type="number" min="{{ $paymentCurrency === 'XOF' ? 100 : 1 }}" step="1" name="amount" value="{{ old('amount') }}" class="payment-input" required placeholder="{{ $defaultPaymentAmounts['start'] }}">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <p class="mb-2 text-[10px] font-black uppercase tracking-wider text-gray-500">Éléments compris dans l’offre</p>
                            <div id="offer-details-placeholder" class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-5 py-4 text-sm text-gray-500">
                                Sélectionnez une offre pour afficher son contenu.
                            </div>
                            @foreach($offerCatalog as $packageKey => $offer)
                                <article data-offer-details="{{ $packageKey }}" class="hidden rounded-xl border border-[#2b909a]/20 bg-[#2b909a]/[.045] p-5" aria-live="polite">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                                        <h4 class="text-sm font-black text-gray-900">SOLUTCLOUD {{ $offer['label'] }}</h4>
                                        <p class="text-xs font-semibold text-[#237781]">{{ $offer['audience'] }}</p>
                                    </div>
                                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                                        @foreach($offer['details'] as $detail)
                                            <div class="rounded-lg border border-white/80 bg-white px-4 py-3">
                                                <dt class="text-[10px] font-black uppercase tracking-wider text-gray-400">{{ $detail['label'] }}</dt>
                                                <dd class="mt-1 text-xs font-semibold leading-5 text-gray-700">{{ $detail['value'] }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </article>
                            @endforeach
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-gray-500">Précisions du client / Notes additionnelles</label>
                            <textarea id="description" name="description" maxlength="5000" rows="3" class="payment-input resize-y" placeholder="Précisions communiquées par le client">{{ old('description') }}</textarea>
                            <p class="mt-1.5 text-xs leading-5 text-gray-500">Champ facultatif : il contient uniquement les précisions du client. Une note transmise par le site vitrine est préremplie automatiquement ; son absence ne bloque jamais la création du lien.</p>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-col gap-4 py-2 sm:flex-row sm:items-center sm:justify-between">
                        <p class="max-w-3xl text-xs leading-5 text-gray-600">Une commande ou un devis ne peut créer qu’un seul dossier de paiement. Tant qu’il n’est pas payé, son lien peut être renvoyé ou régénéré depuis le tableau de suivi. Le statut <strong>Payé</strong> n’est appliqué qu’après vérification auprès de Moneroo.</p>
                        <button class="shrink-0 rounded-lg bg-[#2b909a] px-5 py-3 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-[#2b909a]/15 transition hover:bg-[#237781]">
                            Générer et envoyer
                        </button>
                    </div>
                </form>
            </section>

            <section class="payment-card overflow-hidden">
                <div class="flex flex-col gap-2 border-b border-gray-100 bg-gray-50/70 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800">Tableau de suivi</h3>
                        <p class="mt-1 text-xs text-gray-500">Actualisez un statut manuellement si le webhook tarde à arriver.</p>
                    </div>
                    <span class="w-fit rounded-full bg-gray-900 px-3 py-1 text-[10px] font-black uppercase text-white">{{ $payments->total() }} paiement(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="admin-data-table payment-table min-w-full text-sm">
                        <thead class="bg-[#2b909a] text-white">
                            <tr>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Référence</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Client</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Offre / Montant</th>
                                <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Paiement</th>
                                <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Statut évolution</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Suivi</th>
                                <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($payments as $payment)
                                @php
                                    $badge = match($payment->status) {
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'failed', 'cancelled' => 'bg-red-100 text-red-800',
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <tr id="payment-{{ $payment->id }}" class="align-top transition hover:bg-[#2b909a]/[.025]">
                                    <td class="px-5 py-5">
                                        <div class="font-black text-gray-900">{{ $payment->reference }}</div>
                                        <div class="mt-1 text-[10px] text-gray-400">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                        @if($payment->websiteLead)
                                            <div class="mt-1 text-[10px] font-bold text-[#237781]">{{ $payment->websiteLead->commercialReference() }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5">
                                        <div class="font-bold text-gray-800">{{ $payment->customer_name }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $payment->company_name }}</div>
                                        <a href="mailto:{{ $payment->customer_email }}" class="mt-1 block text-xs text-[#237781] hover:underline">{{ $payment->customer_email }}</a>
                                    </td>
                                    <td class="px-5 py-5">
                                        @php
                                            $purposeBadge = match($payment->purpose) {
                                                'renewal' => 'bg-violet-100 text-violet-800',
                                                'upgrade' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-cyan-100 text-cyan-800',
                                            };
                                        @endphp
                                        <div class="flex flex-wrap gap-1.5">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $purposeBadge }}">{{ $payment->purposeLabel() }}</span>
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase text-slate-700">{{ $payment->channelLabel() }}</span>
                                        </div>
                                        <div class="mt-2 font-black text-gray-800">SOLUTCLOUD {{ strtoupper($payment->package) }}</div>
                                        <div class="mt-1 text-sm font-bold text-[#237781]">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</div>
                                    </td>
                                    <td class="px-5 py-5 text-center">
                                        <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $badge }}">{{ $payment->statusLabel() }}</span>
                                        @if($payment->failure_reason)
                                            <p class="mx-auto mt-2 max-w-[180px] text-[10px] leading-4 text-red-600">{{ $payment->failure_reason }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 text-center">
                                        @if($payment->purpose === 'upgrade')
                                            @php
                                                $upgradeStatusClass = ! $payment->isPaid()
                                                    ? 'bg-amber-100 text-amber-800'
                                                    : ($payment->applied_at ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800');
                                            @endphp
                                            <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $upgradeStatusClass }}">{{ $payment->upgradeStatusLabel() }}</span>
                                            <p class="mt-2 text-[10px] font-bold text-gray-500">{{ $payment->applied_at ? 'BUSINESS actif' : 'Offre actuelle : '.strtoupper($payment->company?->package ?: 'START') }}</p>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 text-xs text-gray-500">
                                        <div>Lien : <strong class="text-gray-700">{{ $payment->link_sent_at ? $payment->link_sent_at->format('d/m H:i') : 'Non envoyé' }}</strong></div>
                                        <div class="mt-1">Vérifié : <strong class="text-gray-700">{{ $payment->verified_at ? $payment->verified_at->format('d/m H:i') : 'En attente' }}</strong></div>
                                        @if($payment->company)
                                            <div class="mt-2 font-bold text-emerald-700">Instance créée</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            @if($payment->status === 'draft')
                                                <form method="POST" action="{{ route('admin.payments.initialize', $payment) }}">@csrf
                                                    <button class="rounded-md bg-gray-900 px-2.5 py-1.5 text-[10px] font-black uppercase text-white">Créer le lien</button>
                                                </form>
                                            @elseif($payment->canRegenerateLink())
                                                <form method="POST" action="{{ route('admin.payments.initialize', $payment) }}" onsubmit="return confirm('Générer un nouveau lien Moneroo et l’envoyer au client ? L’ancien lien restera traçable.');">@csrf
                                                    <button class="rounded-md bg-blue-600 px-2.5 py-1.5 text-[10px] font-black uppercase text-white">Régénérer</button>
                                                </form>
                                            @endif
                                            @if($payment->canSendLink())
                                                <form method="POST" action="{{ route('admin.payments.send-link', $payment) }}">@csrf
                                                    <button class="rounded-md border border-[#2b909a]/30 px-2.5 py-1.5 text-[10px] font-black uppercase text-[#237781]">Renvoyer</button>
                                                </form>
                                            @endif
                                            @if($payment->moneroo_payment_id && !$payment->isPaid())
                                                <form method="POST" action="{{ route('admin.payments.refresh', $payment) }}">@csrf
                                                    <button class="rounded-md border border-gray-300 px-2.5 py-1.5 text-[10px] font-black uppercase text-gray-600">Actualiser</button>
                                                </form>
                                            @endif
                                            @if($payment->checkout_url && !$payment->isPaid())
                                                <a href="{{ $payment->checkout_url }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-gray-300 px-2.5 py-1.5 text-[10px] font-black uppercase text-gray-600">Ouvrir</a>
                                            @endif
                                            @if($payment->isPaid() && !$payment->company_id)
                                                <a href="{{ route('admin.dashboard', ['payment' => $payment->id]) }}#new-instance" class="rounded-md bg-emerald-600 px-2.5 py-1.5 text-[10px] font-black uppercase text-white">Créer instance</a>
                                            @endif
                                            @if($payment->purpose === 'upgrade' && $payment->isPaid() && !$payment->applied_at)
                                                <form method="POST" action="{{ route('admin.payments.finalize-upgrade', $payment) }}" onsubmit="return confirm('Finaliser le passage de ce client à SOLUTCLOUD BUSINESS ?');">@csrf
                                                    <button class="rounded-md border border-blue-200 bg-blue-600 px-2.5 py-1.5 text-[10px] font-black uppercase text-white">Finaliser l’évolution</button>
                                                </form>
                                            @endif
                                            @if($payment->canRemoveFromTracking())
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.payments.destroy', $payment) }}"
                                                    onsubmit="return confirm('Supprimer {{ $payment->reference }} du tableau de suivi ? Le lien Moneroo distant ne sera pas annulé.');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md border border-red-200 px-2.5 py-1.5 text-[10px] font-black uppercase text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-16 text-center text-sm font-semibold text-gray-400">Aucun paiement créé pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">{{ $payments->links() }}</div>
                @endif
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lead = document.getElementById('website_lead_id');
            const fields = {
                name: document.getElementById('customer_name'),
                email: document.getElementById('customer_email'),
                phone: document.getElementById('customer_phone'),
                company: document.getElementById('company_name'),
                package: document.getElementById('package'),
                description: document.getElementById('description'),
                amount: document.getElementById('amount'),
            };
            const defaultAmounts = @json($defaultPaymentAmounts);
            const prefilledFieldNames = ['name', 'email', 'phone', 'company', 'package', 'description', 'amount'];
            const offerDetailsPlaceholder = document.getElementById('offer-details-placeholder');
            const offerDetailCards = document.querySelectorAll('[data-offer-details]');

            const displayOfferDetails = (packageName = '') => {
                let hasVisibleOffer = false;

                offerDetailCards.forEach((card) => {
                    const isVisible = card.dataset.offerDetails === packageName;
                    card.classList.toggle('hidden', !isVisible);
                    hasVisibleOffer ||= isVisible;
                });

                offerDetailsPlaceholder?.classList.toggle('hidden', hasVisibleOffer);
            };

            const clearManualFields = () => {
                for (const key of prefilledFieldNames) {
                    if (fields[key]) fields[key].value = '';
                    if (key === 'phone') fields[key]?.dispatchEvent(new CustomEvent('phone:set-number', { detail: { number: '' } }));
                }
            };

            const fillFromLead = () => {
                const option = lead.options[lead.selectedIndex];

                if (!option || !option.value) {
                    clearManualFields();
                    displayOfferDetails('');
                    return;
                }

                for (const key of ['name', 'email', 'phone', 'company', 'package', 'description']) {
                    if (fields[key]) fields[key].value = option.dataset[key] || '';
                    if (key === 'phone') fields[key]?.dispatchEvent(new CustomEvent('phone:set-number', { detail: { number: option.dataset[key] || '' } }));
                }
                if (fields.amount) fields.amount.value = defaultAmounts[option.dataset.package] || '';
                displayOfferDetails(option.dataset.package || '');
            };

            lead.addEventListener('change', fillFromLead);
            fields.package?.addEventListener('change', () => {
                displayOfferDetails(fields.package.value);
                if (!lead.value && fields.amount && !fields.amount.value) {
                    fields.amount.value = defaultAmounts[fields.package.value] || '';
                }
            });

            if (lead.value) {
                fillFromLead();
            } else {
                displayOfferDetails(fields.package?.value || '');
            }
        });
    </script>
</x-admin-layout>
