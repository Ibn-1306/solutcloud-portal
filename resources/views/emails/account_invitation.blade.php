@extends('emails.layouts.transactional', [
    'emailTitle' => 'Activez votre espace client',
    'preheader' => 'Votre compte et les détails de votre offre SOLUTCLOUD sont disponibles.',
    'emailCategory' => 'Compte client',
    'emailBadge' => 'Activation',
    'emailIntro' => 'Consultez votre offre puis créez le mot de passe de votre espace client.',
])

@section('content')
    <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
    <p>Votre espace client a été créé pour <strong>{{ $company->name }}</strong>. Voici le récapitulatif de l’offre associée à votre compte.</p>

    <div style="margin:28px 0 12px;color:#202124;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.4">Détails de votre offre</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel" style="margin-top:0">
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $company->name }}</td></tr>
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">SOLUTCLOUD {{ $offerDetails['label'] }}</td></tr>
        <tr><td class="email-detail-label">Profil</td><td class="email-detail-value">{{ $offerDetails['audience'] }}</td></tr>
        @if($payment)
            <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $payment->reference }}</td></tr>
            <tr><td class="email-detail-label email-detail-last">Montant</td><td class="email-detail-value email-detail-last">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</td></tr>
        @else
            <tr><td class="email-detail-label email-detail-last">État</td><td class="email-detail-value email-detail-last">Compte créé</td></tr>
        @endif
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:18px 0 0;border:1px solid #dadce0;border-radius:8px;background:#ffffff">
        @foreach($offerDetails['details'] as $detail)
            <tr>
                <td width="34%" valign="top" class="email-detail-label{{ $loop->last ? ' email-detail-last' : '' }}">{{ $detail['label'] }}</td>
                <td valign="top" class="email-detail-value{{ $loop->last ? ' email-detail-last' : '' }}">{{ $detail['value'] }}</td>
            </tr>
        @endforeach
    </table>

    <div style="margin-top:20px;padding:18px 20px;border:1px solid #dadce0;border-radius:8px;background:#f8f9fa">
        <div style="margin-bottom:7px;color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.55px;text-transform:uppercase">Description / Notes additionnelles</div>
        <div style="color:#3c4043;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65">{!! nl2br(e($payment?->description ?: 'Aucune note additionnelle.')) !!}</div>
    </div>

    <div style="margin:32px 0 12px;color:#202124;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.4">Activation de votre compte</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel" style="margin-top:0">
        <tr><td class="email-detail-label">Nom</td><td class="email-detail-value">{{ $user->name }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">E-mail de connexion</td><td class="email-detail-value email-detail-last">{{ $user->email }}</td></tr>
    </table>

    <p>Pour votre première connexion, créez votre mot de passe personnel en cliquant sur le bouton ci-dessous.</p>
@endsection

@section('action')
    <a href="{{ $resetUrl }}" class="email-button">Activer mon compte</a>
@endsection

@section('notice')
    Ce lien est personnel et expire automatiquement après une durée limitée. Ne le partagez avec personne.
@endsection
