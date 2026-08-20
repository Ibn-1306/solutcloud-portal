<section>

    <header class="text-center mb-8">

        <h2 class="text-xl font-bold text-gray-900">
            Sécurité du compte
        </h2>

        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
            Modifiez votre mot de passe pour protéger votre espace administrateur SOLUTCLOUD.
        </p>

    </header>



    <form
        method="post"
        action="{{ route('password.update') }}"
        class="max-w-sm mx-auto space-y-6"
    >

        @csrf
        @method('put')


        <!-- Mot de passe actuel -->
        <div>

            <x-input-label
                for="update_password_current_password"
                :value="__('Mot de passe actuel')"
                class="text-sm font-semibold text-gray-700"
            />


            <x-text-input

                id="update_password_current_password"

                name="current_password"

                type="password"

                class="mt-2 block w-full rounded-xl border-gray-300 focus:border-[#2B909A] focus:ring-[#2B909A]"

                autocomplete="current-password"

            />


            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />

        </div>




        <!-- Nouveau mot de passe -->
        <div>

            <x-input-label
                for="update_password_password"
                :value="__('Nouveau mot de passe')"
                class="text-sm font-semibold text-gray-700"
            />


            <x-text-input

                id="update_password_password"

                name="password"

                type="password"

                class="mt-2 block w-full rounded-xl border-gray-300 focus:border-[#2B909A] focus:ring-[#2B909A]"

                autocomplete="new-password"

            />


            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />

        </div>




        <!-- Confirmation -->
        <div>

            <x-input-label
                for="update_password_password_confirmation"
                :value="__('Confirmation du mot de passe')"
                class="text-sm font-semibold text-gray-700"
            />


            <x-text-input

                id="update_password_password_confirmation"

                name="password_confirmation"

                type="password"

                class="mt-2 block w-full rounded-xl border-gray-300 focus:border-[#2B909A] focus:ring-[#2B909A]"

                autocomplete="new-password"

            />


            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />

        </div>




        <!-- Bouton -->

        <div class="flex justify-center pt-3">

            <x-primary-button
                class="rounded-xl bg-[#2B909A] px-8 py-3 font-bold hover:bg-[#247c85]"
            >

                Modifier le mot de passe

            </x-primary-button>


        </div>




        @if (session('status') === 'password-updated')

            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-center text-sm font-medium text-green-600"
            >
                Mot de passe modifié avec succès.
            </p>

        @endif


    </form>

</section>