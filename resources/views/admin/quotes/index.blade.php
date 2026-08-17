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
            Devis PREMIUM
        </h2>
    </x-slot>

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

            {{-- FORMULAIRE DE CRÉATION DE DEVIS --}}
            <div class="admin-card p-10 mb-12 bg-white/90 backdrop-blur-sm">
                <div class="flex items-center mb-8 border-b border-gray-100 pb-4">
                    <svg class="icon-fix brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-extrabold text-gray-800 uppercase">Générer et envoyer un devis</h3>
                </div>

                <form action="{{ route('admin.quotes.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom du client (Responsable)</label>
                            <input type="text" name="customer_name" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="ex : M. Kouassi Jean">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Client</label>
                            <input type="email" name="customer_email" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="client@email.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Téléphone Client</label>
                            <input type="tel" name="customer_phone" class="input-field focus:border-cyan-600 focus:ring-0" placeholder="+225 07 00 00 00 00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dénomination Sociale (Optionnel)</label>
                            <input type="text" name="company_name" class="input-field focus:border-cyan-600 focus:ring-0" placeholder="Saisir la raison sociale">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Montant du devis (FCFA)</label>
                            <input type="number" name="amount" min="0" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="ex : 150000">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durée (Mois)</label>
                            <input type="number" name="duration" min="1" class="input-field focus:border-cyan-600 focus:ring-0" required placeholder="ex : 12">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description / Notes additionnelles (Optionnel)</label>
                        <textarea name="description" rows="3" class="input-field focus:border-cyan-600 focus:ring-0" placeholder="Détails des modules inclus ou notes spécifiques..."></textarea>
                    </div>

                    <div class="mt-8 text-right">
                        <button type="submit" class="bg-brand-teal hover:bg-cyan-800 text-white font-bold py-3 px-5 rounded-lg shadow-lg transition-all uppercase text-sm tracking-widest">
                            Envoyer le Devis
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLEAU DES DEVIS GÉNÉRÉS --}}
            <div class="admin-card overflow-hidden bg-white/90 backdrop-blur-sm">
                <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-tighter">Historique des devis</h3>
                    <span class="px-3 py-1 bg-brand-teal/10 text-brand-teal text-[10px] font-black rounded-full uppercase">
                        {{ $quotes->count() }} Devis
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="px-8 py-4 text-left text-[11px] font-bold uppercase tracking-widest">N° Devis</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Client</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Téléphone</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Entreprise</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Montant (FCFA)</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Durée</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Statut</th>
                                <th class="px-8 py-4 text-left text-[11px] font-bold uppercase tracking-widest">Date d'envoi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($quotes as $quote)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                <td class="px-8 py-5 font-black text-gray-900">{{ $quote->quote_number }}</td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-800">{{ $quote->customer_name }}</div>
                                    <div class="text-[11px] text-gray-500">
                                        <a href="mailto:{{ $quote->customer_email }}" class="hover:text-teal-600 hover:underline">{{ $quote->customer_email }}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($quote->customer_phone)
                                        <a href="tel:{{ $quote->customer_phone }}" class="text-gray-600 hover:text-teal-600 hover:underline">{{ $quote->customer_phone }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-gray-600">{{ $quote->company_name ?? '—' }}</td>
                                <td class="px-6 py-5 font-bold text-gray-900">{{ number_format($quote->amount, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 text-gray-600">{{ $quote->duration }} mois</td>
                                <td class="px-6 py-5">
                                    @if($quote->status === 'sent')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full uppercase">Envoyé</span>
                                    @elseif($quote->status === 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold rounded-full uppercase">Payé</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-full uppercase">{{ $quote->status }}</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-gray-600">{{ $quote->sent_at ? $quote->sent_at->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-8 py-10 text-center text-gray-400 font-semibold">
                                    Aucun devis généré pour le moment.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
