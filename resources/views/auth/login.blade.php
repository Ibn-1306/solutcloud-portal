<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SOLUTCLOUD — Connexion</title>

    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand: #2B909A;
            --brand-dark: #176f77;
            --brand-soft: #eaf6f7;

            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --surface: #ffffff;

            --left-dark: #071c20;
            --left-mid: #0d3439;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: "Inter", sans-serif;
            color: var(--text);
            background: #fff;
            -webkit-font-smoothing: antialiased;
        }

        button,
        input {
            font: inherit;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(520px, .95fr);
            background: #fff;
        }

        /* =========================
           LEFT EXPERIENCE
        ========================== */

        .login-visual {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 42px 52px 46px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #fff;
            background:
                radial-gradient(circle at 70% 18%, rgba(65, 196, 207, .20), transparent 25%),
                radial-gradient(circle at 18% 78%, rgba(43, 144, 154, .18), transparent 30%),
                linear-gradient(145deg, var(--left-dark) 0%, #08272c 48%, var(--left-mid) 100%);
            isolation: isolate;
        }

        .login-visual::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            opacity: .32;
            background-image:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: linear-gradient(to bottom, #000, transparent 92%);
        }

        .login-visual::after {
            content: "";
            position: absolute;
            width: 620px;
            height: 620px;
            border-radius: 50%;
            right: -280px;
            bottom: -280px;
            background: rgba(52, 181, 191, .11);
            border: 1px solid rgba(255,255,255,.06);
            z-index: -1;
        }

        .visual-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            display: block;
            width: auto;
            height: 62px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .environment-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 13px;
            border: 1px solid rgba(255,255,255,.15);
            background: rgba(255,255,255,.06);
            border-radius: 999px;
            backdrop-filter: blur(10px);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            color: rgba(255,255,255,.80);
        }

        .environment-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #58d7b4;
            box-shadow: 0 0 0 5px rgba(88,215,180,.09);
        }

        .visual-stage {
            position: relative;
            z-index: 2;
            width: min(700px, 100%);
            margin: auto;
        }

        .visual-kicker {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 22px;
            color: #8edbe0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .visual-kicker::before {
            content: "";
            width: 34px;
            height: 1px;
            background: #5fc4cc;
        }

        .visual-title {
            margin: 0;
            max-width: 670px;
            font-size: clamp(42px, 5vw, 72px);
            line-height: .98;
            letter-spacing: -.055em;
            font-weight: 800;
        }

        .visual-title span {
            color: #71cbd2;
        }

        .visual-description {
            margin: 28px 0 0;
            max-width: 570px;
            color: rgba(255,255,255,.68);
            font-size: 16px;
            line-height: 1.75;
        }

                /* ===================================
        LOGIN 3D SOLUTCLOUD
        =================================== */

        .login-3d-zone {
            position: relative;
            width: min(720px, 100%);
            height: 420px;
            margin-top: 34px;
            overflow: hidden;
            border-radius: 24px;

            background:
                radial-gradient(
                    circle at 50% 45%,
                    rgba(43, 144, 154, .13),
                    transparent 44%
                ),
                rgba(255,255,255,.025);

            border: 1px solid rgba(255,255,255,.08);

            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 30px 70px rgba(0,0,0,.18);

            isolation: isolate;
        }

        .login-3d-zone::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;

            background:
                linear-gradient(
                    180deg,
                    rgba(7,28,32,.08),
                    transparent 28%,
                    transparent 72%,
                    rgba(7,28,32,.22)
                );
        }

        .login-3d-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .login-3d-canvas canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
            outline: none;
        }

        .login-3d-caption {
            position: absolute;
            z-index: 4;
            left: 20px;
            right: 20px;
            bottom: 17px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;

            pointer-events: none;
        }

        .login-3d-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            color: rgba(255,255,255,.66);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .15em;
        }

        .login-3d-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #5ed0c1;

            box-shadow:
                0 0 0 4px rgba(94,208,193,.08),
                0 0 14px rgba(94,208,193,.35);
        }

        .login-3d-hint {
            color: rgba(255,255,255,.38);
            font-size: 9px;
            font-weight: 500;
        }

        @media (max-width: 1100px) {
            .login-3d-zone {
                height: 350px;
            }
        }

        @media (max-width: 820px) {
            .login-3d-zone {
                display: none;
            }
        }

        .software-sidebar {
            padding: 22px 16px;
            background: #f7fafb;
            border-right: 1px solid #edf0f2;
        }

        .sidebar-logo {
            width: 35px;
            height: 9px;
            border-radius: 10px;
            background: var(--brand);
            margin-bottom: 24px;
        }

        .sidebar-line {
            width: 76%;
            height: 6px;
            margin: 15px 0;
            border-radius: 10px;
            background: #dfe7e9;
        }

        .sidebar-line.active {
            width: 88%;
            background: rgba(43,144,154,.45);
        }

        .software-content {
            padding: 22px;
        }

        .dash-title {
            width: 122px;
            height: 10px;
            border-radius: 10px;
            background: #24383c;
            margin-bottom: 21px;
        }

        .dash-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .dash-stat {
            min-height: 67px;
            border: 1px solid #e8edef;
            border-radius: 10px;
            background: #fff;
            padding: 13px;
        }

        .dash-stat::before {
            content: "";
            display: block;
            width: 32%;
            height: 5px;
            border-radius: 5px;
            background: #dbe4e6;
            margin-bottom: 11px;
        }

        .dash-stat::after {
            content: "";
            display: block;
            width: 63%;
            height: 9px;
            border-radius: 5px;
            background: #36545a;
        }

        .dash-chart {
            height: 95px;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background:
                linear-gradient(to top, rgba(43,144,154,.07), transparent),
                repeating-linear-gradient(
                    to bottom,
                    transparent,
                    transparent 22px,
                    #edf1f3 23px
                );
        }

        .chart-line {
            position: absolute;
            left: 6%;
            right: 6%;
            top: 48%;
            height: 2px;
            background: var(--brand);
            transform: rotate(-6deg);
            box-shadow:
                80px -11px 0 -1px var(--brand),
                170px 7px 0 -1px var(--brand),
                260px -17px 0 -1px var(--brand);
        }

        .visual-footer {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255,255,255,.45);
            font-size: 11px;
        }

        /* =========================
           RIGHT LOGIN
        ========================== */

        .login-panel {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            padding: 60px clamp(45px, 7vw, 120px);
        }

        .login-container {
            width: min(430px, 100%);
            margin: auto;
        }

        .mobile-logo {
            display: none;
        }

        .login-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: var(--brand);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .login-eyebrow::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--brand);
            box-shadow: 0 0 0 5px rgba(43,144,154,.08);
        }

        .login-heading {
            margin: 0;
            color: #151b20;
            font-size: 36px;
            line-height: 1.1;
            letter-spacing: -.045em;
            font-weight: 800;
        }

        .login-subtitle {
            margin: 13px 0 38px;
            color: #7b858e;
            font-size: 14px;
            line-height: 1.65;
        }

        .form-alert {
            margin-bottom: 24px;
            padding: 13px 15px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #b42318;
            background: #fff7f7;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
        }

        .form-label {
            color: #2a343b;
            font-size: 12px;
            font-weight: 650;
        }

        .forgot-link {
            color: #69757c;
            font-size: 11px;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--brand);
        }

        .input-wrap {
            position: relative;
        }

        .form-input {
            width: 100%;
            height: 52px;
            padding: 0 46px 0 16px;
            border: 1px solid #dbe0e4;
            border-radius: 8px;
            color: #152025;
            background: #fff;
            font-size: 14px;
            outline: none;
            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .form-input::placeholder {
            color: #a0a7ad;
        }

        .form-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(43,144,154,.08);
            background: #fff;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #8d979e;
            pointer-events: none;
        }

        .remember-row {
            margin: 2px 0 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .remember-row input {
            width: 15px;
            height: 15px;
            accent-color: var(--brand);
        }

        .remember-row label {
            font-size: 12px;
            color: #5d686f;
        }

        .login-button {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 7px;
            color: #fff;
            background: #11191d;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .035em;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(17,25,29,.12);
            transition:
                transform .18s ease,
                background-color .2s ease,
                box-shadow .2s ease;
        }

        .login-button:hover {
            background: var(--brand);
            box-shadow: 0 15px 32px rgba(43,144,154,.20);
            transform: translateY(-1px);
        }

        .security-note {
            margin-top: 27px;
            padding-top: 25px;
            border-top: 1px solid #edf0f2;
            display: flex;
            align-items: flex-start;
            gap: 11px;
        }

        .security-icon {
            flex: 0 0 auto;
            width: 31px;
            height: 31px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: var(--brand);
            background: var(--brand-soft);
        }

        .security-note strong {
            display: block;
            margin-bottom: 3px;
            color: #354147;
            font-size: 11px;
        }

        .security-note span {
            display: block;
            color: #899197;
            font-size: 10px;
            line-height: 1.45;
        }

        .right-footer {
            width: min(430px, 100%);
            margin: 42px auto 0;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            color: #a0a7ac;
            font-size: 10px;
        }

        .right-footer a {
            color: inherit;
            text-decoration: none;
        }

        .right-footer a:hover {
            color: var(--brand);
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 1100px) {
            .login-shell {
                grid-template-columns: .9fr 1.1fr;
            }

            .login-visual {
                padding: 36px 36px 40px;
            }

            .visual-title {
                font-size: 50px;
            }

            .software-card {
                transform: none;
            }
        }

        @media (max-width: 820px) {
            .login-shell {
                display: block;
            }

            .login-visual {
                display: none;
            }

            .login-panel {
                min-height: 100vh;
                padding: 38px 22px;
            }

            .login-container {
                width: min(430px, 100%);
            }

            .mobile-logo {
                display: block;
                height: 60px;
                width: auto;
                margin: 0 0 60px;
            }

            .login-heading {
                font-size: 31px;
            }

            .right-footer {
                margin-top: 60px;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 28px 20px;
            }

            .mobile-logo {
                height: 52px;
                margin-bottom: 48px;
            }

            .login-heading {
                font-size: 28px;
            }

            .login-subtitle {
                margin-bottom: 30px;
            }

            .right-footer {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>

<body>

<div class="login-shell">

    <!-- ================================
         PARTIE GAUCHE
    ================================= -->
    <section class="login-visual">

        <header class="visual-header">

            <img
                src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}"
                alt="SOLUTCLOUD"
                class="brand-logo"
            >

            <div class="environment-badge">
                <span class="environment-dot"></span>
                ERP · CRM · CLOUD
            </div>

        </header>

        <div class="visual-stage">

            <div class="visual-kicker">
                Espace professionnel
            </div>

            <h1 class="visual-title">
                Pilotez votre activité.
                <span>Sans complexité.</span>
            </h1>

            <p class="visual-description">
                Accédez à votre environnement SOLUTCLOUD et retrouvez
                vos outils de gestion, vos données et votre activité
                dans un espace sécurisé et centralisé.
            </p>

        <div class="login-3d-zone">

        <div
                id="login-erp-crm-3d"
                class="login-3d-canvas"
                aria-label="Écosystème ERP CRM SOLUTCLOUD"
            ></div>

            <div class="login-3d-caption">

                <span class="login-3d-status">
                    <span class="login-3d-status-dot"></span>
                    ÉCOSYSTÈME CONNECTÉ
                </span>

                <span class="login-3d-hint">
                    Déplacez la souris pour explorer
                </span>

            </div>

        </div>

        <footer class="visual-footer">
            <span>SOLUTCLOUD</span>
            <span>© 2026 I-SOLUTIONS CI</span>
        </footer>

    </section>


    <!-- ================================
         PARTIE DROITE
    ================================= -->
    <main class="login-panel">

        <div class="login-container">

            <!-- Logo mobile -->
            <img
                src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}"
                alt="SOLUTCLOUD"
                class="mobile-logo"
            >

            <div class="login-eyebrow">
                Connexion sécurisée
            </div>

            <h2 class="login-heading">
                Bienvenue sur SOLUTCLOUD
            </h2>

            <p class="login-subtitle">
                Connectez-vous à votre espace pour accéder à votre
                environnement de gestion.
            </p>

            @if ($errors->any())
                <div class="form-alert">
                    L’adresse e-mail ou le mot de passe est incorrect.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">

                @csrf

                <!-- EMAIL -->
                <div class="form-group">

                    <div class="label-row">
                        <label for="email" class="form-label">
                            Adresse e-mail
                        </label>
                    </div>

                    <div class="input-wrap">

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="form-input"
                            placeholder="nom@entreprise.com"
                        >

                        <svg
                            class="input-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M4 6h16v12H4z"/>
                            <path d="m4 7 8 6 8-6"/>
                        </svg>

                    </div>

                </div>


                <!-- PASSWORD -->
                <div class="form-group">

                    <div class="label-row">

                        <label for="password" class="form-label">
                            Mot de passe
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="forgot-link"
                            >
                                Mot de passe oublié ?
                            </a>
                        @endif

                    </div>

                    <div class="input-wrap">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="form-input"
                            placeholder="Votre mot de passe"
                        >

                        <svg
                            class="input-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                        </svg>

                    </div>

                </div>


                <!-- REMEMBER -->
                <div class="remember-row">

                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                    >

                    <label for="remember">
                        Rester connecté sur cet appareil
                    </label>

                </div>


                <!-- BUTTON -->
                <button
                    type="submit"
                    class="login-button"
                >
                    Se connecter
                </button>

            </form>


            <!-- SECURITY -->
            <div class="security-note">

                <div class="security-icon">

                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect x="5" y="10" width="14" height="10" rx="2"/>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    </svg>

                </div>

                <div>
                    <strong>Connexion protégée</strong>
                    <span>
                        Vos échanges avec SOLUTCLOUD sont sécurisés
                        et protégés par une connexion chiffrée.
                    </span>
                </div>

            </div>

        </div>


        <footer class="right-footer">
            <span>© 2026 I-SOLUTIONS CI</span>

            <div>
                <a href="#">Confidentialité</a>
                &nbsp;·&nbsp;
                <a href="#">Conditions</a>
            </div>
        </footer>

    </main>

</div>

</body>
</html>