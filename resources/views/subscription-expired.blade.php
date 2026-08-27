<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#ffffff">

    <title>Abonnement expiré — SOLUTCLOUD</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <style>
        :root {
            --brand: #2b909a;
            --brand-dark: #176f77;
            --brand-soft: #edf8f8;
            --danger: #dc2626;
            --ink: #0b1220;
            --muted: #64748b;
            --line: #dde6ec;
            --surface: #ffffff;
            --background: #f5f8fa;
        }

        * {
            box-sizing: border-box;
        }

        html {
            color-scheme: light;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 0%, rgba(43, 144, 154, .10), transparent 27rem),
                radial-gradient(circle at 95% 90%, rgba(43, 144, 154, .08), transparent 30rem),
                var(--background);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
        }

        .page {
            width: min(1180px, calc(100% - 40px));
            min-height: 100vh;
            margin: 0 auto;
            padding: 28px 0;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            text-decoration: none;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .13em;
        }

        .brand-label {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .content {
            flex: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(300px, .65fr);
            align-items: center;
            gap: clamp(42px, 8vw, 108px);
            padding: 72px 0;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 22px;
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .status svg {
            width: 21px;
            height: 21px;
        }

        h1 {
            max-width: 820px;
            margin: 0;
            font-size: clamp(2.75rem, 6.3vw, 6rem);
            line-height: .98;
            letter-spacing: -.065em;
        }

        .lead {
            max-width: 720px;
            margin: 30px 0 0;
            color: #475569;
            font-size: clamp(1rem, 1.4vw, 1.2rem);
            line-height: 1.75;
        }

        .lead strong {
            color: var(--ink);
        }

        .primary-action {
            display: inline-flex;
            min-height: 56px;
            margin-top: 34px;
            padding: 0 28px;
            align-items: center;
            justify-content: center;
            gap: 11px;
            border: 1px solid var(--brand);
            border-radius: 8px;
            color: #fff;
            background: var(--brand);
            box-shadow: 0 12px 28px rgba(43, 144, 154, .20);
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
        }

        .primary-action:focus-visible,
        .contact-link:focus-visible,
        .brand:focus-visible {
            outline: 3px solid rgba(43, 144, 154, .28);
            outline-offset: 4px;
        }

        .primary-action svg {
            width: 18px;
            height: 18px;
        }

        .support-card {
            position: relative;
            overflow: hidden;
            padding: clamp(26px, 4vw, 42px);
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .08);
        }

        .support-card::before {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            top: -105px;
            right: -90px;
            border: 30px solid var(--brand-soft);
            border-radius: 50%;
        }

        .support-card h2 {
            position: relative;
            margin: 0;
            white-space: nowrap;
            font-size: clamp(1.35rem, 2vw, 1.75rem);
            letter-spacing: -.03em;
        }

        .support-card > p {
            position: relative;
            margin: 12px 0 28px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .contact-list {
            display: grid;
            gap: 12px;
        }

        .contact-link {
            display: grid;
            grid-template-columns: 26px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            padding: 17px 0;
            border-top: 1px solid var(--line);
            color: var(--ink);
            text-decoration: none;
        }

        .contact-link svg {
            width: 22px;
            height: 22px;
            color: var(--brand);
        }

        .contact-type {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .contact-value {
            display: block;
            overflow-wrap: anywhere;
            font-size: 14px;
            font-weight: 750;
        }

        .note {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 26px;
            padding-top: 23px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .note svg {
            flex: 0 0 auto;
            width: 18px;
            height: 18px;
            margin-top: 1px;
            color: var(--brand);
        }

        footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 22px;
            border-top: 1px solid rgba(203, 213, 225, .75);
            color: var(--muted);
            font-size: 11px;
        }

        @media (max-width: 860px) {
            .page {
                width: min(100% - 30px, 680px);
            }

            .content {
                grid-template-columns: 1fr;
                gap: 44px;
                padding: 64px 0;
            }

            h1 {
                max-width: 650px;
                font-size: clamp(2.75rem, 12vw, 5.25rem);
            }
        }

        @media (max-width: 520px) {
            .page {
                width: min(100% - 24px, 480px);
                padding-top: 20px;
            }

            .content {
                padding: 52px 0 42px;
            }

            h1 {
                font-size: clamp(2.55rem, 13vw, 4rem);
            }

            .lead {
                margin-top: 24px;
                line-height: 1.65;
            }

            .primary-action {
                width: 100%;
                padding-inline: 18px;
            }

            .support-card {
                border-radius: 14px;
            }

            footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header>
            <a class="brand" href="https://solutcloud.com" aria-label="Accueil SOLUTCLOUD">
                <img src="{{ asset('img/favicon.png') }}" alt="">
                <span>
                    <span class="brand-name">SOLUTCLOUD</span>
                    <span class="brand-label">Votre gestion, simplement.</span>
                </span>
            </a>
        </header>

        <main class="content">
            <section aria-labelledby="expired-title">
                <div class="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                    Instance temporairement suspendue
                </div>

                <h1 id="expired-title">Votre abonnement est arrivé à expiration.</h1>

                <p class="lead">
                    Pour retrouver l’accès à votre instance, rendez-vous dans votre
                    <strong>espace compte client</strong>, choisissez votre période de renouvellement
                    et procédez au paiement sécurisé. L’instance pourra ensuite être réactivée.
                </p>

                <a class="primary-action" href="{{ route('client.renew') }}">
                    Renouveler mon abonnement
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </a>
            </section>

            <aside class="support-card" aria-labelledby="support-title">
                <h2 id="support-title">Besoin d’assistance ?</h2>
                <p>L’équipe SOLUTCLOUD vous accompagne pour le renouvellement et la réactivation de votre service.</p>

                <div class="contact-list">
                    <a class="contact-link" href="mailto:sales@i-solutions.ci?subject=Réactivation%20de%20mon%20instance%20SOLUTCLOUD">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m4 7 8 6 8-6"></path>
                        </svg>
                        <span>
                            <span class="contact-type">E-mail</span>
                            <span class="contact-value">sales@i-solutions.ci</span>
                        </span>
                    </a>

                    <a class="contact-link" href="tel:+2250101559505">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M7 3h3l1.5 4-2 1.5a15 15 0 0 0 6 6l1.5-2 4 1.5v3a3 3 0 0 1-3 3C10.3 20 4 13.7 4 6a3 3 0 0 1 3-3Z"></path>
                        </svg>
                        <span>
                            <span class="contact-type">Téléphone</span>
                            <span class="contact-value">+225 01 01 55 95 05</span>
                        </span>
                    </a>
                </div>

                <div class="note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 4.6 2.8 8.2 7 10 4.2-1.8 7-5.4 7-10V6l-7-3Z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Vos données restent conservées et protégées pendant la suspension de votre instance.</span>
                </div>
            </aside>
        </main>

        <footer>
            <span>SOLUTCLOUD · I-SOLUTIONS</span>
            <span>Service client et assistance au renouvellement</span>
        </footer>
    </div>
</body>
</html>
