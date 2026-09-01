@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre instance est opérationnelle',
    'preheader' => 'Votre environnement SOLUTCLOUD et les accès ERP de votre équipe sont disponibles.',
    'emailCategory' => 'Mise en service',
    'emailBadge' => 'Instance active',
    'emailIntro' => 'La configuration de votre espace est terminée. Votre équipe peut maintenant commencer à travailler.',
])

@section('content')
    <p>Bonjour,</p>
    <p>L’instance dédiée à <strong>{{ $company->name }}</strong> a été créée, vérifiée et mise en ligne. Conservez les informations suivantes dans un emplacement sécurisé.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label email-detail-last">Adresse de connexion</td><td class="email-detail-value email-detail-last"><a href="{{ $url }}" style="color:#2b909a;text-decoration:none">{{ $url }}</a></td></tr>
    </table>

    <div style="margin:28px 0 12px;color:#202124;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.4">Accès ERP</div>

    @foreach($credentials as $credential)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel" style="margin-top:{{ $loop->first ? '0' : '14px' }}">
            <tr><td colspan="2" style="padding:13px 16px;background:#f8f9fa;color:#237781;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase">{{ $credential['label'] }}</td></tr>
            <tr><td class="email-detail-label">Identifiant</td><td class="email-detail-value"><span class="email-code">{{ $credential['login'] }}</span></td></tr>
            <tr><td class="email-detail-label email-detail-last">Mot de passe initial</td><td class="email-detail-value email-detail-last"><span class="email-code">{{ $credential['password'] }}</span></td></tr>
        </table>
    @endforeach

    @if($company->package === 'premium')
        <p>L’accès super administrateur permet de configurer librement l’ERP et de créer les utilisateurs nécessaires à votre organisation.</p>
    @else
        <p>Les comptes inclus correspondent au nombre d’utilisateurs prévu dans votre offre SOLUTCLOUD.</p>
    @endif
@endsection

@section('notice')
    Modifiez chaque mot de passe initial après la première connexion. Notre équipe ne vous demandera jamais de communiquer ces mots de passe par e-mail.
@endsection
