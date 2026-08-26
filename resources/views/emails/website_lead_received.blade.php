@php
    $requestLabel = match ($lead->type) {
        'trial' => 'Demande de test',
        'order' => 'Commande',
        'quote' => 'Demande de devis',
        default => 'Message de contact',
    };

    $title = match ($lead->type) {
        'order' => 'Nouvelle commande à traiter',
        'quote' => 'Nouvelle demande de devis',
        'trial' => 'Nouvelle demande de test',
        default => 'Nouvelle demande commerciale',
    };
@endphp

@extends('emails.layouts.transactional', [
    'emailTitle' => $title,
    'preheader' => $requestLabel.' reçue de '.$lead->fullname.'.',
    'emailCategory' => 'Équipe commerciale',
    'emailBadge' => 'Action requise',
])

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel" style="margin-top:0">
        <tr><td class="email-detail-label">Type</td><td class="email-detail-value">{{ $requestLabel }}</td></tr>
        <tr><td class="email-detail-label">Nom</td><td class="email-detail-value">{{ $lead->fullname }}</td></tr>
        <tr><td class="email-detail-label">E-mail</td><td class="email-detail-value"><a href="mailto:{{ $lead->email }}" style="color:#2b909a;text-decoration:none">{{ $lead->email }}</a></td></tr>
        <tr><td class="email-detail-label">Téléphone</td><td class="email-detail-value">{{ $lead->phone ?: 'Non renseigné' }}</td></tr>
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $lead->company_name ?: 'Non renseignée' }}</td></tr>
        <tr><td class="email-detail-label">Profil</td><td class="email-detail-value">{{ $lead->profile ?: 'Non renseigné' }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Offre</td><td class="email-detail-value email-detail-last">{{ $lead->offer ? 'SOLUTCLOUD '.$lead->offer : 'Non applicable' }}</td></tr>
    </table>

    <div style="margin-top:24px;padding:18px 20px;border:1px solid #dadce0;border-radius:8px;background:#f8f9fa">
        <div style="margin-bottom:8px;color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">Message</div>
        <div style="color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7">{!! nl2br(e($lead->message ?: 'Aucun message complémentaire.')) !!}</div>
    </div>
@endsection

@section('notice')
    Source : solutcloud.com · Reçue le {{ optional($lead->created_at)->format('d/m/Y à H:i') ?: now()->format('d/m/Y à H:i') }}.
@endsection
