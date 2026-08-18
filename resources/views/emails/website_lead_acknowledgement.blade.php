@php
    $requestLabel = match ($lead->type) {
        'trial' => 'Demande d’essai',
        'quote' => 'Demande de devis',
        default => 'Message de contact',
    };

    $title = match ($lead->type) {
        'trial' => 'Votre demande d’essai est confirmée',
        'quote' => 'Votre demande de devis est confirmée',
        default => 'Votre message a bien été transmis',
    };
@endphp

@extends('emails.layouts.transactional', [
    'emailTitle' => $title,
    'preheader' => 'Votre demande a bien été transmise à l’équipe SOLUTCLOUD.',
    'emailCategory' => 'Suivi de demande',
    'emailBadge' => 'Demande reçue',
    'emailIntro' => 'Notre équipe a bien reçu vos informations et reviendra vers vous dans les meilleurs délais.',
])

@section('content')
    <p>Bonjour <strong>{{ $lead->fullname }}</strong>,</p>
    <p>Merci d’avoir contacté SOLUTCLOUD. Votre demande est enregistrée et a été transmise à un conseiller.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Type</td><td class="email-detail-value">{{ $requestLabel }}</td></tr>
        <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">SC-{{ str_pad((string) $lead->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">E-mail</td><td class="email-detail-value email-detail-last">{{ $lead->email }}</td></tr>
    </table>

    <div style="margin-top:24px;padding:18px 20px;border:1px solid #dadce0;border-radius:8px;background:#f8f9fa">
        <div style="margin-bottom:6px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase">Prochaine étape</div>
        <div style="color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65">Un conseiller examinera votre demande et vous contactera avec les informations adaptées à votre besoin.</div>
    </div>
@endsection

@section('action')
    <a href="https://solutcloud.com" class="email-button">Visiter solutcloud.com</a>
@endsection

@section('notice')
    Ceci est un accusé de réception automatique. Vous pouvez répondre à ce message pour ajouter une précision à votre demande.
@endsection
