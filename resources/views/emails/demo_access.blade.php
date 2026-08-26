@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre démonstration est prête',
    'preheader' => 'Vos accès à l’environnement de démonstration SOLUTCLOUD sont disponibles.',
    'emailCategory' => 'Démonstration',
    'emailBadge' => 'Accès activé',
    'emailIntro' => 'Votre espace d’évaluation est configuré. Vous pouvez dès maintenant découvrir les fonctionnalités de SOLUTCLOUD.',
])

@section('content')
    <p>Bonjour,</p>
    <p>L’environnement de démonstration demandé pour <strong>SOLUTCLOUD</strong> est maintenant accessible avec les informations ci-dessous.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Adresse</td><td class="email-detail-value"><a href="{{ $demo->url }}" style="color:#2b909a;text-decoration:none">{{ $demo->url }}</a></td></tr>
        <tr><td class="email-detail-label">Identifiant</td><td class="email-detail-value"><span class="email-code">{{ $demo->erp_login }}</span></td></tr>
        <tr><td class="email-detail-label email-detail-last">Mot de passe</td><td class="email-detail-value email-detail-last"><span class="email-code">{{ $demo->erp_password }}</span></td></tr>
    </table>

    <p>Nous vous recommandons de tester les principaux parcours de votre activité afin d’évaluer concrètement l’organisation, le suivi et la centralisation proposés par la plateforme.</p>
@endsection

@section('notice')
    Ces identifiants sont confidentiels et l’accès est temporaire. Ne transférez pas ce message à une personne non autorisée.
@endsection
