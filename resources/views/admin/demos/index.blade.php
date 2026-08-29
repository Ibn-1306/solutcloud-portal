<x-admin-layout
    title="SOLUTCLOUD — Démonstrations"
    page-title="Démonstrations"
    description="Création et suivi des accès de démonstration."
>
    <style>
    .brand-teal { color: #2B909A; }
    .bg-brand-teal { background-color: #2B909A; }
    .input-field { border: 1px solid #e2e8f0; border-radius: 0.375rem; width: 100%; padding: 0.6rem; font-size: 15px; }
    .admin-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; }
    svg.icon-fix { width: 28px !important; height: 28px !important; flex-shrink: 0; }
    .admin-watermark {
        position: fixed; top: 52%; left: 50%; transform: translate(-50%, -50%);
        width: 1000px; height: 800px;
        background-image: url('{{ asset("img/favicon.png") }}');
        background-size: contain; background-repeat: no-repeat; background-position: center;
        opacity: 0.05; pointer-events: none; z-index: 0; transition: all 0.3s ease;
    }
    @media (max-width: 768px) {
        .admin-watermark { width: 300px; height: 300px; opacity: 0.03; }
        .admin-card { padding: 1rem; }
        .input-field { font-size: 16px; }
        h2 { font-size: 1.25rem; }
    }
    </style>

    <div class="py-8 relative min-h-screen">
        <div class="admin-watermark"></div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">

            {{-- ALERTES --}}
            @if (session('status'))
                <div class="relative z-50 mb-6 p-4 bg-brand-teal text-white font-bold rounded-xl shadow-lg flex items-center animate-bounce">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="relative z-50 mb-6 p-4 bg-red-600 text-white rounded-xl shadow-lg">
                    <ul class="list-disc list-inside font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section id="demo-requests" class="admin-card mb-8 overflow-hidden bg-white/90 backdrop-blur-sm" aria-labelledby="demo-requests-title">
                <div class="flex flex-col gap-2 border-b border-gray-100 bg-gray-50/70 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 id="demo-requests-title" class="text-sm font-black uppercase tracking-wider text-gray-800">Demandes de démonstration à traiter</h2>
                        <p class="mt-1 text-xs text-gray-500">Sélectionnez une demande pour préremplir la création de ses accès.</p>
                    </div>
                    <span class="w-fit rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $pendingDemoRequests->isNotEmpty() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $pendingDemoRequests->count() }} en attente</span>
                </div>

                <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($pendingDemoRequests as $request)
                        <article class="rounded-2xl border border-violet-200 bg-violet-50/40 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0"><h3 class="break-words font-extrabold text-gray-900">{{ $request->company_name ?: $request->fullname }}</h3><p class="mt-1 break-all text-xs font-semibold text-violet-700">{{ $request->email }}</p></div>
                                <span class="shrink-0 text-[10px] font-bold text-gray-400">{{ $request->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="mt-3 text-xs leading-5 text-gray-600">{{ $request->fullname }}{{ $request->profile ? ' · '.$request->profile : '' }}{{ $request->phone ? ' · '.$request->phone : '' }}</p>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500">{{ $request->message }}</p>
                            <button type="button" data-prepare-demo data-company="{{ $request->company_name }}" data-email="{{ $request->email }}" data-phone="{{ $request->phone }}" class="mt-4 min-h-10 w-full rounded-lg bg-violet-600 px-4 text-xs font-black uppercase tracking-wide text-white transition hover:bg-violet-700">Préparer les accès</button>
                        </article>
                    @empty
                        <p class="py-5 text-sm font-semibold text-gray-500 md:col-span-2 xl:col-span-3">Aucune demande de démonstration n’attend de traitement.</p>
                    @endforelse
                </div>
            </section>
            {{-- FORMULAIRE D'ENVOI DE DEMO --}}
            <div id="demo-create-form" class="admin-card p-10 mb-12 bg-white/90 backdrop-blur-sm">
                <div class="flex items-center mb-8 border-b border-gray-100 pb-4">
                    <svg class="icon-fix brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-extrabold text-gray-800 uppercase">Envoyer une démonstration</h3>
                </div>

                <form action="{{ route('admin.demos.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dénomination Sociale</label>
                            <input id="demo-company-name" type="text" name="company_name" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="Saisir la raison sociale">
                        </div>
                        <div>
                            <label for="demo-subdomain" class="block text-xs font-bold text-gray-500 uppercase mb-1">Identifiant d'instance</label>
                            <input id="demo-subdomain" type="text" name="subdomain" value="{{ \App\Models\Demo::DEFAULT_SUBDOMAIN }}" class="input-field cursor-not-allowed bg-slate-100 text-slate-500" readonly aria-readonly="true">
                            <p class="mt-1.5 text-xs text-gray-400">Adresse fixe : <strong class="text-[#237781]">https://demo.solutcloud.com</strong></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Client</label>
                            <input id="demo-email" type="email" name="email" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="client@email.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Téléphone Client</label>
                            <input id="demo-phone" type="tel" name="phone" value="{{ old('phone') }}" class="input-field focus:border-cyan-600 focus:ring-0" placeholder="Numéro de téléphone" autocomplete="tel" data-phone-input>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Identifiant ERP</label>
                            <input type="text" name="erp_login" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="ex: admin_solut">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mot de passe ERP</label>
                            <input type="text" name="erp_password" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="Saisir le mot de passe">
                        </div>
                    </div>
                    <div class="mt-8 text-right">
                        <button type="submit" class="bg-brand-teal hover:bg-cyan-800 text-white font-bold py-3 px-5 rounded-lg shadow-lg transition-all uppercase text-sm tracking-widest">
                            Envoyer démo
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLEAU DES DEMOS DEPLOYEES --}}
            <div class="admin-card overflow-hidden bg-white/90 backdrop-blur-sm">
                <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-tighter">Démos déployées</h3>
                    <span class="px-3 py-1 bg-brand-teal/10 text-brand-teal text-[10px] font-black rounded-full uppercase">
                        {{ $demos->count() }} Démo(s)
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="admin-data-table min-w-full text-sm">
                        <thead class="bg-[#2b909a] text-white">
                            <tr>
                                <th class="px-8 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Entreprise</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Email</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Téléphone</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">URL</th>
                                <th class="px-8 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Date de début</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($demos as $demo)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                <td class="px-8 py-5 font-bold text-gray-900">{{ $demo->company_name }}</td>
                                <td class="px-6 py-5">
                                    <a href="mailto:{{ $demo->email }}" class="text-gray-600 hover:text-teal-600 hover:underline">{{ $demo->email }}</a>
                                </td>
                                <td class="px-6 py-5">
                                    @if($demo->phone)
                                        <a href="tel:{{ $demo->phone }}" class="text-gray-600 hover:text-teal-600 hover:underline">{{ $demo->phone }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-teal-600 font-medium text-[11px]">{{ $demo->url }}</td>
                                <td class="px-8 py-5 text-gray-600">{{ $demo->starts_at ? $demo->starts_at->format('d/m/Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center align-middle">

                                    <div class="flex flex-col items-center justify-center text-gray-400 font-semibold">

                                        <svg class="w-10 h-10 mb-3 text-gray-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>

                                        <span class="text-sm">
                                            Aucune démonstration déployée pour le moment.
                                        </span>

                                    </div>

                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('[data-prepare-demo]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('demo-company-name').value = button.dataset.company || '';
                document.getElementById('demo-email').value = button.dataset.email || '';

                const phone = document.getElementById('demo-phone');
                phone.value = button.dataset.phone || '';
                phone.dispatchEvent(new CustomEvent('phone:set-number', {
                    detail: { number: button.dataset.phone || '' },
                }));

                document.getElementById('demo-create-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</x-admin-layout>
