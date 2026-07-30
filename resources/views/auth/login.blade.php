<!-- Design Hero-Login Premium SOLUTCLOUD -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOLUTCLOUD - Connexion</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; } /* Fond blanc pour correspondre à ton gradient */
        .hero-linear-gradient { background: #ffffff; }
        .glass-card { 
            background: rgba(255, 255, 255, 0.02); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(0, 0, 0, 0.08); /* Ajusté pour fond clair */
        }
        /* Animation du curseur clignotant */
        .cursor {
            color: #319795;
            font-weight: 200;
            animation: blink 1s infinite;
            margin-left: 5px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen flex flex-col">
    <!-- TOPBAR : Responsive font-size and padding -->
    <div class="w-full bg-[#319795] py-2 px-4 flex flex-col sm:flex-row justify-center items-center gap-2 sm:gap-4 text-center sm:text-left font-semibold shadow-lg">
        <span class="text-sm sm:text-base text-white">SOLUTCLOUD - Espace compte</span>
        <div class="bg-white/20 px-3 py-0.5 rounded border border-black/30 text-white text-[14px] tracking-wider">
            sales@i-solutions.ci
        </div>
    </div>

    <div class="hero-linear-gradient flex-grow flex flex-col">
        <!-- NAV : Logo size and padding responsive -->
        <nav class="max-w-7xl mx-auto w-full px-6 sm:px-2 py-6 sm:py-10 flex justify-between items-center">
            <img src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" alt="SOLUTCLOUD" class="h-12 sm:h-20 w-auto">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[9px] sm:text-[12px] uppercase tracking-[0.22em] text-black font-bold">Système Opérationnel</span>
            </div>
        </nav>

        <!-- MAIN : Column on mobile, Row on desktop -->
        <main class="max-w-7xl mx-auto w-full px-6 sm:px-12 flex-grow flex flex-col lg:flex-row items-center justify-center lg:justify-between gap-12 lg:gap-20 pb-16 sm:pb-24">
            
            <!-- TEXT : Centered on mobile, Left on desktop -->
            <div class="w-full lg:w-1/2 space-y-6 sm:space-y-8 text-center lg:text-left">
                <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold tracking-tighter leading-[1.1] text-black">
                    Il est temps de vous digitaliser avec <br class="hidden lg:block">
                    <span id="typewriter" class="text-[#319795]"></span><span class="cursor">|</span>
                </h1>

                <p class="text-gray-500 text-lg sm:text-xl max-w-lg mx-auto lg:mx-0 font-light leading-relaxed">
                    Connectez-vous pour gérer votre instance et piloter intelligemment votre business.
                </p>
            </div>

            <!-- CARD : Full width on mobile, fixed on desktop -->
            <div class="w-full sm:max-w-[460px] lg:w-[460px]">
                <div class="glass-card p-6 sm:p-10 rounded-3xl shadow-2xl bg-white/50 border border-gray-100">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-2 text-black">Bienvenue !</h2>
                    <p class="text-gray-500 text-sm mb-6 sm:mb-8">Identifiez-vous pour accéder à votre instance</p>
                    
                    @if ($errors->any())
                        <div class="mb-6 p-2 text-sm text-red-500">Identifiants non reconnus.</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5 sm:space-y-6">
                        @csrf
                        <div class="text-left">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Identifiant</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full bg-black/5 border border-black/10 rounded-xl px-5 py-3 text-black focus:outline-none focus:border-[#319795] transition-all" placeholder="me@email.com">
                        </div>
                        <div class="text-left">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Mot de passe</label>
                            <input type="password" name="password" required class="w-full bg-black/5 border border-black/10 rounded-xl px-5 py-3 text-black focus:outline-none focus:border-[#319795] transition-all" placeholder="••••••••••••">
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-[#000000]/80 text-white font-bold px-8 py-3 rounded-lg hover:bg-[#319795] transition-all shadow-lg">
                            ACCÈS AUTORISÉ
                        </button>
                    </form>
                </div>
            </div>
        </main>

        <!-- FOOTER : Margin adjusted for mobile -->
        <footer class="w-full py-6 sm:py-8 mt-auto text-center border-t border-gray-100">
            <p class="text-[9px] sm:text-[12px] uppercase tracking-[0.25em] text-gray-500 font-bold">
                &copy; 2026 I-SOLUTIONS CI
            </p>
        </footer>
    </div>

    <script>
        const text = "SOLUTCLOUD";
        const speed = 150;
        const typewriter = document.getElementById('typewriter');
        let i = 0;

        function typeEffect() {
            if (i < text.length) {
                typewriter.innerHTML += text.charAt(i);
                i++;
                setTimeout(typeEffect, speed);
            } else {
                setTimeout(() => {
                    const cursor = document.querySelector('.cursor');
                    if(cursor) cursor.style.display = 'none';
                }, 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(typeEffect, 500);
        });
    </script>
</body>
</html>