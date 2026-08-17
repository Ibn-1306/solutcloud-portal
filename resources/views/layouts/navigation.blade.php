<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo / Favicon dans la barre de navigation -->
                <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}">
                <img src="{{ asset('img/favicon.png') }}" class="block h-10 w-auto fill-current text-black" alt="Solutcloud">
                </a>
                <span class="ml-3 font-bold text-black hidden sm:block">SOLUTCLOUD_GESTION</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')">
                        {{ __('Tableau de bord - Gestion') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.demos.index')" :active="request()->routeIs('admin.demos.*')">
                        {{ __('Démonstration') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.quotes.index')" :active="request()->routeIs('admin.quotes.*')">
                        {{ __('Devis') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-3 py-1 border border-gray-200 rounded-full text-sm font-bold text-gray-700 bg-white hover:bg-teal-50 hover:text-teal-700 hover:border-teal-100 focus:outline-none transition duration-150 ease-in-out shadow-sm">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-100 text-teal-600 shadow-sm">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="pr-2">Administrateur</div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Se déconnecter') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')">
                {{ __('Tableau de bord - Gestion') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.demos.index')" :active="request()->routeIs('admin.demos.*')">
                {{ __('Démonstration') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.quotes.index')" :active="request()->routeIs('admin.quotes.*')">
                {{ __('Devis') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50/50">
            <div class="px-4 flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-teal-600 shadow-sm">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-black text-base text-teal-700">Administrateur</div>
                    <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Voir mon profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Se déconnecter') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
