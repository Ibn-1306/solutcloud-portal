<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6; margin: 0; padding: 0; }
        .wrapper { background-color: #f8fafc; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background-color: #2B909A; padding: 30px; text-align: center; color: white; }
        .favicon { width: 48px; height: 48px; margin-bottom: 15px; border-radius: 8px; }
        .content { padding: 40px; }
        .access-card { background-color: #f1f5f9; border-radius: 12px; padding: 25px; margin: 25px 0; border: 1px dashed #cbd5e1; }
        .button { display: inline-block; padding: 16px 32px; background-color: #2B909A; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .footer { padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; background: #f8fafc; }
        .status-badge { display: inline-block; background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 99px; font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 15px; }
        h1 { margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        h2 { color: #0f172a; font-size: 20px; margin-top: 0; }
        code { background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-family: monospace; color: #e11d48; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <!-- Utilisation de l'URL absolue du favicon pour l'affichage email -->
                <img src="https://www.solutcloud.com/img/favicon.png" alt="SOLUTCLOUD" class="favicon">
                <h1>SOLUTCLOUD</h1>
            </div>
            <div class="content">
                <div class="status-badge">Instance Opérationnelle</div>
                <h2>Félicitations, {{ $company->name }} !</h2>
                <p>Votre infrastructure <strong>ERP/CRM Dolibarr</strong> a été déployée avec succès.</p>
                
                <p>Voici vos accès pour piloter votre activité :</p>

                <div class="access-card">
                    <p style="margin: 0 0 12px 0;"><strong>🌐 Adresse de l'instance :</strong><br>
                       <a href="{{ $url }}" style="color: #2B909A; font-weight: bold;">{{ $url }}</a></p>
                    <p style="margin: 0 0 12px 0;"><strong>👤 Identifiant :</strong> <code>{{ $login }}</code></p>
                    <p style="margin: 0;"><strong>🔑 Mot de passe :</strong> <code>{{ $password }}</code></p>
                </div>

                <div style="text-align: center; margin-top: 35px;">
                    <a href="{{ $url }}" class="button">Lancer mon Logiciel</a>
                </div>
            </div>
            <div class="footer">
                &copy; 2026 <strong>I-SOLUTIONS CI</strong> | Abidjan, Côte d'Ivoire<br>
                Pour toute assistance : sales@i-solutions.ci
            </div>
        </div>
    </div>
</body>
</html>