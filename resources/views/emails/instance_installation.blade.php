@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre instance est en cours d’installation',
    'preheader' => 'Patientez pendant l’installation et activez dès maintenant votre espace client SOLUTCLOUD.',
    'emailCategory' => 'Installation & compte client',
    'emailBadge' => 'En préparation',
    'emailIntro' => 'Votre environnement se prépare. Vous pouvez déjà activer votre espace client et gérer votre abonnement en toute simplicité.',
])

@section('content')
    <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
    <p>Votre instance est en cours d'installation. Nous vous invitons donc à patienter pendant sa finalisation.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $company->name }}</td></tr>
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">SOLUTCLOUD {{ strtoupper($company->package) }}</td></tr>
        @if($payment)
            <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $payment->reference }}</td></tr>
        @endif
        <tr><td class="email-detail-label email-detail-last">État</td><td class="email-detail-value email-detail-last">Installation en cours</td></tr>
    </table>

    @if($activationUrl)
        <div style="margin:30px 0 12px;color:#202124;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.4">Activez dès maintenant votre espace client</div>
        <p>Créez votre mot de passe personnel pour accéder à votre espace SOLUTCLOUD. Vous pourrez y consulter et gérer votre abonnement en toute simplicité pendant que notre équipe termine l’installation.</p>
    @else
        <div style="margin:30px 0 12px;color:#202124;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.4">Votre espace client est déjà activé</div>
        <p>Vous pouvez continuer à gérer votre abonnement depuis votre espace client habituel pendant la préparation de votre instance.</p>
    @endif

    <p>Lorsque l’installation sera terminée, vous recevrez un dernier e-mail contenant l’adresse et les accès de votre instance ERP.</p>
@endsection

@if($activationUrl)
    @section('action')
        <a href="{{ $activationUrl }}" class="email-button">Activer mon espace client</a>
    @endsection
@endif

@section('notice')
    Aucune action technique n’est requise pendant l’installation. Le lien d’activation est personnel et ne doit pas être partagé.
@endsection
