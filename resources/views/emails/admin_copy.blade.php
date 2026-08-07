<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #f8fafc; padding: 20px; }
        .card { max-width: 600px; margin: auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden; }
        .header { background: #2B909A; padding: 20px; text-align: center; font-weight: bold; font-size: 18px; letter-spacing: 1px; color: white; }
        .content { padding: 30px; }
        .badge { background: #10b981; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; display: inline-block; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { padding: 12px 8px; border-bottom: 1px solid #334155; }
        .label { color: #94a3b8; width: 150px; font-size: 13px; text-transform: uppercase; }
        .value { color: #ffffff; font-weight: 600; }
        .action-box { margin-top: 30px; padding: 20px; background: #0f172a; border-radius: 8px; border: 1px solid #2B909A; text-align: center; }
        .action-box p { margin: 0 0 15px 0; font-size: 14px; }
        .btn-admin { display: inline-block; padding: 10px 20px; background: #2B909A; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">NOUVELLE COMMANDE À TRAITER</div>
        <div class="content">
            <div class="badge">PAIEMENT REÇU - À INSTALLER</div>
            
            <table class="data-table">
                <tr>
                    <td class="label">Entreprise :</td>
                    <td class="value">{{ $order->company_name }}</td>
                </tr>
                <tr>
                    <td class="label">Responsable :</td>
                    <td class="value">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email :</td>
                    <td class="value">{{ $order->customer_email }}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone :</td>
                    <td class="value">{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <td class="label">Offre :</td>
                    <td class="value" style="color: #2B909A;">SOLUTCLOUD {{ $order->plan }}</td>
                </tr>
                <tr>
                    <td class="label">Montant :</td>
                    <td class="value">{{ number_format($order->amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>

            <div class="action-box">
                <p>🚀 Connectez-vous à LWS pour créer l'instance :</p>
                <!-- Ici on génère l'URL prévisionnelle pour t'aider -->
                <p style="color: #2B909A; font-family: monospace;">
                    {{ Str::slug($order->company_name) }}.solutcloud.com
                </p>
                <a href="https://login.solutcloud.com/admin" class="btn-admin">ACCÉDER AU DASHBOARD ADMIN</a>
            </div>
        </div>
    </div>
</body>
</html>