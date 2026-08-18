@extends('emails.layouts.transactional', [
    'emailTitle' => 'Bienvenue dans l’écosystème SOLUTCLOUD',
    'preheader' => 'Votre inscription aux actualités SOLUTCLOUD est confirmée.',
    'emailCategory' => 'Newsletter',
    'emailBadge' => 'Inscription confirmée',
    'emailIntro' => 'Vous recevrez désormais une sélection utile d’actualités, de conseils et d’évolutions autour de la gestion d’entreprise.',
])

@section('content')
    <p>Bonjour,</p>
    <p>Merci de rejoindre SOLUTCLOUD. Nous privilégions des communications claires, occasionnelles et directement utiles à votre activité.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:26px 0">
        <tr>
            <td width="30" valign="top" style="color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700">01</td>
            <td style="padding:0 0 18px 12px;border-bottom:1px solid #eceff1;color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6"><strong style="color:#202124">Conseils opérationnels</strong><br>Des méthodes concrètes pour mieux piloter votre entreprise.</td>
        </tr>
        <tr>
            <td width="30" valign="top" style="padding-top:18px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700">02</td>
            <td style="padding:18px 0 18px 12px;border-bottom:1px solid #eceff1;color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6"><strong style="color:#202124">Nouveautés produit</strong><br>Les fonctionnalités et améliorations qui comptent réellement.</td>
        </tr>
        <tr>
            <td width="30" valign="top" style="padding-top:18px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700">03</td>
            <td style="padding:18px 0 0 12px;color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6"><strong style="color:#202124">Ressources sélectionnées</strong><br>Des contenus pour accélérer l’adoption de vos outils numériques.</td>
        </tr>
    </table>

    <p style="font-size:13px;color:#70757a">Inscription enregistrée pour <strong>{{ $subscriber->email }}</strong>.</p>
@endsection

@section('action')
    <a href="https://solutcloud.com" class="email-button">Découvrir la plateforme</a>
@endsection

@section('notice')
    Vous recevez ce message parce que cette adresse a été inscrite à la newsletter depuis solutcloud.com.
@endsection
