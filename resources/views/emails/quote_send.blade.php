@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre proposition SOLUTCLOUD PREMIUM',
    'preheader' => 'Votre proposition commerciale personnalisée est disponible.',
    'emailCategory' => 'Proposition commerciale',
    'emailBadge' => $quote->quote_number,
    'emailIntro' => 'Une proposition adaptée aux besoins de votre organisation a été préparée par notre équipe.',
])

@section('content')
    <p>Bonjour <strong>{{ $quote->customer_name }}</strong>,</p>
    <p>Merci pour votre intérêt. Vous trouverez ci-dessous la synthèse de notre proposition pour <strong>{{ $quote->company_name ?: 'votre organisation' }}</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;border-radius:10px;background:#102a2d">
        <tr>
            <td style="padding:22px 24px">
                <div style="color:#9cced1;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase">Montant de la proposition</div>
                <div style="margin-top:7px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:28px;font-weight:700;line-height:1.2">{{ number_format((float) $quote->amount, 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $quote->quote_number }}</td></tr>
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $quote->company_name ?: 'Non renseignée' }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Durée</td><td class="email-detail-value email-detail-last">{{ $quote->duration }}</td></tr>
    </table>

    @if($quote->description)
        <div style="margin-top:24px;padding:18px 20px;border:1px solid #dce6e8;border-radius:10px;background:#f8fafb">
            <div style="margin-bottom:8px;color:#718286;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">Périmètre proposé</div>
            <div style="color:#40565a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7">{!! nl2br(e($quote->description)) !!}</div>
        </div>
    @endif
@endsection

@section('action')
    <a href="mailto:sales@i-solutions.ci?subject={{ rawurlencode('Proposition '.$quote->quote_number) }}" class="email-button">Échanger sur cette proposition</a>
@endsection

@section('notice')
    Pour accepter cette proposition ou demander un ajustement, répondez simplement à cet e-mail. Notre équipe commerciale vous accompagnera dans la suite du processus.
@endsection
