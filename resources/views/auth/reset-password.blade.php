<x-guest-layout>

<div class="min-h-screen flex items-center justify-center bg-[#f6fafb] px-6">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">

            <div class="text-center mb-8">

                <x-application-logo class="w-28 h-28 mx-auto mb-5" />

                <h1 class="text-2xl font-bold text-[#1A2E35]">
                    Activez votre espace client
                </h1>

                <p class="mt-3 text-sm text-[#4A6269] leading-relaxed">
                    Bienvenue dans votre espace client SOLUTCLOUD.<br>
                    Créez votre mot de passe sécurisé pour activer votre compte.
                </p>

            </div>


            <form method="POST" action="{{ route('password.store') }}">

                @csrf

                <input 
                    type="hidden" 
                    name="token" 
                    value="{{ $request->route('token') }}"
                >

                @if($request->boolean('activation'))
                    <input type="hidden" name="activation" value="1">
                @endif


                <div class="space-y-5">


                    <div>
                        <x-input-label 
                            for="email" 
                            :value="__('Adresse e-mail professionnelle')" 
                        />

                        <x-text-input 
                            id="email"
                            class="block mt-2 w-full bg-[#f6fafb] cursor-not-allowed"
                            type="email"
                            name="email"
                            :value="old('email', $request->email)"
                            readonly
                        />

                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>



                    <div 
                        class="mt-4"
                        x-data="{ showPassword: false }"
                    >
                        <x-input-label 
                            for="password" 
                            :value="__('Créer mot de passe')" 
                        />

                        <div class="relative mt-2">

                            <x-text-input
                                id="password"
                                class="block w-full pr-12"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="new-password"
                            />

                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#2b909a] transition"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                            >

                                <!-- Œil ouvert : mot de passe caché -->
                                <svg
                                    x-show="!showPassword"
                                    x-cloak
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <!-- Œil barré : mot de passe visible -->
                                <svg
                                    x-show="showPassword"
                                    x-cloak
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M3 3l18 18"/>
                                    <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"/>
                                    <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6 0 9.5 7 9.5 7a15.5 15.5 0 0 1-2.3 3.2"/>
                                    <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a10 10 0 0 0 4.1-.9"/>
                                </svg>

                            </button>

                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>



                    <div 
                        class="mt-4"
                        x-data="{ showPassword: false }"
                    >
                        <x-input-label 
                            for="password_confirmation" 
                            :value="__('Confirmer mot de passe')" 
                        />

                        <div class="relative mt-2">

                            <x-text-input
                                id="password_confirmation"
                                class="block w-full pr-12"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            />

                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#2b909a] transition"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                            >

                                <svg
                                    x-show="!showPassword"
                                    x-cloak
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <svg
                                    x-show="showPassword"
                                    x-cloak
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M3 3l18 18"/>
                                    <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"/>
                                    <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6 0 9.5 7 9.5 7a15.5 15.5 0 0 1-2.3 3.2"/>
                                    <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a10 10 0 0 0 4.1-.9"/>
                                </svg>

                            </button>

                        </div>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>


                </div>


                <div class="mt-8">

                    <x-primary-button class="w-full justify-center py-3">
                        ACTIVER MON ESPACE
                    </x-primary-button>

                </div>


            </form>

        </div>


        <p class="text-center text-xs text-gray-400 mt-6">
            © {{ date('Y') }} SOLUTCLOUD — Solution de gestion N°1
        </p>

    </div>

</div>

    <script>
    function togglePassword(id, button) {

        const input = document.getElementById(id);

        const visible = input.type === "text";

        input.type = visible ? "password" : "text";

        button.innerHTML = visible 
        ? `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7"/>
        </svg>
        `
        :
        `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.06-3.368"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M6.223 6.223l11.554 11.554"/>
        </svg>
        `;
    }
    </script>

</x-guest-layout>