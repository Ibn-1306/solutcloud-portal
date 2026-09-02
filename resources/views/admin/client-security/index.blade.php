<x-admin-layout
    title="SOLUTCLOUD — Sécurité clients"
    page-title="Sécurité clients"
    description="Activation des comptes, réinitialisation des mots de passe et suivi des envois."
>
    <style>
        .security-card { background:#fff; border:1px solid #e5edef; border-radius:18px; box-shadow:0 10px 30px rgba(15,23,42,.05); }
        .security-input { width:100%; min-height:46px; border:1px solid #d7e2e5; border-radius:11px; padding:.72rem .9rem; color:#1f2937; background:#fff; }
        .security-input:focus { border-color:#2b909a; box-shadow:0 0 0 3px rgba(43,144,154,.13); outline:none; }
    </style>

    <div class="mx-auto max-w-7xl space-y-7 py-8">
        @if(session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800" role="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-800" role="alert"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicateurs de sécurité client">
            <article class="security-card border-l-4 border-l-[#2b909a] p-5"><p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">Comptes clients</p><p class="mt-2 text-3xl font-black text-slate-950">{{ $totalClientCount }}</p><p class="mt-1 text-xs text-slate-500">Comptes enregistrés</p></article>
            <article class="security-card border-l-4 border-l-amber-500 p-5"><p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">À activer</p><p class="mt-2 text-3xl font-black text-amber-700">{{ $pendingActivationCount }}</p><p class="mt-1 text-xs text-slate-500">Mot de passe jamais défini</p></article>
            <article class="security-card border-l-4 border-l-emerald-500 p-5"><p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">Comptes activés</p><p class="mt-2 text-3xl font-black text-emerald-700">{{ $initializedClientCount }}</p><p class="mt-1 text-xs text-slate-500">Accès initialisé</p></article>
            <article class="security-card border-l-4 border-l-blue-500 p-5"><p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">Liens envoyés</p><p class="mt-2 text-3xl font-black text-blue-700">{{ $sentLinkCount }}</p><p class="mt-1 text-xs text-slate-500">Historique confirmé</p></article>
        </section>

        <section class="security-card overflow-hidden" aria-labelledby="secure-link-title">
            <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-5">
                <p class="text-xs font-black uppercase tracking-[.15em] text-[#2b909a]">Action sécurisée</p>
                <h2 id="secure-link-title" class="mt-1 text-xl font-extrabold text-slate-950">Envoyer un lien au client</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Le système choisit automatiquement une activation initiale ou une réinitialisation selon l’état du compte. Aucun mot de passe client n’est visible ni communiqué.</p>
            </div>
            <form method="POST" action="{{ route('admin.client-security.send') }}" class="grid gap-5 p-6 lg:grid-cols-[minmax(0,1fr)_minmax(280px,.7fr)_auto] lg:items-end lg:p-8">
                @csrf
                <div>
                    <label for="security-user" class="mb-2 block text-[10px] font-black uppercase tracking-wider text-slate-500">Compte client</label>
                    <select id="security-user" name="user_id" class="security-input" required>
                        <option value="">Sélectionner un client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" data-state="{{ $client->password_initialized_at ? 'reset' : 'activation' }}" data-company="{{ $client->company?->name ?: 'Entreprise non rattachée' }}" data-email="{{ $client->email }}" @selected((string) old('user_id') === (string) $client->id)>
                                {{ $client->name }} · {{ $client->email }} · {{ $client->company?->name ?: 'Sans entreprise' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="security-action-preview" class="min-h-[74px] rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3" aria-live="polite">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Action prévue</p>
                    <p data-security-action class="mt-1 text-sm font-extrabold text-slate-700">Sélectionnez un compte client.</p>
                    <p data-security-detail class="mt-1 text-xs text-slate-500">L’état du compte déterminera le type d’e-mail.</p>
                </div>
                <button class="min-h-[46px] rounded-xl bg-[#2b909a] px-6 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-[#2b909a]/15 transition hover:bg-[#237781] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30">Envoyer le lien sécurisé</button>
            </form>
        </section>

        <section class="security-card overflow-hidden" aria-labelledby="security-history-title">
            <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/70 px-6 py-5 xl:flex-row xl:items-end xl:justify-between">
                <div><p class="text-xs font-black uppercase tracking-[.15em] text-[#2b909a]">Traçabilité</p><h2 id="security-history-title" class="mt-1 text-xl font-extrabold text-slate-950">Tableau de suivi</h2><p class="mt-1 text-xs text-slate-500">Les envois les plus récents apparaissent en premier.</p></div>
                <form method="GET" action="{{ route('admin.client-security.index') }}" class="grid gap-2 sm:grid-cols-2 xl:grid-cols-[220px_160px_150px_auto]">
                    <input name="search" value="{{ request('search') }}" class="security-input text-sm" placeholder="Client, e-mail, entreprise">
                    <select name="type" class="security-input text-sm"><option value="">Tous les types</option><option value="activation" @selected(request('type') === 'activation')>Activation</option><option value="reset" @selected(request('type') === 'reset')>Mot de passe oublié</option></select>
                    <select name="status" class="security-input text-sm"><option value="">Tous les statuts</option><option value="sent" @selected(request('status') === 'sent')>Envoyé</option><option value="failed" @selected(request('status') === 'failed')>Échec</option><option value="pending" @selected(request('status') === 'pending')>En cours</option></select>
                    <div class="flex gap-2"><button class="min-h-[46px] rounded-xl bg-slate-900 px-4 text-xs font-black uppercase text-white">Filtrer</button><a href="{{ route('admin.client-security.index') }}" class="inline-flex min-h-[46px] items-center rounded-xl border border-slate-300 px-4 text-xs font-black uppercase text-slate-600">Effacer</a></div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-data-table min-w-full text-sm">
                    <thead class="bg-[#2b909a] text-white"><tr><th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Client</th><th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Entreprise</th><th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Type de lien</th><th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Statut</th><th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Date</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($links as $link)
                            @php
                                $typeClass = $link->type === 'activation' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800';
                                $statusClass = match($link->status) { 'sent' => 'bg-emerald-100 text-emerald-800', 'failed' => 'bg-red-100 text-red-800', default => 'bg-slate-100 text-slate-700' };
                            @endphp
                            <tr class="align-top transition hover:bg-[#2b909a]/[.035]">
                                <td class="px-5 py-5"><p class="font-extrabold text-slate-900">{{ $link->user?->name ?: 'Compte supprimé' }}</p><p class="mt-1 text-xs text-[#237781]">{{ $link->user?->email ?: '—' }}</p></td>
                                <td class="px-5 py-5 font-semibold text-slate-600">{{ $link->user?->company?->name ?: '—' }}</td>
                                <td class="px-5 py-5"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $typeClass }}">{{ $link->typeLabel() }}</span></td>
                                <td class="px-5 py-5 text-center"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $statusClass }}">{{ $link->statusLabel() }}</span>@if($link->failure_reason)<p class="mx-auto mt-2 max-w-[220px] text-[10px] leading-4 text-red-600">{{ $link->failure_reason }}</p>@endif</td>

                                <td class="px-5 py-5 whitespace-nowrap text-xs text-slate-500">{{ ($link->sent_at ?? $link->created_at)?->format('d/m/Y') }}<span class="mt-1 block text-slate-400">{{ ($link->sent_at ?? $link->created_at)?->format('H:i') }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-16 text-center"><p class="font-bold text-slate-500">Aucun envoi sécurisé dans le suivi.</p><p class="mt-1 text-xs text-slate-400">Le premier envoi apparaîtra automatiquement ici.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($links->hasPages())<div class="border-t border-slate-100 px-6 py-4">{{ $links->links() }}</div>@endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('security-user');
            const action = document.querySelector('[data-security-action]');
            const detail = document.querySelector('[data-security-detail]');
            const updatePreview = () => {
                const option = select?.options[select.selectedIndex];
                if (!option?.value) { action.textContent = 'Sélectionnez un compte client.'; detail.textContent = 'L’état du compte déterminera le type d’e-mail.'; return; }
                const activation = option.dataset.state === 'activation';
                action.textContent = activation ? 'Renvoyer l’activation initiale' : 'Réinitialiser un mot de passe oublié';
                detail.textContent = `${option.dataset.company} · ${option.dataset.email}`;
            };
            select?.addEventListener('change', updatePreview);
            updatePreview();
        });
    </script>
</x-admin-layout>