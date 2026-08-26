<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Merci de vous être inscrit(e) ! Avant de commencer, pourriez-vous vérifier votre adresse courriel en cliquant sur le lien que nous venons de vous envoyer ? Si vous ne l’avez pas reçu, nous vous en enverrons un autre avec plaisir.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __("Un nouveau lien de vérification a été envoyé à l'adresse électronique que vous avez fournie lors de votre inscription.") }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __("Renvoyer l'e-mail de vérification") }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Déconnexion') }}
            </button>
        </form>
    </div>
</x-guest-layout>
