<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; border: 1px solid #eef2f6; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #2B909A; padding: 40px 20px; text-align: center; color: white; }
        .logo-mail { width: 60px; height: 60px; margin-bottom: 15px; border-radius: 10px; background: white; padding: 5px; }
        .content { padding: 30px; background-color: #ffffff; }
        .access-box { background-color: #f8fafc; border: 1px dashed #2B909A; padding: 25px; border-radius: 8px; margin: 25px 0; }
        .note-box { background-color: #fff9eb; padding: 15px; border-left: 4px solid #f59e0b; font-size: 14px; margin: 20px 0; }
        .footer { background-color: #f1f5f9; padding: 25px; text-align: center; font-size: 12px; color: #64748b; }
        .btn { display: inline-block; padding: 14px 30px; background-color: #2B909A; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; }
        strong { color: #2B909A; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Remplacer par l'URL réelle de ton image une fois en ligne -->
            <img src="https://solutcloud.com/img/favicon.png" alt="Logo" class="logo-mail">
            <h1 style="margin:0; font-size: 24px; letter-spacing: 2px;">SOLUTCLOUD</h1>
        </div>
        <div class="content">
            <h2 style="margin-top:0;">Bienvenue chez SOLUTCLOUD</h2>
            <p>Bonjour <strong>{{ $name }}</strong>,</p>
            <p>Nous avons le plaisir de vous informer que votre instance de gestion est prête et opérationnelle.</p>
            
            <p>Voici vos accès pour vous connecter à votre <strong>portail client centralisé</strong> :</p>
            
            <div class="access-box">
                <p style="margin:0;"><strong>Lien :</strong> <a href="https://solutcloud.com" style="color:#2B909A;">https://solutcloud.com</a></p>
                <p style="margin:10px 0;"><strong>Identifiant :</strong> <span style="font-family: monospace; background: #eee; padding: 2px 5px;">{{ $email }}</span></p>
                <p style="margin:0;"><strong>Mot de passe :</strong> <span style="font-family: monospace; background: #eee; padding: 2px 5px;">{{ $password }}</span></p>
            </div>

            <p>Accès direct à votre instance ERP :<br>
            <a href="https://{{ $url }}">Accéder à mon instance</a>

            <div class="note-box">
                ⚠️ <strong>Note importante :</strong> Vous recevrez un second mail ou un message Whatsapp contenant vos accès spécifiques à l'ERP.
            </div>

            <p>Merci d'avoir choisi <strong>SOLUTCLOUD</strong> pour votre gestion !</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="https://solutcloud.com" class="btn">Accéder à mon espace</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>I-SOLUTIONS</strong><br>
            Ingénierie informatique<br>
            Yopougon Ananeraie, 21 BP 4069 Abidjan 21, Côte d'Ivoire.</p>
        </div>
    </div>
</body>
</html>