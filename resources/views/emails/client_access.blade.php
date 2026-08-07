<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background-color: #ffffff; border: 1px solid #e2e8f0; }
        .header { background-color: #2B909A; padding: 40px 20px; text-align: center; color: #ffffff; }
        .logo-mail { width: 64px; height: 64px; margin-bottom: 15px; border-radius: 12px; background: #ffffff; padding: 8px; }
        .header h1 { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; }
        .content { padding: 40px 30px; }
        .content h2 { color: #2B909A; margin-top: 0; font-size: 22px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }
        .status-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 30px 0; }
        .order-details { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; font-size: 14px; }
        .footer { background-color: #f8fafc; padding: 30px; text-align: center; font-size: 13px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .highlight { color: #2B909A; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://solutcloud.com/img/favicon.png" alt="SolutCloud Logo" class="logo-mail">
            <h1>SOLUTCLOUD</h1>
        </div>
        <div class="content">
            <h2>Merci pour votre confiance !</h2>
            <p>Bonjour <span class="highlight">{{ $order->customer_name }}</span>,</p>
            <p>Nous vous confirmons que votre paiement pour l'offre <span class="highlight">SOLUTCLOUD {{ $order->plan }}</span> a été reçu avec succès.</p>
            
            <div class="status-box">
                <p style="margin: 0; color: #92400e; font-weight: bold;">⚙️ Statut : Préparation de votre instance en cours</p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #92400e;">Un administrateur procède actuellement à la configuration manuelle de votre environnement ERP sur nos serveurs. Cette opération prend généralement entre 30 minutes et 2 heures.</p>
            </div>

            <p>Dès que votre instance sera prête, vous recevrez un <strong>second email</strong> contenant vos identifiants sécurisés et votre lien d'accès définitif.</p>
            
            <div class="order-details">
                <p style="margin-top:0;"><strong>Récapitulatif de commande :</strong></p>
                <p style="margin: 5px 0;">Entreprise : {{ $order->company_name }}</p>
                <p style="margin: 5px 0;">Montant payé : {{ number_format($order->amount, 0, ',', ' ') }} FCFA</p>
                <p style="margin: 5px 0;">Transaction ID : {{ $order->transaction_id }}</p>
            </div>

            <p style="margin-top: 30px;">Merci d'avoir choisi <span class="highlight">SOLUTCLOUD</span> pour piloter la croissance de votre entreprise.</p>
        </div>
        <div class="footer">
            <p><strong>I-SOLUTIONS - Ingénierie Informatique</strong><br>
            Yopougon Ananeraie, 21 BP 4069 Abidjan 21, Côte d'Ivoire.</p>
        </div>
    </div>
</body>
</html>