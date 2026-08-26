@php
    $requestLabel = match ($lead->type) {
        'trial' => 'Demande de test',
        'order' => 'Commande',
        'quote' => 'Demande de devis',
        default => 'Message de contact',
    };

    $title = match ($lead->type) {
        'trial' => 'Votre demande de test est confirmée',
        'order' => 'Votre commande est confirmée',
        'quote' => 'Votre demande de devis est confirmée',
        default => 'Votre message a bien été transmis',
    };

    $nextStep = match ($lead->type) {
        'trial' => 'Un conseiller vérifiera vos besoins puis vous contactera pour préparer votre accès de test.',
        'order' => 'Un conseiller validera avec vous les informations de la commande avant toute activation ou facturation.',
        'quote' => 'Notre équipe étudiera votre besoin et vous contactera pour convenir de la suite.',
        default => 'Un conseiller examinera votre message et vous répondra dans les meilleurs délais.',
    };

    $referencePrefix = match ($lead->type) {
        'trial' => 'TEST',
        'order' => 'CMD',
        'quote' => 'DEVIS-REQ',
        default => 'SC',
    };
@endphp

@extends('emails.layouts.transactional', [
    'emailTitle' => $title,
    'preheader' => $title,
    'emailCategory' => 'Confirmation',
    'emailBadge' => 'Bien reçu',
])

@section('content')
    <p>Bonjour <strong>{{ $lead->fullname }}</strong>,</p>
    <p>Merci pour votre confiance. Voici le récapitulatif transmis à l’équipe SOLUTCLOUD.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Type</td><td class="email-detail-value">{{ $requestLabel }}</td></tr>
        @if($lead->offer)
            <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">SOLUTCLOUD {{ $lead->offer }}</td></tr>
        @endif
        <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $referencePrefix }}-{{ now()->format('y') }}-{{ str_pad((string) $lead->id, 4, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">E-mail</td><td class="email-detail-value email-detail-last">{{ $lead->email }}</td></tr>
    </table>

    <div style="margin-top:24px;padding:18px 20px;border:1px solid #dadce0;border-radius:8px;background:#f8f9fa">
        <div style="margin-bottom:6px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase">Prochaine étape</div>
        <div style="color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65">{{ $nextStep }}</div>
    </div>
@endsection
