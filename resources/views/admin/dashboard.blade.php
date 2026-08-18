<x-app-layout>
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

    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight uppercase tracking-widest">
            BIENVENUE SUR SOLUTCLOUD GESTION
        </h2>
    </x-slot>

    <div class="py-8 relative min-h-screen">
        <div class="admin-watermark"></div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">

            {{-- STATISTIQUES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                
                {{-- Carte 1 : Total Entreprises --}}
                <div class="admin-card p-5 border-l-4 border-cyan-500 bg-white/95 backdrop-blur-md shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400">Total Entreprises</p>
                            <h3 class="text-xl font-black text-gray-800 mt-1">
                                {{ $totalCount }} <span class="text-[10px] text-gray-500">ENTREPRISE(S)</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M9 9.75h1.5M9 12.75h1.5M9 15.75h1.5m4.5-9H15m-1.5 3H15m-1.5 3H15m-1.5 3H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Carte 2 : En attente d'installation --}}
                <div class="admin-card p-5 border-l-4 border-amber-500 bg-white/95 backdrop-blur-md shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400">En attente d'installation</p>
                            <h3 class="text-xl font-black {{ $pendingCount > 0 ? 'text-amber-600' : 'text-gray-800' }} mt-1">
                                {{ $pendingCount }} <span class="text-[10px] text-gray-500">A INSTALLER</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $pendingCount > 0 ? 'text-amber-600 animate-pulse' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Carte 3 : Instances Actives --}}
                <div class="admin-card p-5 border-l-4 border-brand-teal bg-white/95 backdrop-blur-md shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400">Instances Actives</p>
                            <h3 class="text-xl font-black text-gray-800 mt-1">
                                {{ $activeCount }} <span class="text-[10px] text-gray-500">CLIENT(S)</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-teal" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 015.25 4.5h13.5a2.25 2.25 0 012.25 2.25v5.25a2.25 2.25 0 01-2.25 2.25m-13.5 0L3 16.5m15.75-2.25l2.25 2.25M3.75 20.25h16.5M18.75 20.25h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Carte 4 : Alertes --}}
                <div class="admin-card p-5 border-l-4 {{ $alerts > 0 ? 'border-orange-500' : 'border-gray-200' }} bg-white/95 backdrop-blur-md shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400">Alertes Échéances (7j)</p>
                            <h3 class="text-xl font-black {{ $alerts > 0 ? 'text-orange-600' : 'text-gray-800' }} mt-1">
                                {{ $alerts }} <span class="text-[10px] text-gray-500">CRITIQUE(S)</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 {{ $alerts > 0 ? 'bg-orange-50 border-orange-100' : '' }} rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $alerts > 0 ? 'text-orange-600 animate-pulse' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

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

            {{-- FORMULAIRE D'AJOUT --}}
            <div class="admin-card p-10 mb-12 bg-white/90 backdrop-blur-sm">
                <div class="flex items-center mb-8 border-b pb-4">
                    <svg class="icon-fix brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <h3 class="text-lg font-extrabold text-gray-800 uppercase">Nouveau Client</h3>
                </div>

                <form action="{{ route('admin.companies.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dénomination Sociale</label>
                            <input type="text" id="company_name" name="name" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="Saisir la raison sociale">
                        </div>
                        <div>
                            <label id="label-domain" class="block text-xs font-bold text-gray-500 uppercase mb-1">Identifiant d'instance</label>
                            <input type="text" id="input-domain" name="subdomain" class="input-field bg-gray-50 cursor-not-allowed focus:ring-0" required readonly placeholder="Par dénomination">
                            <p id="hint-domain" class="text-[10px] text-gray-400 mt-1">Adresse : <span id="preview-url" class="font-bold text-cyan-600">...</span>.solutcloud.com</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Client</label>
                            <input type="email" name="email" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="client@email.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Téléphone Client</label>
                            <input type="tel" name="phone" class="input-field focus:border-cyan-600 focus:ring-0" placeholder="+225 07 00 00 00 00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mot de passe</label>
                            <input type="password" name="password" class="input-field focus:border-cyan-600 focus:ring-0" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Forfait</label>
                            <select name="package" class="input-field">
                                <option value="start">Forfait START</option>
                                <option value="business">Forfait BUSINESS</option>
                                <option value="premium">Forfait PREMIUM</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Engagement Initial</label>
                            <select name="duration" class="input-field bg-gray-100 cursor-not-allowed" readonly>
                                <option value="12" selected>12 Mois (1 an)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 text-right">
                        <button type="submit" class="bg-brand-teal hover:bg-cyan-800 text-white font-bold py-3 px-5 rounded-lg shadow-lg transition-all uppercase text-sm tracking-widest">
                            Créer instance
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLEAU DES CLIENTS --}}
            <div class="admin-card overflow-hidden bg-white/90 backdrop-blur-sm">
                <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-tighter">Instances Déployées</h3>
                    <span class="px-3 py-1 bg-brand-teal/10 text-brand-teal text-[10px] font-black rounded-full uppercase">
                        {{ $companies->count() }} Client(s)
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="px-8 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Entreprise & URL</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold uppercase tracking-widest">Forfait</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold uppercase tracking-widest">Statut</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Échéance</th>
                                <th class="px-8 py-4 text-right text-[11px] font-bold uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($companies as $company)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900">{{ $company->name }}</div>
                                    <div class="text-[11px] text-teal-600 font-medium">
                                    @if($company->package === 'premium')
                                    https://{{ $company->subdomain }}
                                    @else
                                    https://{{ $company->subdomain }}.solutcloud.com
                                    @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] text-gray-400 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <a href="mailto:{{ $company->email ?? $company->users->first()->email ?? '' }}" class="hover:text-teal-600 hover:underline">
                                            {{ $company->email ?? $company->users->first()->email ?? 'Email non défini' }}
                                        </a>
                                    </div>
                                    @if($company->phone)
                                    <div class="flex items-center gap-1 text-[10px] text-gray-400 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <a href="tel:{{ $company->phone }}" class="hover:text-teal-600 hover:underline">{{ $company->phone }}</a>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded text-[10px] font-black uppercase {{ $company->package == 'premium' ? 'bg-amber-100 text-amber-700 border border-amber-200' : ($company->package == 'business' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $company->package }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($company->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                            Attente Install
                                        </span>
                                    @elseif($company->status == 'active')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Suspendu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-700">
                                        {{ $company->expires_at ? $company->expires_at->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-gray-600 tracking-tighter">
                                        @if($company->expires_at)
                                        @if(now()->gt($company->expires_at))
                                        <span class="text-red-500 font-bold">Expiré</span>
                                        @else
                                        Expire dans {{ (int) now()->diffInDays($company->expires_at) }} jours
                                        @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        @if($company->status == 'pending')
                                            <button onclick="openFinalizeModal('{{ $company->id }}', '{{ $company->name }}')"
                                                    class="bg-blue-600 text-white px-3 py-1 rounded-md font-black text-[10px] hover:bg-blue-700 transition-all uppercase">
                                                Finaliser l'activation
                                            </button>
                                        @else
                                            @if($company->status == 'active')
                                                <form action="{{ route('admin.suspend', $company->id) }}" method="POST" onsubmit="return confirm('Suspendre cet accès ?')">
                                                    @csrf
                                                    <button class="text-red-600 font-black text-[11px] hover:underline tracking-tighter uppercase">Suspendre</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.activate', $company->id) }}" method="POST" class="inline-flex items-center gap-2">
                                                    @csrf
                                                    <select name="duration" class="text-[10px] py-0.5 px-1 border-gray-300 rounded bg-white">
                                                        <option value="1">1 mois</option>
                                                        <option value="12">1 an</option>
                                                    </select>
                                                    <button class="text-green-600 font-black text-[11px] hover:underline tracking-tighter uppercase">Réactiver</button>
                                                </form>
                                            @endif
                                        @endif
                                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Suppression définitive ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-semibold">
                                    Aucune instance déployée pour le moment.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL DE FINALISATION --}}
        <dialog id="modal-finalize" class="rounded-2xl p-0 border-none shadow-2xl backdrop:bg-black/50">
            <div class="p-8 w-[450px] bg-white">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 uppercase">Finaliser l'installation</h3>
                        <p id="finalize-client-name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"></p>
                    </div>
                </div>
                <form id="form-finalize" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Identifiant ERP créé</label>
                            <input type="text" name="erp_login" class="input-field focus:border-blue-600" required placeholder="ex: admin_solut">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Mot de passe ERP créé</label>
                            <input type="text" name="erp_password" class="input-field focus:border-blue-600" required placeholder="Saisir le mot de passe">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modal-finalize').close()" class="text-[10px] font-black text-gray-400 uppercase hover:text-gray-600">Annuler</button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-black text-[10px] uppercase shadow-lg shadow-blue-200">
                            Activer & Envoyer l'Email
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputName = document.getElementById('company_name');
        const inputDomain = document.getElementById('input-domain');
        const packageSelect = document.querySelector('select[name="package"]');
        const labelDomain = document.getElementById('label-domain');
        const hintDomain = document.getElementById('hint-domain');

        const slugify = (text) => {
            return text.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim()
                .replace(/\s+/g, '').replace(/[^\w-]+/g, '').replace(/--+/g, '-');
        };

        const toggleDomainMode = (mode) => {
            if (mode === 'premium') {
                inputDomain.readOnly = false;
                inputDomain.classList.remove('bg-gray-50', 'cursor-not-allowed');
                labelDomain.innerText = "Nom de Domaine Dédié";
                if(inputDomain.value === "" || inputDomain.value.indexOf('.') === -1) { inputDomain.value = "www."; }
                
                const span = document.createElement('span');
                span.className = 'text-orange-500 font-bold';
                span.textContent = "Format obligatoire : www.nomdomaine.com";
                hintDomain.replaceChildren(span);
            } else {
                inputDomain.readOnly = true;
                inputDomain.classList.add('bg-gray-50', 'cursor-not-allowed');
                labelDomain.innerText = "Identifiant d'instance";
                const formatted = slugify(inputName.value);
                inputDomain.value = formatted;
                
                const previewSpan = document.createElement('span');
                previewSpan.id = 'preview-url';
                previewSpan.className = 'font-bold brand-teal';
                previewSpan.textContent = formatted || '...';

                hintDomain.replaceChildren(
                    document.createTextNode('Adresse : '),
                    previewSpan,
                    document.createTextNode('.solutcloud.com')
                );
            }
        };
        inputDomain.addEventListener('blur', function() {
            if (packageSelect.value === 'premium') {
                let val = this.value.trim().toLowerCase();
                if (val !== "" && !val.startsWith('www.')) { this.value = 'www.' + val; }
            }
        });
        inputName.addEventListener('input', function() {
            if (packageSelect.value !== 'premium') {
                const formatted = slugify(this.value);
                inputDomain.value = formatted;
                const preview = document.getElementById('preview-url');
                if(preview) preview.innerText = formatted || '...';
            }
        });
        packageSelect.addEventListener('change', function () { toggleDomainMode(this.value); });
    });

    window.openFinalizeModal = function(id, name) {
        const modal = document.getElementById('modal-finalize');
        const form = document.getElementById('form-finalize');
        const nameDisplay = document.getElementById('finalize-client-name');
        if (modal && form && nameDisplay) {
            nameDisplay.innerText = "Client : " + name;
            form.action = "/companies/" + id + "/finalize";
            modal.showModal();
        }
    }
</script>
</x-app-layout>