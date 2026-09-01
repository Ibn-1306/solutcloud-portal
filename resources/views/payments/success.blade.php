<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Paiement confirmé — SOLUTCLOUD</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-12">
        <div class="pointer-events-none absolute -left-32 top-[-8rem] h-96 w-96 rounded-full bg-[#2b909a]/10 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-24 h-[30rem] w-[30rem] rounded-full bg-cyan-200/30 blur-3xl" aria-hidden="true"></div>

        <section class="relative w-full max-w-2xl overflow-hidden rounded-[2rem] border border-[#2b909a]/15 bg-white px-6 py-10 text-center shadow-[0_28px_90px_rgba(15,23,42,.10)] sm:px-12 sm:py-14" aria-labelledby="payment-success-title">
            <header class="flex items-center justify-center border-b border-slate-100 pb-8">
                <img src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" alt="SOLUTCLOUD" class="h-12 w-auto sm:h-14">
            </header>

            <div class="mx-auto mt-9 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-50 ring-8 ring-emerald-50/60 sm:h-28 sm:w-28">
                <svg class="h-14 w-14 text-emerald-600 sm:h-16 sm:w-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <p class="mt-8 text-xs font-black uppercase tracking-[.24em] text-[#237781]">Transaction sécurisée</p>
            <h1 id="payment-success-title" class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Paiement confirmé</h1>
            <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-slate-600 sm:text-base">
                Votre règlement <strong class="font-extrabold text-slate-800">{{ $payment->reference }}</strong> a bien été enregistré.
                @if($upgradePending)
                    Votre passage à <strong class="font-extrabold text-slate-800">SOLUTCLOUD BUSINESS</strong> est maintenant en cours de traitement. Merci de patienter pendant sa finalisation par notre équipe.
                @elseif($upgradeFinalized)
                    Votre passage à <strong class="font-extrabold text-slate-800">SOLUTCLOUD BUSINESS</strong> a été finalisé.
                @elseif($subscriptionUpdated)
                    Votre abonnement SOLUTCLOUD a été mis à jour.
                @else
                    Notre équipe poursuit maintenant la préparation de votre espace client et vous transmettra les instructions d’activation par e-mail.
                @endif
            </p>

            <div class="mx-auto mt-8 max-w-md rounded-2xl border border-slate-100 bg-slate-50 px-5 py-4 text-sm leading-6 text-slate-500">
                Vous pouvez fermer cet onglet en toute sécurité. Aucun autre clic n’est nécessaire.
            </div>
        </section>
    </main>
</body>
</html>