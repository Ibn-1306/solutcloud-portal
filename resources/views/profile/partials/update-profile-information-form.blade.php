<section>

    <header class="text-center mb-8">

        <h2 class="text-xl font-bold text-gray-900">
            Adresse e-mail
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            Modifiez l'adresse e-mail utilisée pour accéder à votre espace administrateur.
        </p>

    </header>


    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>



    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="max-w-sm mx-auto space-y-6"
    >

        @csrf
        @method('patch')


        <div>

            <x-input-label
                for="email"
                :value="__('Adresse e-mail')"
                class="text-sm font-semibold text-gray-700"
            />


            <x-text-input

                id="email"

                name="email"

                type="email"

                class="mt-2 block w-full rounded-xl border-gray-300 focus:border-[#2B909A] focus:ring-[#2B909A]"

                :value="old('email', $user->email)"

                required

                autocomplete="username"

            />


            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />

        </div>



        <div class="flex justify-center pt-2">

            <x-primary-button
                class="rounded-xl bg-[#2B909A] px-8 py-3 font-bold hover:bg-[#247c85]"
            >

                Enregistrer

            </x-primary-button>


        </div>


    </form>


</section>