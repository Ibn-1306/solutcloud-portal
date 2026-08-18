@php
    $requestLabel = match ($lead->type) {
        'trial' => 'Demande d’essai',
        'quote' => 'Demande de devis',
        default => 'Message de contact',
    };
@endphp

@extends('emails.layouts.transactional', [
    'emailTitle' => 'Nouvelle demande commerciale',
    'preheader' => $requestLabel.' reçue de '.$lead->fullname.'.',
    'emailCategory' => 'Équipe commerciale',
    'emailBadge' => 'Action requise',
    'emailIntro' => $lead->fullname.' vient de transmettre une demande depuis le site solutcloud.com.',
])

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel" style="margin-top:0">
        <tr><td class="email-detail-label">Type</td><td class="email-detail-value">{{ $requestLabel }}</td></tr>
        <tr><td class="email-detail-label">Nom</td><td class="email-detail-value">{{ $lead->fullname }}</td></tr>
        <tr><td class="email-detail-label">E-mail</td><td class="email-detail-value"><a href="mailto:{{ $lead->email }}" style="color:#176f77;text-decoration:none">{{ $lead->email }}</a></td></tr>
        <tr><td class="email-detail-label">Téléphone</td><td class="email-detail-value">{{ $lead->phone ?: 'Non renseigné' }}</td></tr>
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $lead->company_name ?: 'Non renseignée' }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Profil</td><td class="email-detail-value email-detail-last">{{ $lead->profile ?: 'Non renseigné' }}</td></tr>
    </table>

    <div style="margin-top:24px;padding:18px 20px;border:1px solid #dce6e8;border-radius:10px;background:#f8fafb">
        <div style="margin-bottom:8px;color:#718286;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">Message</div>
        <div style="color:#40565a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7">{!! nl2br(e($lead->message ?: 'Aucun message complémentaire.')) !!}</div>
    </div>
@endsection

@section('action')
    <a href="mailto:{{ $lead->email }}" class="email-button">Répondre au demandeur</a>
@endsection

@section('notice')
    Source : solutcloud.com · Reçue le {{ optional($lead->created_at)->format('d/m/Y à H:i') ?: now()->format('d/m/Y à H:i') }}.
@endsection
