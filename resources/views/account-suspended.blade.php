<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <title>Compte suspendu — SOLUTCLOUD</title>
    <style>
        :root { color-scheme: light; --brand:#2b909a; --brand-dark:#155b63; --ink:#0f172a; --muted:#64748b; --line:#dce7e9; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:linear-gradient(145deg,#f7fbfb 0%,#fff 50%,#eef8f8 100%); color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        .page { min-height:100vh; display:grid; place-items:center; padding:32px 18px; position:relative; overflow:hidden; }
        .page::before,.page::after { content:""; position:absolute; border:1px solid rgba(43,144,154,.15); border-radius:999px; pointer-events:none; }
        .page::before { width:460px; height:460px; right:-240px; top:-190px; }
        .page::after { width:320px; height:320px; left:-190px; bottom:-180px; }
        .card { width:min(100%,760px); overflow:hidden; border:1px solid var(--line); border-radius:28px; background:rgba(255,255,255,.96); box-shadow:0 28px 80px rgba(15,23,42,.10); position:relative; z-index:1; }
        .topline { height:7px; background:linear-gradient(90deg,var(--brand-dark),var(--brand),#6acbd2); }
        .content { padding:42px clamp(24px,7vw,64px) 36px; }
        .brand { display:flex; align-items:center; gap:12px; color:var(--brand); font-weight:900; letter-spacing:.08em; }
        .brand img { width:42px; height:42px; object-fit:contain; }
        .status { display:inline-flex; align-items:center; gap:9px; margin-top:38px; padding:8px 13px; border:1px solid #fecaca; border-radius:999px; background:#fff7f7; color:#b42318; font-size:12px; font-weight:850; text-transform:uppercase; letter-spacing:.08em; }
        .status span { width:8px; height:8px; border-radius:50%; background:#e5484d; box-shadow:0 0 0 5px rgba(229,72,77,.11); }
        h1 { max-width:620px; margin:22px 0 0; font-size:clamp(34px,6vw,58px); line-height:1.04; letter-spacing:-.045em; }
        .company { color:var(--brand); }
        .lead { max-width:610px; margin:24px 0 0; color:var(--muted); font-size:17px; line-height:1.75; }
        .notice { margin-top:30px; padding:20px 22px; border:1px solid var(--line); border-radius:18px; background:#f8fbfb; }
        .notice strong { display:block; margin-bottom:7px; color:var(--ink); font-size:15px; }
        .notice p { margin:0; color:var(--muted); font-size:14px; line-height:1.65; }
        .actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:28px; }
        .button { min-height:48px; display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:0 20px; border:1px solid var(--brand); border-radius:14px; background:var(--brand); color:#fff; font-size:14px; font-weight:800; text-decoration:none; }
        .button.secondary { border-color:var(--line); background:#fff; color:var(--brand-dark); }
        .refresh { display:flex; align-items:center; gap:10px; margin-top:28px; color:#718096; font-size:13px; font-weight:650; }
        .refresh::before { content:""; width:9px; height:9px; flex:none; border-radius:50%; background:#22a06b; box-shadow:0 0 0 5px rgba(34,160,107,.10); }
        .footer { display:flex; flex-wrap:wrap; justify-content:space-between; gap:12px; padding:19px clamp(24px,7vw,64px); border-top:1px solid #e8eff0; background:#f8fbfb; color:#8493a3; font-size:11px; font-weight:700; letter-spacing:.04em; }
        .logout { margin:0; }
        .logout button { appearance:none; border:0; background:none; padding:0; color:var(--brand-dark); font:inherit; cursor:pointer; }
        @media (max-width:560px) { .content { padding-top:32px; } .status { margin-top:30px; } .actions,.button { width:100%; } .footer { flex-direction:column; } }
    </style>
</head>
<body>
    <main class="page">
        <section class="card" aria-labelledby="suspended-title">
            <div class="topline"></div>
            <div class="content">
                <div class="brand"><img src="{{ asset('img/favicon.png') }}" alt=""><span>SOLUTCLOUD</span></div>
                <div class="status"><span></span>Accès temporairement suspendu</div>
                <h1 id="suspended-title">Votre espace client est <span class="company">suspendu.</span></h1>
                <p class="lead">
                    @if($company)
                        L’accès SOLUTCLOUD de <strong>{{ $company->name }}</strong> a été suspendu par l’administration.
                    @else
                        Cet espace SOLUTCLOUD a été suspendu par l’administration.
                    @endif
                    Vos données restent conservées, mais le compte client et l’instance sont indisponibles jusqu’à leur réactivation.
                </p>

                <div class="notice">
                    <strong>Besoin d’en savoir plus ?</strong>
                    <p>Contactez le service client SOLUTCLOUD. Notre équipe pourra vous indiquer la raison de la suspension et les conditions de réactivation.</p>
                </div>

                <div class="actions">
                    <a class="button" href="mailto:sales@i-solutions.ci?subject=Compte%20SOLUTCLOUD%20suspendu">Contacter le service client <span aria-hidden="true">→</span></a>
                    <a class="button secondary" href="tel:+2250101559505">+225 01 01 55 95 05</a>
                </div>

                @if($statusUrl)
                    <p class="refresh" id="suspension-refresh" role="status" aria-live="polite">Vérification automatique de la réactivation en cours…</p>
                @endif
            </div>
            <footer class="footer">
                <span>© {{ now()->year }} I-SOLUTIONS · SOLUTCLOUD</span>
                @auth
                    <form class="logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Se déconnecter</button></form>
                @else
                    <span>sales@i-solutions.ci</span>
                @endauth
            </footer>
        </section>
    </main>

    @if($statusUrl)
        <script>
            (() => {
                const statusUrl = @json($statusUrl);
                const message = document.getElementById('suspension-refresh');

                const checkStatus = async () => {
                    try {
                        const separator = statusUrl.includes('?') ? '&' : '?';
                        const response = await fetch(statusUrl + separator + '_=' + Date.now(), {
                            cache: 'no-store',
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) return;
                        const payload = await response.json();

                        if (payload.status === 'active' && payload.redirect_url) {
                            message.textContent = 'Compte réactivé. Retour vers votre espace…';
                            window.location.replace(payload.redirect_url);
                        }
                    } catch (_) {
                        // Une interruption réseau temporaire sera retentée automatiquement.
                    }
                };

                checkStatus();
                window.setInterval(checkStatus, 4000);
            })();
        </script>
    @endif
</body>
</html>
