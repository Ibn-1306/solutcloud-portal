@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre paiement est confirmé',
    'preheader' => 'Votre commande SOLUTCLOUD a bien été enregistrée.',
    'emailCategory' => 'Commande',
    'emailBadge' => 'Paiement reçu',
    'emailIntro' => 'Votre commande est validée. Notre équipe prépare maintenant votre environnement de travail sécurisé.',
])

@section('content')
    <p>Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p>Merci pour votre confiance. Nous avons bien reçu le règlement associé à la commande de <strong>{{ $order->company_name }}</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:26px 0">
        <tr>
            <td width="34" valign="top"><div style="width:28px;height:28px;border-radius:50%;background:#176f77;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;line-height:28px;text-align:center">1</div></td>
            <td style="padding:2px 0 18px 10px;color:#20373a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.5"><strong>Paiement reçu</strong><br><span style="color:#718286">La transaction a été confirmée.</span></td>
        </tr>
        <tr>
            <td width="34" valign="top"><div style="width:28px;height:28px;border-radius:50%;border:2px solid #79b7bc;color:#176f77;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;line-height:24px;text-align:center;box-sizing:border-box">2</div></td>
            <td style="padding:2px 0 18px 10px;color:#20373a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.5"><strong>Configuration en cours</strong><br><span style="color:#718286">Votre instance est préparée et contrôlée.</span></td>
        </tr>
        <tr>
            <td width="34" valign="top"><div style="width:28px;height:28px;border-radius:50%;border:2px solid #d3dfe1;color:#718286;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;line-height:24px;text-align:center;box-sizing:border-box">3</div></td>
            <td style="padding:2px 0 0 10px;color:#20373a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.5"><strong>Accès à venir</strong><br><span style="color:#718286">Vous recevrez un message distinct dès la mise en service.</span></td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $order->company_name }}</td></tr>
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">{{ strtoupper($order->plan) }}</td></tr>
        <tr><td class="email-detail-label">Montant</td><td class="email-detail-value">{{ number_format((float) $order->amount, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="email-detail-label email-detail-last">Transaction</td><td class="email-detail-value email-detail-last">{{ $order->transaction_id }}</td></tr>
    </table>
@endsection

@section('action')
    <a href="https://solutcloud.com" class="email-button">Découvrir SOLUTCLOUD</a>
@endsection

@section('notice')
    Pour votre sécurité, SOLUTCLOUD ne vous demandera jamais votre mot de passe ou vos coordonnées bancaires par e-mail.
@endsection
