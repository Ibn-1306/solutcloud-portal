@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre règlement SOLUTCLOUD',
    'preheader' => 'Consultez le récapitulatif et poursuivez votre règlement sur Moneroo.',
    'emailCategory' => 'Paiement',
    'emailBadge' => 'Récapitulatif',
])

@section('content')
    <p>Bonjour <strong>{{ $payment->customer_name }}</strong>,</p>
    <p>Comme convenu avec notre équipe, votre règlement est disponible sur Moneroo.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Référence</td><td class="email-detail-value">{{ $payment->reference }}</td></tr>
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">SOLUTCLOUD {{ strtoupper($payment->package) }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Montant</td><td class="email-detail-value email-detail-last">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</td></tr>
    </table>

    <p>Vous pourrez choisir le moyen de paiement disponible qui vous convient. La préparation de votre instance commencera après confirmation du règlement.</p>
@endsection

@section('action')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" width="420" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:420px">
                    <tr>
                        <td align="center" bgcolor="#2b909a" style="border-radius:7px">
                            <a href="{{ $payment->checkout_url }}" class="email-button" style="display:block;padding:18px 32px;border-radius:7px;background:#2b909a;color:#ffffff!important;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;line-height:1.2;text-align:center;text-decoration:none">
                                Payer en toute sécurité
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
