<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Statut du paiement | SOLUTCLOUD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f8f9] text-gray-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl shadow-gray-200/50">
            <div class="border-b border-gray-100 px-7 py-6 sm:px-9">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/favicon.png') }}" alt="SOLUTCLOUD" class="h-10 w-10 object-contain">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.18em] text-[#2b909a]">SOLUTCLOUD</p>
                        <p class="mt-1 text-xs text-gray-500">Paiement sécurisé</p>
                    </div>
                </div>
            </div>

            <div class="px-7 py-9 text-center sm:px-10">
                @if($payment?->isPaid())
                    <div class="mx-auto flex items-center justify-center text-emerald-600">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                    </div>
                    <h1 class="mt-5 text-2xl font-black">Paiement confirmé</h1>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Votre règlement <strong>{{ $payment->reference }}</strong> a été confirmé. Notre équipe peut maintenant préparer votre instance.</p>
                @else
                    <div class="mx-auto flex items-center justify-center text-amber-600">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <h1 class="mt-5 text-2xl font-black">Vérification en cours</h1>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Nous vérifions directement le statut auprès de Moneroo. Vous recevrez la suite du processus après confirmation sécurisée.</p>
                @endif

                <a href="{{ route('login') }}" class="mt-7 inline-flex rounded-lg bg-[#2b909a] px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-[#237781]">Accéder à SOLUTCLOUD</a>
            </div>
        </section>
    </main>
</body>
</html>
