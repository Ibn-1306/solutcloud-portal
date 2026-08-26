@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre instance est en cours de préparation',
    'preheader' => 'Notre équipe prépare maintenant votre environnement SOLUTCLOUD.',
    'emailCategory' => 'Installation',
    'emailBadge' => 'En préparation',
])

@section('content')
    <p>Bonjour,</p>
    <p>La création de l’environnement dédié à <strong>{{ $company->name }}</strong> est prise en charge par notre équipe.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">SOLUTCLOUD {{ strtoupper($company->package) }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">État</td><td class="email-detail-value email-detail-last">Installation en cours</td></tr>
    </table>

    <p>Cette opération est réalisée et vérifiée manuellement. Vous recevrez un dernier e-mail contenant l’adresse et les accès ERP dès que l’installation sera terminée.</p>
@endsection

@section('notice')
    Aucune action technique n’est requise de votre part pendant l’installation.
@endsection
