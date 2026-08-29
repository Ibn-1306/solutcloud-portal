@props([
    'title' => 'SOLUTCLOUD — Espace client',
    'pageTitle' => 'Tableau de bord',
])

@php
    $client = auth()->user();
    $company = $client?->company;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body
    class="min-h-screen overflow-x-hidden bg-[#f4f8f9] font-sans text-slate-900 antialiased"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        init() {
            try {
                this.sidebarCollapsed = localStorage.getItem('solutcloud-sidebar-collapsed') === 'true';
            } catch (error) {}
        },
        setSidebarCollapsed(value) {
            this.sidebarCollapsed = value;
            try {
                localStorage.setItem('solutcloud-sidebar-collapsed', value ? 'true' : 'false');
            } catch (error) {}
        },
    }"
    @keydown.escape.window="sidebarOpen = false"
>
    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity duration-200 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-[2px] lg:hidden"
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <aside
        id="client-sidebar"
        class="fixed inset-y-0 left-0 z-50 flex w-[286px] max-w-[86vw] -translate-x-full flex-col overflow-y-auto bg-black px-5 py-5 text-white shadow-2xl transition-all duration-300 ease-out lg:translate-x-0 lg:shadow-none"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarCollapsed ? 'lg:w-[88px] lg:px-4' : 'lg:w-[286px] lg:px-5',
        ]"
        aria-label="Navigation du compte client"
    >
        <div class="flex min-h-12 items-center justify-between border-b border-white/10 pb-5 lg:border-0 lg:pb-3" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
            <a
                href="{{ route('client.dashboard') }}"
                class="flex min-w-0 items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#51c6d0]"
                :class="sidebarCollapsed ? 'lg:w-full lg:justify-center' : ''"
                @click="if (sidebarCollapsed && window.innerWidth >= 1024) { $event.preventDefault(); setSidebarCollapsed(false); }"
                :aria-label="sidebarCollapsed ? 'Ouvrir le menu SOLUTCLOUD' : 'Accueil SOLUTCLOUD'"
                :title="sidebarCollapsed ? 'Ouvrir le menu' : null"
            >
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                    <img src="{{ asset('img/favicon.png') }}" alt="" class="h-7 w-7 object-contain">
                </span>
                <span class="min-w-0" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <span class="block text-base font-extrabold tracking-[.08em]">SOLUTCLOUD</span>
                    <span class="block truncate text-xs text-white/70">Espace client sécurisé</span>
                </span>
            </a>

            <button
                x-cloak
                x-show="! sidebarCollapsed"
                type="button"
                @click="setSidebarCollapsed(true)"
                class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-md text-white/70 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#51c6d0] lg:flex"
                aria-label="Réduire le menu"
                title="Réduire le menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/><path d="M14 9l-3 3 3 3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <button
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-md text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#51c6d0] lg:hidden"
                @click="sidebarOpen = false"
                aria-label="Fermer le menu"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <nav class="mt-8 space-y-1.5">
            <a
                href="{{ route('client.dashboard') }}"
                @class([
                    'group flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                    'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('client.dashboard'),
                    'hover:bg-white/[.1]' => ! request()->routeIs('client.dashboard'),
                ])
                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                :title="sidebarCollapsed ? 'Tableau de bord' : null"
                @click="sidebarOpen = false"
            >
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Tableau de bord</span>
            </a>

            <a
                href="{{ route('client.renew') }}"
                @class([
                    'group flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                    'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('client.renew') || request()->routeIs('client.subscription.*'),
                    'hover:bg-white/[.1]' => ! request()->routeIs('client.renew') && ! request()->routeIs('client.subscription.*'),
                ])
                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                :title="sidebarCollapsed ? 'Abonnement' : null"
                @click="sidebarOpen = false"
            >
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/>
                </svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Abonnement</span>
            </a>
        </nav>

        <div class="mt-auto border-t border-white/10 pt-5">
            <a href="{{ route('client.profile') }}" @class([
                'mb-2 flex min-h-11 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('client.profile') || request()->routeIs('client.password.update'),
                'hover:bg-white/[.1]' => ! request()->routeIs('client.profile') && ! request()->routeIs('client.password.update'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Compte' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0115 0" stroke-linecap="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Compte</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-md px-4 text-sm font-semibold text-white transition hover:bg-white/[.1] focus:outline-none focus:ring-2 focus:ring-[#51c6d0]" :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Se déconnecter' : null">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 012 2v12a2 2 0 01-2 2h-4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Se déconnecter</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen min-w-0 transition-[padding] duration-300 ease-out" :class="sidebarCollapsed ? 'lg:pl-22' : 'lg:pl-[286px]'">
        <header class="sticky top-0 z-30 border-b border-slate-200/90 bg-white/95 backdrop-blur">
            <div class="flex min-h-[72px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        class="group relative flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#2b909a]/40 hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 lg:hidden"
                        @click="sidebarOpen = ! sidebarOpen"
                        :aria-expanded="sidebarOpen.toString()"
                        aria-controls="client-sidebar"
                        aria-label="Ouvrir le menu"
                    >
                        <span class="absolute h-0.5 w-5 bg-current transition duration-300" :class="sidebarOpen ? 'rotate-45' : '-translate-y-1.5'"></span>
                        <span class="absolute h-0.5 w-5 bg-current transition duration-200" :class="sidebarOpen ? 'opacity-0 scale-x-0' : 'opacity-100'"></span>
                        <span class="absolute h-0.5 w-5 bg-current transition duration-300" :class="sidebarOpen ? '-rotate-45' : 'translate-y-1.5'"></span>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-extrabold text-slate-950 sm:text-xl">{{ $pageTitle }}</p>
                        <p class="hidden truncate text-xs text-slate-500 sm:block">{{ $company?->name }}</p>
                    </div>
                </div>

                <div class="flex min-w-0 items-center gap-2 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-1.5 pr-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#dff3f5] text-[#176f78]">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0113 0" stroke-linecap="round"/></svg>
                    </span>
                    <span class="hidden max-w-44 truncate text-sm font-bold text-slate-700 sm:block">{{ $client?->name }}</span>
                </div>
            </div>
        </header>

        <main class="min-w-0 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 xl:px-10">
            <div class="mx-auto w-full max-w-[1500px]">
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" role="status">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                        <p class="font-extrabold">Une vérification est nécessaire.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
    <script>
        (() => {
            const statusUrl = @json(route('account.suspended.status'));
            const suspendedUrl = @json(route('account.suspended'));

            const checkAccountAccess = async () => {
                try {
                    const separator = statusUrl.includes('?') ? '&' : '?';
                    const response = await fetch(statusUrl + separator + '_=' + Date.now(), {
                        cache: 'no-store',
                        credentials: 'same-origin',
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok) return;
                    const payload = await response.json();

                    if (payload.status === 'suspended' && payload.suspension_reason === 'administrative') {
                        window.location.replace(suspendedUrl);
                    }
                } catch (_) {
                    // La vérification suivante reprendra automatiquement.
                }
            };

            checkAccountAccess();
            window.setInterval(checkAccountAccess, 4000);
        })();
    </script></body>
</html>
