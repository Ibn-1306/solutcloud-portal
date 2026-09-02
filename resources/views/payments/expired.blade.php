<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <title>Erreur 404 — Lien de paiement expiré</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-12">
        <div class="pointer-events-none absolute -left-32 top-[-8rem] h-96 w-96 rounded-full bg-[#2b909a]/10 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-24 h-[30rem] w-[30rem] rounded-full bg-rose-100/60 blur-3xl" aria-hidden="true"></div>

        <section class="relative w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white px-6 py-10 text-center sm:px-12 sm:py-14" aria-labelledby="expired-payment-title">
            <header class="flex items-center justify-center border-b border-slate-100 pb-8">
                <img src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" alt="SOLUTCLOUD" class="h-12 w-auto sm:h-14">
            </header>

            <div class="mx-auto mt-9 flex h-24 w-24 items-center justify-center rounded-full bg-rose-50 text-rose-700 ring-8 ring-rose-50/60">
                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6m0 4h.01" stroke-linecap="round"/></svg>
            </div>

            <p class="mt-8 text-xs font-black uppercase tracking-[.24em] text-rose-700">Erreur 404</p>
            <h1 id="expired-payment-title" class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Lien de paiement expiré</h1>
            <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-slate-600 sm:text-base">
                Ce lien de paiement n’est plus valide. Demandez à l’équipe SOLUTCLOUD de générer un nouveau lien sécurisé.
            </p>
            @if($payment)
                <p class="mt-6 text-xs font-bold uppercase tracking-[.12em] text-slate-400">Référence {{ $payment->reference }}</p>
            @endif
        </section>
    </main>
</body>
</html>
