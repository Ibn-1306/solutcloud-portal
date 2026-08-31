@props([
    'title' => 'SOLUTCLOUD — Administration',
    'pageTitle' => 'Tableau de bord',
    'description' => 'Pilotez les opérations SOLUTCLOUD depuis un espace centralisé.',
])

@php
    $admin = auth()->user();
    $initials = collect(preg_split('/\s+/', trim((string) $admin?->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
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
    <style>
        [x-cloak]{display:none!important}

        .admin-data-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .admin-data-table > thead {
            background: #2b909a;
            color: #fff;
        }

        .admin-data-table > tbody > tr:nth-child(odd) {
            background: #fff;
        }

        .admin-data-table > tbody > tr:nth-child(even) {
            background: #f2f6f7;
        }

        .admin-data-table > tbody > tr > td {
            border-bottom: 1px solid #dbe5e7;
        }

        .admin-data-table > tbody > tr:last-child > td {
            border-bottom: 0;
        }

        .admin-data-table > tbody > tr:hover {
            background: #e7f3f4;
        }
    </style>
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
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-[2px] lg:hidden"
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <aside
        id="admin-sidebar"
        class="fixed inset-y-0 left-0 z-50 flex w-[286px] max-w-[86vw] -translate-x-full flex-col overflow-y-auto bg-black px-5 py-5 text-white shadow-2xl transition-all duration-300 ease-out lg:translate-x-0 lg:shadow-none"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarCollapsed ? 'lg:w-[88px] lg:px-4' : 'lg:w-[286px] lg:px-5',
        ]"
        aria-label="Navigation administrateur"
    >
        <div class="flex min-h-12 items-center justify-between border-b border-white/10 pb-5 lg:border-0 lg:pb-3" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
            <a
                href="{{ route('admin.dashboard') }}"
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
                    <span class="block truncate text-xs text-white/70">Administration</span>
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
            <button type="button" @click="sidebarOpen = false" class="flex h-11 w-11 items-center justify-center rounded-xl text-slate-300 transition hover:bg-white/[.08] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#51c6d0] lg:hidden" aria-label="Fermer le menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
        </div>

        <nav class="mt-8 space-y-1.5">
            <a href="{{ route('admin.dashboard') }}" @class([
                'flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.dashboard') || request()->routeIs('admin.companies.*') || request()->routeIs('admin.suspend') || request()->routeIs('admin.activate'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.dashboard') && ! request()->routeIs('admin.companies.*') && ! request()->routeIs('admin.suspend') && ! request()->routeIs('admin.activate'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Tableau de bord' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Tableau de bord</span>
            </a>

            <a href="{{ route('admin.demos.index') }}" @class([
                'flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.demos.*'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.demos.*'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Démonstrations' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M9 9l6 3-6 3V9z" stroke-linejoin="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Démonstrations</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" @class([
                'flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.orders.*'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.orders.*'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Commandes' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3h10a2 2 0 012 2v16l-7-4-7 4V5a2 2 0 012-2z"/><path d="M9 8h6M9 12h6" stroke-linecap="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Commandes</span>
            </a>

            <a href="{{ route('admin.payments.index') }}" @class([
                'flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.payments.*'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.payments.*'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Paiement' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3" stroke-linecap="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Paiement</span>
            </a>
            <a href="{{ route('admin.client-security.index') }}" @class([
                'flex min-h-12 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.client-security.*'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.client-security.*'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Sécurité clients' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3l7 3v5c0 4.8-2.9 8.2-7 10-4.1-1.8-7-5.2-7-10V6l7-3z"/><path d="M9 12l2 2 4-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Sécurité clients</span>
            </a>

        </nav>

        <div class="mt-8 rounded-lg border border-white/10 bg-white/[.05] p-4" :class="sidebarCollapsed ? 'lg:hidden' : ''">
            <p class="text-[10px] font-extrabold uppercase tracking-[.16em] text-[#8bd4da]">Accès sécurisé</p>
            <p class="mt-2 text-xs leading-5 text-white/70">Actions réservées aux administrateurs autorisés SOLUTCLOUD.</p>
        </div>

        <div class="mt-auto border-t border-white/10 pt-5">
            <a href="{{ route('admin.profile.edit') }}" @class([
                'mb-2 flex min-h-11 items-center gap-3 rounded-md px-4 text-sm font-bold text-white transition focus:outline-none focus:ring-2 focus:ring-[#51c6d0]',
                'bg-[#2b909a] shadow-lg shadow-[#2b909a]/20' => request()->routeIs('admin.profile.edit'),
                'hover:bg-white/[.1]' => ! request()->routeIs('admin.profile.edit'),
            ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Compte' : null" @click="sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0115 0" stroke-linecap="round"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Compte</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-md px-4 text-sm font-semibold text-white transition hover:bg-white/[.1] focus:outline-none focus:ring-2 focus:ring-[#51c6d0]" :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" :title="sidebarCollapsed ? 'Se déconnecter' : null">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 012 2v12a2 2 0 01-2 2h-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Se déconnecter</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen min-w-0 transition-[padding] duration-300 ease-out" :class="sidebarCollapsed ? 'lg:pl-22' : 'lg:pl-[286px]'">
        <header class="sticky top-0 z-30 border-b border-slate-200/90 bg-white/95 backdrop-blur">
            <div class="flex min-h-[76px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="sidebarOpen = ! sidebarOpen" :aria-expanded="sidebarOpen.toString()" aria-controls="admin-sidebar" aria-label="Ouvrir le menu" class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#2b909a]/40 hover:text-[#207b84] focus:outline-none focus:ring-2 focus:ring-[#2b909a]/30 lg:hidden">
                        <span class="absolute h-0.5 w-5 bg-current transition duration-300" :class="sidebarOpen ? 'rotate-45' : '-translate-y-1.5'"></span>
                        <span class="absolute h-0.5 w-5 bg-current transition duration-200" :class="sidebarOpen ? 'scale-x-0 opacity-0' : 'opacity-100'"></span>
                        <span class="absolute h-0.5 w-5 bg-current transition duration-300" :class="sidebarOpen ? '-rotate-45' : 'translate-y-1.5'"></span>
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-extrabold text-slate-950 sm:text-xl">{{ $pageTitle }}</h1>
                        <p class="hidden truncate text-xs text-slate-500 sm:block">{{ $description }}</p>
                    </div>
                </div>
                <div class="flex min-w-0 items-center gap-2 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-1.5 pr-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#dff3f5] text-[#176f78]">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="8" r="3.5"/>
                            <path d="M5.5 20a6.5 6.5 0 0113 0" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="hidden max-w-44 truncate text-sm font-bold text-slate-700 sm:block">Administrateur</span>
                </div>
            </div>
        </header>

        <main class="min-w-0 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 xl:px-10">
            <div class="mx-auto w-full max-w-[1500px]">{{ $slot }}</div>
        </main>
    </div>
</body>
</html>
