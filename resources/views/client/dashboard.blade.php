<x-app-layout>
    <style>
        .brand-teal { color: #2B909A; }
        .bg-brand-teal { background-color: #2B909A; }
        .client-card { background: white; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #f1f5f9; position: relative; overflow: hidden; }
        
        /* Bouton Action Principal */
        .btn-access { 
            background: linear-gradient(135deg, #2B909A 0%, #1e6b73 100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-access:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 12px 24px rgba(43, 144, 154, 0.3);
            filter: brightness(1.1);
        }
        
        /* Filigrane d'Arrière-plan */
        .client-watermark {
            position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 600px; height: 600px; 
            background-image: url('{{ asset("img/favicon.png") }}');
            background-size: contain; background-repeat: no-repeat; 
            opacity: 0.02; pointer-events: none; z-index: 0;
        }

        .status-dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 uppercase tracking-tight">
                TABLEAU DE BORD <span class="brand-teal">SOLUTCLOUD</span>
            </h2>
            <div class="hidden sm:flex items-center gap-2 text-[10px] font-bold text-gray-400 bg-white border border-gray-100 px-4 py-1.5 rounded-full uppercase tracking-widest shadow-sm">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Session Sécurisée
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative min-h-screen bg-[#fcfdfe]">
        <div class="client-watermark"></div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 relative z-10">
            
            <div class="client-card">
                <!-- BANNIÈRE DE STATUT SI SUSPENDU -->
                @if($company->status != 'active')
                    <div class="bg-red-600 py-2 px-8 text-center">
                        <p class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Accès Restreint - Action requise</p>
                    </div>
                @endif

                <!-- EN-TÊTE DE L'ENTREPRISE -->
                <div class="px-8 py-10 bg-white border-b border-gray-50 sm:flex justify-between items-end">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Raison Sociale</p>
                        <h3 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $company->name }}</h3>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-xs font-bold text-brand-teal bg-teal-50 px-2 py-0.5 rounded italic">
                                {{ $company->package === 'premium' ? 'https://'.$company->subdomain : 'https://'.$company->subdomain.'.solutcloud.com' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-6 sm:mt-0">
                        @if($company->status == 'active')
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center px-4 py-2 rounded-xl bg-green-50 text-green-700 text-[10px] font-black uppercase border border-green-100 shadow-sm">
                                    <span class="status-dot bg-green-500 animate-pulse"></span> Service Opérationnel
                                </span>
                            </div>
                        @else
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center px-4 py-2 rounded-xl bg-red-50 text-red-700 text-[10px] font-black uppercase border border-red-100">
                                    <span class="status-dot bg-red-500"></span> Instance Coupée
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    <!-- SECTION INFOS CLÉS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        <!-- CARTE OFFRE -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-gradient-to-br from-white to-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Offre</p>
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <h4 class="text-2xl font-black text-gray-800 tracking-tight">SOLUTCLOUD <span class="brand-teal">{{ strtoupper($company->package) }}</span></h4>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Maintenance & Support Inclus</p>
                        </div>

                        <!-- CARTE ÉCHÉANCE -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-gradient-to-br from-white to-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Expiration</p>
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="text-2xl font-black text-gray-800 tracking-tight">
                                {{ $company->expires_at ? $company->expires_at->format('d/m/Y') : 'En attente' }}
                            </h4>
                            @if($company->expires_at)
                                <p class="text-[10px] mt-1 font-black uppercase">
                                    @if(now()->gt($company->expires_at))
                                        <span class="text-red-600 tracking-widest animate-pulse">Action requise : Contrat expiré</span>
                                    @else
                                        <span class="text-gray-400">Jours restants :</span> 
                                        <span class="{{ now()->diffInDays($company->expires_at) < 15 ? 'text-orange-500' : 'text-brand-teal' }}">
                                            {{ (int) now()->diffInDays($company->expires_at) }} jours
                                        </span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- BLOC ACTIONS -->
                    <div class="space-y-4">
                        @if($company->status == 'active')
                            <!-- ACCÈS ERP -->
                            <a href="{{ $company->package === 'premium' ? 'https://'.$company->subdomain : 'https://'.$company->subdomain.'.solutcloud.com' }}" 
                               target="_blank" 
                               class="btn-access flex items-center justify-center w-full py-5 text-white font-black rounded-2xl uppercase tracking-[0.15em] text-sm shadow-xl shadow-teal-100">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Lancer mon instance de gestion
                            </a>
                        @else
                            <!-- MESSAGE ALERTE SI SUSPENDU -->
                            <div class="bg-red-50 border-2 border-red-100 p-6 rounded-2xl mb-6 flex items-start gap-4">
                                <div class="bg-red-100 p-2 rounded-lg">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black text-red-800 uppercase tracking-widest">Accès ERP Verrouillé</h3>
                                    <p class="text-[11px] text-red-600/80 mt-1 leading-relaxed">Votre période d'abonnement est arrivée à son terme. Vos données sont conservées en sécurité, mais l'accès au logiciel nécessite un réabonnement immédiat.</p>
                                </div>
                            </div>
                        @endif

                        <!-- FORMULAIRE RENOUVELLEMENT -->
                        <form action="{{ route('client.renew') }}" method="POST">
                            @csrf
                            <button type="submit" class="group w-full py-5 border-2 border-gray-100 hover:border-brand-teal text-gray-400 hover:text-brand-teal font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Procéder au renouvellement
                            </button>
                        </form>
                    </div>
                </div>

                <!-- FOOTER DASHBOARD -->
                <div class="px-8 py-6 bg-[#fcfdfe] border-t border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" class="h-4 opacity-20">
                        <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Master Portal v2.1</span>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Assistance : <a href="mailto:sales@i-solutions.ci" class="text-brand-teal hover:underline">sales@i-solutions.ci</a></p>
                </div>
            </div>

            <!-- LIENS SECONDAIRES -->
            <div class="mt-10 flex justify-center items-center gap-8">
                <a href="https://www.solutcloud.com/contact.html" target="_blank" class="text-[9px] font-black text-gray-400 hover:text-brand-teal transition-colors uppercase tracking-widest">Support Client</a>
                <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                <a href="#" class="text-[9px] font-black text-gray-400 hover:text-brand-teal transition-colors uppercase tracking-widest">Documentation</a>
                <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[9px] font-black text-red-400 hover:text-red-600 transition-colors uppercase tracking-widest">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>