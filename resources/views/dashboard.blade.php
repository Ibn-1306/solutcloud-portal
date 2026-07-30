<x-app-layout>
    <style>
    /* --- STYLES DE BASE (DESKTOP) --- */
    .brand-teal { color: #2B909A; }
    .bg-brand-teal { background-color: #2B909A; }
    .input-field { border: 1px solid #e2e8f0; border-radius: 0.375rem; width: 100%; padding: 0.6rem; font-size: 14px; }
    .admin-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; }
    svg.icon-fix { width: 28px !important; height: 28px !important; flex-shrink: 0; }

    /* FILIGRANE / WATERMARK */
    .admin-watermark {
        position: fixed;
        top: 52%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 1000px; /* Taille Desktop */
        height: 800px;
        background-image: url('{{ asset("img/favicon.png") }}');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.05; 
        pointer-events: none; 
        z-index: 0;
        transition: all 0.3s ease; 
    }

    /* --- RESPONSIVE : MOBILES --- */
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

        <!-- SECTION STATISTIQUES -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    
    <!-- Carte Revenus -->
    <div class="admin-card p-5 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Revenus Mensuels (Est.)</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">{{ number_format($monthlyRevenue, 0, ',', ' ') }} <span class="text-sm font-bold text-gray-600">FCFA</span></h3>
            </div>
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Carte Instances Actives -->
    <div class="admin-card p-5 border-l-4 border-cyan-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Instances Actives</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $activeCount }} <span class="text-sm font-bold text-gray-600">CLIENT(S)</span></h3>
            </div>
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
        </div>
    </div>

    <!-- Carte Alertes Échéances -->
    <div class="admin-card p-5 border-l-4 {{ $alerts > 0 ? 'border-orange-500' : 'border-gray-200' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Alertes Échéances (7j)</p>
                <h3 class="text-2xl font-black {{ $alerts > 0 ? 'text-orange-600' : 'text-gray-800' }} mt-1">{{ $alerts }} <span class="text-sm font-bold text-gray-600">Critique(s)</span></h3>
            </div>
            <div class="w-12 h-12 {{ $alerts > 0 ? 'bg-orange-50' : 'bg-white' }} rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 {{ $alerts > 0 ? 'text-orange-600 animate-pulse' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
        </div>
    </div>

    </div>
            
            <!-- ALERTES -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-brand-teal text-white font-bold rounded-xl shadow-lg flex items-center animate-bounce">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-600 text-white rounded-xl shadow-lg">
                    <ul class="list-disc list-inside font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORMULAIRE D'AJOUT -->
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
                            <input type="email" name="email" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="contact@client.com">
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
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Abonnement</label>
                            <select name="duration" class="input-field font-bold brand-teal">
                                <option value="1">1 Mois</option>
                                <option value="2">2 Mois</option>
                                <option value="3">3 Mois</option>
                                <option value="12">1 An (12 mois)</option>
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

            <!-- TABLEAU DES CLIENTS VERSION PREMIUM -->
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
                            @foreach($companies as $company)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900">{{ $company->name }}</div>
                                    <div class="text-[11px] text-gray-400">
                                        @if($company->package === 'premium')
                                            {{ $company->subdomain }} 
                                        @else
                                            {{ $company->subdomain }}.solutcloud.com
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded text-[10px] font-black uppercase {{ $company->package == 'premium' ? 'bg-amber-100 text-amber-700 border border-amber-200' : ($company->package == 'business' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $company->package }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($company->status == 'active')
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
                                                    <option value="2">2 mois</option>
                                                    <option value="3">3 mois</option>
                                                    <option value="12">1 an</option>
                                                </select>
                                                <button class="text-green-600 font-black text-[11px] hover:underline tracking-tighter uppercase">Activer</button>
                                            </form>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                if(inputDomain.value === "") inputDomain.value = "www.";
                hintDomain.innerHTML = "<span class='text-orange-500 font-bold'>⚠️ Format obligatoire : www.nomdomaine.com</span>";
            } else {
                inputDomain.readOnly = true;
                inputDomain.classList.add('bg-gray-50', 'cursor-not-allowed');
                labelDomain.innerText = "Identifiant d'instance";
                const formatted = slugify(inputName.value);
                inputDomain.value = formatted;
                hintDomain.innerHTML = `Adresse : <span id="preview-url" class="font-bold brand-teal">${formatted || '...'}</span>.solutcloud.com`;
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

        packageSelect.addEventListener('change', function () {
            toggleDomainMode(this.value);
        });
    });
    </script>
</x-app-layout>