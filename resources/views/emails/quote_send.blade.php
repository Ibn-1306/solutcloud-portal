@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre devis SOLUTCLOUD PREMIUM',
    'preheader' => 'Votre devis et son lien de paiement sécurisé sont disponibles.',
    'emailCategory' => 'Devis commercial',
    'emailBadge' => $quote->quote_number,
    'emailIntro' => 'Notre équipe a préparé une proposition adaptée aux besoins de votre organisation.',
])

@section('content')
    <p>Bonjour <strong>{{ $quote->customer_name }}</strong>,</p>
    <p>Merci pour votre confiance. Voici le récapitulatif du devis préparé pour <strong>{{ $quote->company_name ?: 'votre organisation' }}</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;border:1px solid #dadce0;border-radius:8px;background:#ffffff">
        <tr>
            <td style="padding:22px 24px">
                <div style="color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase">Montant à régler</div>
                <div style="margin-top:7px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:30px;font-weight:700;line-height:1.2">{{ number_format((float) $quote->amount, 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $quote->quote_number }}</td></tr>
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $quote->company_name ?: 'Non renseignée' }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Durée</td><td class="email-detail-value email-detail-last">{{ $quote->duration }} mois</td></tr>
    </table>

    @if($quote->description)
        <div style="margin-top:24px;padding:18px 20px;border:1px solid #dadce0;border-radius:8px;background:#f8f9fa">
            <div style="margin-bottom:8px;color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">Détails de la proposition</div>
            <div style="color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7">{!! nl2br(e($quote->description)) !!}</div>
        </div>
    @endif
@endsection

@section('action')
    <a href="{{ $quote->payment_url }}" class="email-button" style="font-size:15px;padding:16px 28px">Cliquez ici pour payer</a>
@endsection

@section('notice')
    <strong>Paiement sécurisé.</strong> Le bouton vous redirige vers la plateforme de paiement Moneroo. Le montant de <strong>{{ number_format((float) $quote->amount, 0, ',', ' ') }} FCFA</strong> est associé à la référence {{ $quote->quote_number }} et ne peut pas être modifié depuis ce message.
@endsection
