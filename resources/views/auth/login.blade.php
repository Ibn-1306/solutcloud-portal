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

    <script>
        (() => {
            const storageKey = 'solutcloud-login-preloader-seen';
            const navigationEntry = performance.getEntriesByType?.('navigation')?.[0];
            const navigationType = navigationEntry?.type
                ?? (performance.navigation?.type === 1 ? 'reload' : 'navigate');
            let shouldShow = false;

            try {
                shouldShow = navigationType === 'reload' || sessionStorage.getItem(storageKey) !== '1';
                sessionStorage.setItem(storageKey, '1');
            } catch {
                shouldShow = true;
            }

            window.solutcloudLoginPreloaderShouldShow = shouldShow;

            if (shouldShow) {
                document.documentElement.classList.add('solutcloud-preloader-active');
            }
        })();
    </script>

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

            color: #ffffff;

            background:
                radial-gradient(
                    circle at 78% 34%,
                    rgba(72, 190, 199, .16) 0%,
                    rgba(72, 190, 199, 0) 32%
                ),

                radial-gradient(
                    circle at 15% 72%,
                    rgba(43, 144, 154, .15) 0%,
                    rgba(43, 144, 154, 0) 30%
                ),

                linear-gradient(
                    180deg,
                    #ffffff 0%,
                    #f8fbfb 7%,
                    #eaf3f4 13%,
                    #bfd9dc 20%,
                    #6d9ca0 28%,
                    #315e63 36%,
                    #153b40 44%,
                    #0d3035 55%,
                    #08272c 72%,
                    #071c20 100%
                );

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

                [x-cloak] {
            display: none !important;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;

            width: 34px;
            height: 34px;

            display: flex;
            align-items: center;
            justify-content: center;

            transform: translateY(-50%);

            padding: 0;
            border: 0;
            border-radius: 50%;

            background: transparent;
            color: rgba(255,255,255,.48);

            cursor: pointer;

            transition:
                color .2s ease,
                background-color .2s ease,
                transform .2s ease;
        }

        .password-toggle svg {
            width: 19px;
            height: 19px;
            pointer-events: none;
        }

        .password-toggle:hover {
            color: #67cbd2;
            background: rgba(43,144,154,.10);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(.90);
        }

        .password-toggle:focus-visible {
            outline: 2px solid #2B909A;
            outline-offset: 2px;
        }

        .visual-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo {
            display: block;
            width: auto;
            height: 100px;
            object-fit: contain;

            /* couleurs originales du logo */
            filter: none;
        }

        .environment-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 9px 15px;

            border: 1px solid rgba(43, 144, 154, .18);

            background: rgba(255, 255, 255, .72);

            border-radius: 999px;

            color: #315e63;

            backdrop-filter: blur(10px);

            font-size: 11px;
            font-weight: 700;
            letter-spacing: .07em;

            box-shadow:
                0 5px 18px rgba(18, 62, 68, .05);
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
            gap: 10px;

            margin-bottom: 22px;

            color: #1f5f66;

            font-size: 12px;
            font-weight: 800;

            letter-spacing: .16em;
            text-transform: uppercase;

            text-shadow: 0 1px 0 rgba(255,255,255,.35);
        }

        .visual-kicker::before {
            content: "";

            width: 34px;
            height: 2px;

            border-radius: 999px;

            background:
                linear-gradient(
                    90deg,
                    #2B909A 0%,
                    #1f5f66 100%
                );

            box-shadow:
                0 1px 4px rgba(43,144,154,.15);
        }

        .visual-title {
            margin: 0;
            max-width: 670px;
            font-size: clamp(42px, 5vw, 72px);
            line-height: .98;
            letter-spacing: -.055em;
            font-weight: 800;
        }

        .visual-title [data-typewriter] {
            color: inherit;
        }

        .typewriter-line {
            display: block;
            min-height: 1em;
            white-space: nowrap;
        }

        .visual-title .typewriter-line:first-child {
            color: #ffffff;
        }

        .typewriter-line--accent {
            color: #71cbd2;
        }

        .typewriter-cursor {
            display: inline-block;
            width: .19em;
            height: .19em;
            margin-left: .13em;
            border-radius: 50%;
            background: #71cbd2;
            box-shadow: 0 0 0 .08em rgba(113, 203, 210, .12);
            vertical-align: .12em;
            animation: typewriter-cursor-pulse 1.05s ease-in-out infinite;
        }

        @keyframes typewriter-cursor-pulse {
            0%, 100% { opacity: .38; transform: scale(.72); }
            50% { opacity: 1; transform: scale(1); }
        }

        .visual-description {
            margin: 26px 0 0;

            max-width: 620px;

            color: rgba(255,255,255,.78);

            font-size: 16px;
            line-height: 1.65;

            font-weight: 400;
        }

        .login-welcome {
            width: 100%;
            text-align: center;
            margin-bottom: 45px;
        }

        .login-favicon {
            width: 100px;
            height: 100px;

            object-fit: contain;

            margin: 0 auto 22px;

            display: block;

            filter: drop-shadow(0 10px 22px rgba(43,144,154,.14));
        }

        .login-heading {
            margin: 0;

            color: #0f172a;

            font-size: 38px;
            line-height: 1.12;

            letter-spacing: -.045em;

            font-weight: 800;

            text-transform: none;
        }

        .login-subtitle {
            width: 100%;
            max-width: 330px;

            margin: 20px auto 0;

            color: #64748b;

            font-size: 15px;
            line-height: 1.6;

            text-align: center;

            display: block;
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
            color: rgba(255,255,255,.72);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .13em;
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

        /* =========================
           RIGHT LOGIN
        ========================== */

        .login-panel {
            min-height: 100vh;

            display: flex;
            flex-direction: column;
            justify-content: center;

            background:
                radial-gradient(
                    circle at 92% 10%,
                    rgba(43, 144, 154, .10) 0%,
                    rgba(43, 144, 154, 0) 32%
                ),
                radial-gradient(
                    circle at 8% 88%,
                    rgba(43, 144, 154, .055) 0%,
                    rgba(43, 144, 154, 0) 30%
                ),
                linear-gradient(180deg, #ffffff 0%, #fbfdfd 100%);

            padding: 60px clamp(45px, 7vw, 120px);

            color: #0f172a;

            position: relative;
            overflow: hidden;

            border-left: 1px solid #e6eef0;
        }

        /* couche décorative */
        .login-shapes {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }


        /* garde le contenu au-dessus */
        .login-container,
        .right-footer {
            position: relative;
            z-index: 2;
        }


        .shape-orbit {
            position: absolute;

            width: 420px;
            height: 420px;

            right: -210px;
            top: 40px;

            border-radius: 50%;

            border: 1px solid rgba(43,144,154,.28);

            box-shadow:
                0 0 80px rgba(43,144,154,.12);

            transform: rotate(-20deg);
        }

        .shape-orbit::before {

            content:"";

            position:absolute;

            inset:45px;

            border-radius:50%;

            border:1px solid rgba(43,144,154,.11);
        }


        /* Hexagone technologie */
        .shape-hexagon {

            position:absolute;

            width:160px;
            height:160px;

            left:-60px;
            top:170px;


            background:
                linear-gradient(
                    145deg,
                    rgba(43,144,154,.11),
                    rgba(255,255,255,.08)
                );


            clip-path: polygon(
                25% 6%,
                75% 6%,
                100% 50%,
                75% 94%,
                25% 94%,
                0 50%
            );


            border:1px solid rgba(43,144,154,.25);
        }


        /* Bloc glass incliné */

        .shape-glass {

            position:absolute;

            width:110px;
            height:110px;

            right:130px;
            bottom:120px;


            border-radius:26px;


            background:
                rgba(255,255,255,.68);


            border:
                1px solid rgba(43,144,154,.14);


            backdrop-filter:blur(12px);


            transform:
                rotate(35deg);


            box-shadow:
                0 24px 60px rgba(15,23,42,.08);
        }



        /* Halo lumineux */

        .shape-glow {

            position:absolute;

            width:500px;
            height:500px;


            left:50%;
            top:50%;


            transform:
                translate(-50%,-50%);


            background:
                radial-gradient(
                    circle,
                    rgba(43,144,154,.10),
                    transparent 65%
                );


            filter:blur(30px);
        }


        /* grand anneau */
        .shape-ring {
            position: absolute;

            width: 220px;
            height: 220px;

            left: -120px;
            bottom: 140px;

            border-radius: 50%;

            border: 1px solid rgba(43,144,154,.22);
        }


        /* carré élégant */
        .shape-square {
            position: absolute;

            width: 90px;
            height: 90px;

            right: 90px;
            bottom: 160px;

            border-radius: 22px;

            border: 1px solid rgba(43,144,154,.13);

            transform: rotate(35deg);

            background:
                linear-gradient(
                    135deg,
                    rgba(43,144,154,.09),
                    rgba(255,255,255,.24)
                );
        }


        /* petits points */
        .shape-dot {
            position: absolute;

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #2B909A;

            box-shadow:
                0 0 18px rgba(43,144,154,.8);
        }


        .shape-dot-1 {
            top: 160px;
            right: 250px;
        }


        .shape-dot-2 {
            bottom: 250px;
            left: 120px;
        }

        .login-container {
            width: min(430px, 100%);
            margin: auto;
        }

        .mobile-logo {
            display: none;
        }

        .mobile-value-proposition {
            display: none;
        }


        .login-heading {
            margin: 0;

            color: #0f172a;

            font-size: 36px;
            line-height: 1.1;
            letter-spacing: -.045em;
            font-weight: 800;
        }

        .login-subtitle {
            margin: 13px 0 38px;

            color: #64748b;

            font-size: 14px;
            line-height: 1.65;
        }

        .form-label {
            color: #334155;

            font-size: 12px;
            font-weight: 650;
        }

        .forgot-link {
            color: #64748b;

            font-size: 11px;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--brand-dark);
        }

        .form-group {
            margin-bottom: 14px;
        }

        .input-wrap {
            position: relative;
        }

        .form-input {
            width: 100%;
            height: 52px;

            padding: 0 46px 0 16px;

            border: 1px solid #d7e1e4;

            border-radius: 8px;

            color: #0f172a;

            background: rgba(255,255,255,.88);

            font-size: 14px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            border-color: #2B909A;

            box-shadow:
                0 0 0 4px rgba(43,144,154,.13);

            background: #ffffff;
        }

        .login-panel .password-toggle {
            color: #64748b;
        }

        .login-panel .password-toggle:hover {
            color: var(--brand-dark);
            background: rgba(43,144,154,.09);
        }

        .form-input.no-icon {
            padding-right: 16px;
        }

        .form-alert {
            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 24px;
            padding: 14px 16px;

            border-radius: 12px;

            color: #b91c1c;

            background:
                #fef2f2;

            border:
                1px solid #fecaca;

            font-size: 13px;
            font-weight: 500;

            line-height: 1.5;

            animation: alertFade .25s ease-out;
        }

        .form-alert svg {
            width: 20px;
            height: 20px;

            flex-shrink: 0;

            color: #ef4444;
        }


        @keyframes alertFade {

            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

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
            margin-top: 18px;
            margin-bottom: 34px;

            padding-top: 4px;

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .remember-row input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;

            width: 17px;
            height: 17px;
            flex: 0 0 17px;

            margin: 0;

            border: 1.5px solid #94a3b8;
            border-radius: 3px;

            background: #ffffff;

            cursor: pointer;

            display: grid;
            place-content: center;

            transition:
                background-color .2s ease,
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .remember-row input[type="checkbox"]:checked {
            background: #2B909A;
            border-color: #2B909A;

            box-shadow:
                0 0 0 3px rgba(43,144,154,.12);
        }

        .remember-row input[type="checkbox"]::before {
            content: "";

            width: 8px;
            height: 4px;

            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;

            transform:
                rotate(-45deg)
                scale(0);

            transition: transform .15s ease;
        }

        .remember-row input[type="checkbox"]:checked::before {
            transform:
                rotate(-45deg)
                scale(1);
        }

        .remember-row label {
            color: #475569;

            font-size: 12px;
            font-weight: 500;
            line-height: 1.4;

            cursor: pointer;
            user-select: none;
        }

        .login-button {
            display: block;

            width: min(280px, 100%);
            height: 54px;

            margin: 0 auto;

            border: none;
            border-radius: 999px;

            background: #2B909A;
            color: #ffffff;

            font-size: 14px;
            font-weight: 700;
            letter-spacing: .01em;

            cursor: pointer;

            box-shadow:
                0 12px 28px rgba(43, 144, 154, .20);

            transform: scale(1);

            transition:
                transform .22s ease,
                background-color .22s ease,
                box-shadow .22s ease;
        }

        .login-button:hover {
            background: #257f88;
            transform: scale(1.045);

            box-shadow:
                0 16px 34px rgba(43, 144, 154, .30);
        }

        .login-button:active {
            transform: scale(.985);
        }

        .login-button:focus-visible {
            outline: 3px solid rgba(43, 144, 154, .30);
            outline-offset: 4px;
        }

        .right-footer {
            width: min(430px, 100%);
            margin: 42px auto 0;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;

            color: #94a3b8;

            font-size: 10px;
            font-weight: 500;
        }

        .right-footer span:first-child {
            color: #64748b;
            font-weight: 600;
        }

        .right-footer span:last-child {
            color: #94a3b8;
            text-align: right;
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
                font-size: clamp(38px, 5vw, 50px);
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
                min-height: 100svh;
                min-height: 100dvh;
                padding: clamp(24px, 5vw, 38px) clamp(20px, 6vw, 42px);
            }

            .login-container {
                width: min(430px, 100%);
            }

            .mobile-logo {
                display: block;
                height: clamp(48px, 8vw, 60px);
                width: auto;
                margin: 0 auto 24px;
            }

            .mobile-value-proposition {
                display: block;
                min-height: 82px;
                margin: 0 auto 28px;
                text-align: center;
            }

            .mobile-value-kicker {
                margin: 0 0 9px;
                color: var(--brand-dark);
                font-size: 9px;
                font-weight: 800;
                letter-spacing: .16em;
                text-transform: uppercase;
            }

            .mobile-typewriter-title {
                margin: 0;
                color: #0f172a;
                font-size: clamp(22px, 5.2vw, 29px);
                font-weight: 800;
                letter-spacing: -.035em;
                line-height: 1.08;
            }

            .mobile-typewriter-title .typewriter-line--accent {
                color: var(--brand);
            }


            .login-heading {
                font-size: 31px;
            }

            .right-footer {
                margin-top: 42px;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 24px 18px;
            }

            .mobile-logo {
                height: 48px;
                margin-bottom: 20px;
            }

            .login-heading {
                font-size: 28px;
            }

            .login-subtitle {
                margin-bottom: 30px;
            }

            .right-footer {
                margin-top: 36px;
                flex-direction: row;
                justify-content: space-between;
                gap: 15px;
                font-size: 9px;
            }
        }

        @media (min-width: 821px) {
            .login-shell,
            .login-visual,
            .login-panel {
                min-height: 100svh;
                min-height: 100dvh;
            }

            .brand-logo {
                height: clamp(70px, 9vh, 100px);
            }

            .visual-title {
                font-size: clamp(20px, min(2.6vw, 6vh), 58px);
                font-weight: 700;
                line-height: 1.02;
                letter-spacing: -.045em;
            }

            .visual-description {
                margin-top: clamp(16px, 2.4vh, 26px);
                font-size: clamp(13px, 1.5vh, 16px);
            }

            .login-3d-zone {
                height: clamp(250px, 38vh, 420px);
                margin-top: clamp(18px, 3vh, 34px);
            }
        }

        @media (min-width: 821px) and (max-height: 760px) {
            .login-visual {
                padding-top: 24px;
                padding-bottom: 24px;
            }

            .visual-kicker {
                margin-bottom: 12px;
            }

            .visual-description {
                line-height: 1.48;
            }

            .login-3d-zone {
                height: clamp(210px, 32vh, 255px);
            }

            .login-panel {
                padding-top: 30px;
                padding-bottom: 30px;
            }

            .login-welcome {
                margin-bottom: 26px;
            }

            .login-favicon {
                width: 72px;
                height: 72px;
                margin-bottom: 20px;
            }

            .right-footer {
                margin-top: 28px;
            }
        }

        @media (max-width: 820px) and (max-height: 700px) {
            .login-panel {
                padding-top: 18px;
                padding-bottom: 18px;
            }

            .mobile-logo {
                height: 42px;
                margin-bottom: 14px;
            }

            .mobile-value-proposition {
                min-height: 68px;
                margin-bottom: 18px;
            }


            .login-welcome {
                margin-bottom: 24px;
            }

            .login-favicon {
                width: 64px;
                height: 64px;
                margin-bottom: 14px;
            }

            .login-heading {
                font-size: 26px;
            }

            .right-footer {
                margin-top: 28px;
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

            .typewriter-cursor {
                display: none;
            }
        }
        html.solutcloud-preloader-active body.is-preloading {
            overflow: hidden;
        }

        .solutcloud-preloader {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.96);
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 260ms ease, visibility 260ms ease;
        }

        html.solutcloud-preloader-active .solutcloud-preloader {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            animation: solutcloud-preloader-fallback 260ms ease 2s forwards;
        }

        .solutcloud-preloader.is-leaving {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .solutcloud-preloader-stage {
            position: relative;
            display: grid;
            width: clamp(154px, 19vw, 184px);
            aspect-ratio: 1;
            place-items: center;
        }

        .solutcloud-preloader-ring {
            position: absolute;
            inset: 0;
            border: 5px solid #dfecee;
            border-top-color: var(--brand);
            border-radius: 50%;
            box-shadow: 0 18px 44px rgba(25, 83, 89, 0.12);
            animation: solutcloud-preloader-spin 760ms linear infinite;
        }

        .solutcloud-preloader-logo {
            position: relative;
            z-index: 1;
            display: block;
            width: 72%;
            height: auto;
            object-fit: contain;
        }

        .solutcloud-preloader-label {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @keyframes solutcloud-preloader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes solutcloud-preloader-fallback {
            to {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .solutcloud-preloader-ring {
                animation-duration: 1.5s !important;
            }
        }
    </style>
</head>

<body class="is-preloading">

<div class="solutcloud-preloader" id="solutcloud-preloader" role="status" aria-live="polite" aria-label="Chargement de SOLUTCLOUD">
    <div class="solutcloud-preloader-stage">
        <span class="solutcloud-preloader-ring" aria-hidden="true"></span>
        <img class="solutcloud-preloader-logo" src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}" alt="SOLUTCLOUD" decoding="async" loading="eager" fetchpriority="high">
        <span class="solutcloud-preloader-label">Chargement de SOLUTCLOUD</span>
    </div>
</div>

<div class="login-shell">

    <!-- ================================
         PARTIE GAUCHE
    ================================= -->
    <section class="login-visual">

        <header class="visual-header">

            <div class="logo-wrap">
                <img
                    src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}"
                    alt="SOLUTCLOUD"
                    class="brand-logo"
                >
            </div>

            <div class="environment-badge">
                ERP · CRM · CLOUD
            </div>

        </header>

        <div class="visual-stage">

            <div class="visual-kicker">
                ESPACE COMPTE
            </div>

            <h1 class="visual-title" aria-label="Pilotez votre activité, sans complexité.">
                <span data-typewriter aria-hidden="true">
                    <span class="typewriter-line" data-typewriter-primary>Pilotez votre activité,</span>
                    <span class="typewriter-line typewriter-line--accent" data-typewriter-secondary>Sans complexité.</span>
                    <span class="typewriter-cursor" data-typewriter-cursor></span>
                </span>
            </h1>

            <p class="visual-description">
                Accédez à votre environnement SOLUTCLOUD et retrouvez vos outils de gestion,
                vos données et votre activité dans un espace sécurisé et centralisé.
            </p>

        <div class="login-3d-zone">

        <div
                id="login-erp-crm-3d"
                class="login-3d-canvas"
                aria-label="Écosystème ERP CRM SOLUTCLOUD"
            ></div>

            <div class="login-3d-caption">

                <span class="login-3d-status">
                    ÉCOSYSTÈME CONNECTÉ
                </span>

                <span class="login-3d-hint">
                    Déplacez la souris pour explorer
                </span>

            </div>

        </div>

    </section>


    <!-- ================================
         PARTIE DROITE
    ================================= -->
    <main class="login-panel">

        <!-- Décoration géométrique premium -->
        <div class="login-shapes" aria-hidden="true">
            <span class="shape-ring"></span>
            <span class="shape-square"></span>
            <span class="shape-orbit"></span>
            <span class="shape-hexagon"></span>
            <span class="shape-glass"></span>
            <span class="shape-glow"></span>
        </div>

        <div class="login-container">

            <!-- Logo mobile -->
            <img
                src="{{ asset('img/LOGO_SOLUTCLOUD_Sans_fond.png') }}"
                alt="SOLUTCLOUD"
                class="mobile-logo"
            >

            <div class="mobile-value-proposition">
                <p class="mobile-value-kicker">Votre activité, mieux pilotée</p>
                <p class="mobile-typewriter-title" aria-label="Pilotez votre activité, sans complexité.">
                    <span data-typewriter aria-hidden="true">
                        <span class="typewriter-line" data-typewriter-primary>Pilotez votre activité,</span>
                        <span class="typewriter-line typewriter-line--accent" data-typewriter-secondary>Sans complexité.</span>
                        <span class="typewriter-cursor" data-typewriter-cursor></span>
                    </span>
                </p>
            </div>

            <div class="login-welcome">

                <img
                    src="{{ asset('img/favicon.png') }}"
                    alt="SOLUTCLOUD"
                    class="login-favicon"
                >

                <h2 class="login-heading">
                    Bienvenue dans<br>
                    l'espace compte
                </h2>

            </div>

            @if ($errors->any())
                <div class="form-alert" role="alert">

                    <svg 
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v5"/>
                        <path d="M12 16h.01"/>
                    </svg>

                    <span>
                        L’adresse e-mail ou le mot de passe est incorrect.
                    </span>

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
                            class="form-input no-icon"
                            placeholder="nom@entreprise.com"
                        >
                    </div>

                </div>


                <!-- PASSWORD -->
            <div
                class="form-group"
                x-data="{ showPassword: false }"
            >
                <div class="label-row">
                    <label for="password" class="form-label">
                        Mot de passe
                    </label>
                </div>

                <div class="input-wrap">

                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="form-input"
                        placeholder="Votre mot de passe"
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        @click="showPassword = !showPassword"
                        :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                    >

                        <!-- Œil ouvert : mot de passe caché -->
                        <svg
                            x-show="!showPassword"
                            x-cloak
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>

                        <!-- Œil barré : mot de passe visible -->
                        <svg
                            x-show="showPassword"
                            x-cloak
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M3 3l18 18"/>
                            <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"/>
                            <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6 0 9.5 7 9.5 7a15.5 15.5 0 0 1-2.3 3.2"/>
                            <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 7 9.5 7a10 10 0 0 0 4.1-.9"/>
                        </svg>

                    </button>

                </div>
            </div>


                <!-- REMEMBER -->
                <div class="remember-row">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
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

        <footer class="right-footer">
            <a 
                href="https://solutcloud.com"
                rel="noopener noreferrer"
            >
                SOLUTCLOUD
            </a>

            <span>
                © 2026 I-SOLUTIONS CI
            </span>
        </footer>

    </main>

</div>

<script>
    (() => {
        const phrases = [
            ['Pilotez votre activité,', 'Sans complexité.'],
            ['Centralisez vos opérations,', 'En toute simplicité.'],
            ['Accélérez votre croissance,', 'Avec une vision claire.'],
        ];
        const instances = [...document.querySelectorAll('[data-typewriter]')];

        if (!instances.length) return;

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const render = (primary, secondary, cursorTarget = 'secondary') => {
            for (const instance of instances) {
                const primaryNode = instance.querySelector('[data-typewriter-primary]');
                const secondaryNode = instance.querySelector('[data-typewriter-secondary]');
                const cursor = instance.querySelector('[data-typewriter-cursor]');
                primaryNode.textContent = primary;
                secondaryNode.textContent = secondary;
                (cursorTarget === 'primary' ? primaryNode : secondaryNode).append(cursor);
            }
        };

        if (reducedMotion) {
            render(...phrases[0]);
            return;
        }

        let phraseIndex = 0;
        let primaryLength = phrases[0][0].length;
        let secondaryLength = phrases[0][1].length;
        let phase = 'erase-secondary';

        const schedule = (delay) => window.setTimeout(step, delay);
        const step = () => {
            const [primary, secondary] = phrases[phraseIndex];

            if (phase === 'hold') {
                phase = 'erase-secondary';
                schedule(3000);
                return;
            }

            if (phase === 'erase-secondary') {
                secondaryLength -= 1;
                render(primary.slice(0, primaryLength), secondary.slice(0, Math.max(0, secondaryLength)));
                if (secondaryLength <= 0) phase = 'erase-primary';
                schedule(22);
                return;
            }

            if (phase === 'erase-primary') {
                primaryLength -= 1;
                render(primary.slice(0, Math.max(0, primaryLength)), '', 'primary');
                if (primaryLength <= 0) {
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    primaryLength = 0;
                    secondaryLength = 0;
                    phase = 'type-primary';
                    schedule(180);
                    return;
                }
                schedule(18);
                return;
            }

            const [nextPrimary, nextSecondary] = phrases[phraseIndex];

            if (phase === 'type-primary') {
                primaryLength += 1;
                render(nextPrimary.slice(0, primaryLength), '', 'primary');
                if (primaryLength >= nextPrimary.length) phase = 'type-secondary';
                schedule(42);
                return;
            }

            secondaryLength += 1;
            render(nextPrimary, nextSecondary.slice(0, secondaryLength));
            if (secondaryLength >= nextSecondary.length) phase = 'hold';
            schedule(42);
        };

        render(...phrases[0]);
        schedule(3000);
    })();

    (() => {
        const root = document.documentElement;
        const loader = document.getElementById('solutcloud-preloader');

        if (!loader) {
            root.classList.remove('solutcloud-preloader-active');
            document.body.classList.remove('is-preloading');
            return;
        }

        if (!window.solutcloudLoginPreloaderShouldShow) {
            document.body.classList.remove('is-preloading');
            loader.remove();
            return;
        }

        window.setTimeout(() => {
            loader.classList.add('is-leaving');
            root.classList.remove('solutcloud-preloader-active');
            document.body.classList.remove('is-preloading');

            window.setTimeout(() => loader.remove(), 320);
        }, 2000);
    })();
</script>

</body>
</html>
