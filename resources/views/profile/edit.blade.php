<x-admin-layout
    title="SOLUTCLOUD — Profil administrateur"
    page-title="Mon profil"
    description="Gérez les informations et la sécurité du compte administrateur."
>


    <div class="py-12 bg-gray-50 min-h-screen">

        <div class="max-w-3xl mx-auto px-6 space-y-8">


            <!-- Identité administrateur -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

                <div class="flex items-center justify-center flex-col text-center">


                    <div class="relative w-24 h-24 rounded-full bg-[#2B909A] flex items-center justify-center shadow-lg">

                        <!-- Icône profil -->
                        <svg 
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-14 h-14 text-white"
                        >
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/>
                            <path 
                                fill-rule="evenodd"
                                d="M4 20a8 8 0 1 1 16 0H4Z"
                                clip-rule="evenodd"
                            />
                        </svg>


                        <!-- Petit réglage -->
                        <div class="absolute -right-1 -bottom-1 w-9 h-9 rounded-full bg-white flex items-center justify-center shadow-m">

                            <svg 
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="w-7 h-7 text-[#2B909A]"
                            >
                                <path 
                                    fill-rule="evenodd"
                                    d="M11.078 2.75a.75.75 0 0 1 1.344 0l.602 1.204a1.5 1.5 0 0 0 1.34.83l1.35.02a.75.75 0 0 1 .53 1.28l-.96.94a1.5 1.5 0 0 0-.43 1.5l.32 1.31a.75.75 0 0 1-1.08.82L12.75 10.1a1.5 1.5 0 0 0-1.5 0l-1.18.56a.75.75 0 0 1-1.08-.82l.32-1.31a1.5 1.5 0 0 0-.43-1.5l-.96-.94a.75.75 0 0 1 .53-1.28l1.35-.02a1.5 1.5 0 0 0 1.34-.83l.602-1.204Z"
                                    clip-rule="evenodd"
                                />
                                <path d="M12 13.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                            </svg>

                        </div>

                    </div>


                    <h3 class="mt-5 text-xl font-bold text-gray-900">
                        Administrateur
                    </h3>


                    <span class="mt-3 inline-flex px-4 py-1 rounded-full text-xs font-semibold bg-[#2B909A]/10 text-[#2B909A]">
                        Compte administrateur
                    </span>


                </div>

            </div>



            <!-- Email -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

                @include('profile.partials.update-profile-information-form')

            </div>

            <!-- Sécurité du compte -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

                @include('profile.partials.update-password-form')

            </div>


        </div>

    </div>


</x-admin-layout>
