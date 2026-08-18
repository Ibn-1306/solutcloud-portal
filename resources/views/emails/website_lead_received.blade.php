<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Nouvelle demande SOLUTCLOUD</title>
</head>
<body style="margin:0;background:#f3f6f7;font-family:Arial,sans-serif;color:#172126">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px">
        <div style="background:#ffffff;border-radius:12px;padding:28px;border-top:5px solid #2b909a">
            <h1 style="margin:0 0 22px;font-size:22px">Nouvelle demande depuis solutcloud.com</h1>
            <table role="presentation" style="width:100%;border-collapse:collapse">
                <tr><td style="padding:7px 0;font-weight:bold">Type</td><td style="padding:7px 0">{{ strtoupper($lead->type) }}</td></tr>
                <tr><td style="padding:7px 0;font-weight:bold">Nom</td><td style="padding:7px 0">{{ $lead->fullname }}</td></tr>
                <tr><td style="padding:7px 0;font-weight:bold">E-mail</td><td style="padding:7px 0"><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td></tr>
                <tr><td style="padding:7px 0;font-weight:bold">Téléphone</td><td style="padding:7px 0">{{ $lead->phone ?: 'Non renseigné' }}</td></tr>
                <tr><td style="padding:7px 0;font-weight:bold">Entreprise</td><td style="padding:7px 0">{{ $lead->company_name ?: 'Non renseignée' }}</td></tr>
                <tr><td style="padding:7px 0;font-weight:bold">Profil</td><td style="padding:7px 0">{{ $lead->profile ?: 'Non renseigné' }}</td></tr>
            </table>

            @if($lead->message)
                <div style="margin-top:22px;padding:18px;background:#f6fafb;border-radius:8px;white-space:pre-line">{{ $lead->message }}</div>
            @endif

            <p style="margin:24px 0 0;color:#66747c;font-size:13px">Répondez directement à cet e-mail pour contacter {{ $lead->fullname }}.</p>
        </div>
    </div>
</body>
</html>
