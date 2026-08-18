<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Votre demande SOLUTCLOUD</title>
</head>
<body style="margin:0;background:#f3f6f7;font-family:Arial,sans-serif;color:#172126">
    <div style="max-width:600px;margin:0 auto;padding:32px 16px">
        <div style="background:#ffffff;border-radius:12px;padding:32px;border-top:5px solid #2b909a">
            <h1 style="margin:0 0 18px;font-size:24px">Bonjour {{ $lead->fullname }},</h1>
            <p style="margin:0 0 16px;line-height:1.7">
                @if($lead->type === 'trial')
                    Votre demande d’essai gratuit a bien été reçue.
                @elseif($lead->type === 'quote')
                    Votre demande de devis a bien été reçue.
                @else
                    Votre message a bien été transmis à notre équipe.
                @endif
            </p>
            <p style="margin:0 0 22px;line-height:1.7">Un conseiller SOLUTCLOUD vous répondra dans les meilleurs délais.</p>
            <a href="https://solutcloud.com" style="display:inline-block;padding:12px 22px;border-radius:7px;background:#2b909a;color:#ffffff;text-decoration:none;font-weight:bold">Visiter solutcloud.com</a>
            <p style="margin:24px 0 0;color:#66747c;font-size:12px">Ce message automatique confirme uniquement la réception de votre demande.</p>
        </div>
    </div>
</body>
</html>
