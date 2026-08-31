<x-admin-layout
    title="SOLUTCLOUD — Commandes"
    page-title="Commandes"
    description="Commandes START/BUSINESS et demandes PREMIUM."
>
    <style>
        .admin-card {
            background: #fff;
            border: 1px solid #edf2f4;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .06);
        }

        .admin-watermark {
            position: fixed;
            top: 52%;
            left: 50%;
            z-index: 0;
            width: 900px;
            height: 700px;
            pointer-events: none;
            opacity: .035;
            transform: translate(-50%, -50%);
            background: url('{{ asset('img/favicon.png') }}') center / contain no-repeat;
        }

        .orders-table th {
            white-space: nowrap;
        }

        .order-message {
            max-width: 320px;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        @media (max-width: 768px) {
            .admin-watermark {
                width: 280px;
                height: 280px;
            }
        }
    </style>

    <div class="relative min-h-screen py-8">
        <div class="admin-watermark" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="admin-card border-l-4 border-l-[#2b909a] p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Total reçu</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $orders->total() }}</p>
                </div>

                <div class="admin-card border-l-4 border-l-cyan-500 bg-cyan-50/20 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Commandes START</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $startOrderCount }}</p>
                </div>

                <div class="admin-card border-l-4 border-l-indigo-500 bg-indigo-50/20 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Commandes BUSINESS</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $businessOrderCount }}</p>
                </div>

                <div class="admin-card border-l-4 border-l-amber-500 bg-amber-50/20 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-gray-400">Demandes de devis</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $quoteRequestCount }}</p>
                </div>
            </div>

            <div class="admin-card overflow-hidden">
                <div class="flex flex-col gap-2 border-b border-gray-100 bg-gray-50/70 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800">Demandes commerciales</h3>
                        <p class="mt-1 text-xs text-gray-500">Les plus récentes apparaissent en premier.</p>
                    </div>
                    <span class="w-fit rounded-full bg-[#2b909a]/10 px-3 py-1 text-[10px] font-black uppercase text-[#237781]">
                        {{ $orders->total() }} demande(s)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="admin-data-table orders-table min-w-full text-sm">
                        <thead class="bg-[#2b909a] text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Référence</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Type / Offre</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Client</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Entreprise</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Besoin</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">E-mails</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Reçue le</th>
                                <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($orders as $order)
                                <tr class="align-top transition-colors hover:bg-[#2b909a]/[.035]">
                                    <td class="px-6 py-5 font-black text-gray-900">
                                        {{ $order->commercialReference() }}
                                    </td>

                                    <td class="px-6 py-5">
                                        @if($order->type === 'quote')
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-black uppercase text-amber-800">
                                                Demande de devis
                                            </span>
                                        @elseif($order->offer === 'BUSINESS')
                                            <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-[10px] font-black uppercase text-indigo-800">
                                                Commande BUSINESS
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-[10px] font-black uppercase text-cyan-800">
                                                Commande START
                                            </span>
                                        @endif

                                        <div class="mt-2 font-black text-gray-800">
                                            SOLUTCLOUD {{ $order->offer ?: '—' }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $order->profile ?: 'Profil non renseigné' }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-800">{{ $order->fullname }}</div>
                                        <a href="mailto:{{ $order->email }}" class="mt-1 block text-xs text-[#237781] hover:underline">
                                            {{ $order->email }}
                                        </a>
                                        @if($order->phone)
                                            <a href="tel:{{ $order->phone }}" class="mt-1 block text-xs text-gray-500 hover:text-[#237781] hover:underline">
                                                {{ $order->phone }}
                                            </a>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5 font-semibold text-gray-700">
                                        {{ $order->company_name ?: 'Non renseignée' }}
                                    </td>

                                    <td class="px-6 py-5 text-xs leading-5 text-gray-600">
                                        <div class="order-message">{{ $order->clientNotes() ?: 'Aucune précision.' }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="space-y-2">
                                            <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ $order->acknowledged_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                                                Client {{ $order->acknowledged_at ? 'confirmé' : 'non confirmé' }}
                                            </span>
                                            <span class="block w-fit rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ $order->notified_at ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                Sales {{ $order->notified_at ? 'notifié' : 'à vérifier' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold text-gray-600">
                                        {{ optional($order->created_at)->format('d/m/Y') }}
                                        <span class="mt-1 block font-normal text-gray-400">{{ optional($order->created_at)->format('H:i') }}</span>
                                    </td>

                                    <td class="px-6 py-5 text-right">
                                        @if($order->payments->isNotEmpty())
                                            <a href="{{ route('admin.payments.index') }}" class="inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-[10px] font-black uppercase text-emerald-700">
                                                Paiement créé
                                            </a>
                                        @else
                                            <a href="{{ route('admin.payments.index', ['lead' => $order->id]) }}#payment-form" class="inline-flex rounded-md bg-[#2b909a] px-3 py-2 text-[10px] font-black uppercase text-white shadow-sm">
                                                Créer paiement
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex max-w-sm flex-col items-center text-gray-400">
                                            <svg class="mb-3 h-11 w-11 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 6h18M5 6v13h14V6M9 10h6M9 14h4M8 3h8l1 3H7l1-3z" />
                                            </svg>
                                            <p class="font-bold text-gray-500">Aucune commande reçue pour le moment.</p>
                                            <p class="mt-1 text-xs">Les commandes et demandes PREMIUM du site apparaîtront ici.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
