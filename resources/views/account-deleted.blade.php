<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <title>Compte supprimé — SOLUTCLOUD</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <style>
        :root { color-scheme:light; --brand:#2b909a; --ink:#0f172a; --muted:#64748b; --line:#dce7e9; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:linear-gradient(145deg,#f7fbfb,#fff 52%,#eef8f8); color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        main { min-height:100vh; display:grid; place-items:center; padding:24px; }
        section { width:min(100%,650px); border:1px solid var(--line); border-radius:28px; background:#fff; padding:clamp(30px,7vw,58px); text-align:center; }
        img { width:auto; height:50px; }
        .icon { width:82px; height:82px; display:grid; place-items:center; margin:34px auto 0; border-radius:50%; background:#fff1f2; color:#be123c; }
        .icon svg { width:38px; height:38px; }
        p.label { margin:24px 0 0; color:#be123c; font-size:12px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
        h1 { margin:12px 0 0; font-size:clamp(30px,6vw,46px); line-height:1.08; letter-spacing:-.04em; }
        p.message { max-width:490px; margin:20px auto 0; color:var(--muted); font-size:16px; line-height:1.75; }
        footer { margin-top:34px; padding-top:20px; border-top:1px solid #edf2f3; color:#94a3b8; font-size:11px; font-weight:700; }
    </style>
</head>
<body>
    <main>
        <section aria-labelledby="deleted-title">
            <img src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" alt="SOLUTCLOUD">
            <div class="icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <p class="label">Accès clôturé</p>
            <h1 id="deleted-title">Ce compte a été supprimé.</h1>
            <p class="message">L’administrateur SOLUTCLOUD a supprimé ce compte et son accès à l’instance.</p>
            <footer>© {{ now()->year }} I-SOLUTIONS · SOLUTCLOUD</footer>
        </section>
    </main>
</body>
</html>
