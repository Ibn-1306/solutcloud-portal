@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre instance est opérationnelle',
    'preheader' => 'Votre environnement SOLUTCLOUD est prêt à être utilisé.',
    'emailCategory' => 'Mise en service',
    'emailBadge' => 'Instance active',
    'emailIntro' => 'La configuration de votre espace est terminée. Votre équipe peut maintenant commencer à travailler.',
])

@section('content')
    <p>Bonjour,</p>
    <p>L’instance dédiée à <strong>{{ $company->name }}</strong> a été créée, vérifiée et mise en ligne. Conservez les informations d’accès suivantes dans un emplacement sécurisé.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Adresse</td><td class="email-detail-value"><a href="{{ $url }}" style="color:#176f77;text-decoration:none">{{ $url }}</a></td></tr>
        <tr><td class="email-detail-label">Identifiant</td><td class="email-detail-value"><span class="email-code">{{ $login }}</span></td></tr>
        <tr><td class="email-detail-label email-detail-last">Mot de passe initial</td><td class="email-detail-value email-detail-last"><span class="email-code">{{ $password }}</span></td></tr>
    </table>

    <p>Lors de votre première connexion, vérifiez les informations de votre organisation et créez les comptes des collaborateurs autorisés.</p>
@endsection

@section('action')
    <a href="{{ $url }}" class="email-button">Ouvrir mon instance</a>
@endsection

@section('notice')
    Modifiez le mot de passe initial après votre première connexion. Notre équipe ne vous demandera jamais de le communiquer par e-mail.
@endsection
