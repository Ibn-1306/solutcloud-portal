@extends('emails.layouts.transactional', [
    'emailTitle' => 'Nouvelle commande à provisionner',
    'preheader' => 'Une commande SOLUTCLOUD payée nécessite la création de son instance.',
    'emailCategory' => 'Administration',
    'emailBadge' => 'Paiement confirmé',
    'emailIntro' => 'Le paiement est validé. L’environnement client peut maintenant être configuré et mis en service.',
])

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;border:1px solid #dadce0;border-radius:8px;background:#ffffff">
        <tr>
            <td style="padding:22px 24px">
                <div style="color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase">Montant encaissé</div>
                <div style="margin-top:7px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:28px;font-weight:700;line-height:1.2">{{ number_format((float) $order->amount, 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>

    <p>Une nouvelle commande a été enregistrée pour <strong>{{ $order->company_name }}</strong>. Voici les informations utiles à son traitement.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Entreprise</td><td class="email-detail-value">{{ $order->company_name }}</td></tr>
        <tr><td class="email-detail-label">Responsable</td><td class="email-detail-value">{{ $order->customer_name }}</td></tr>
        <tr><td class="email-detail-label">E-mail</td><td class="email-detail-value"><a href="mailto:{{ $order->customer_email }}" style="color:#2b909a;text-decoration:none">{{ $order->customer_email }}</a></td></tr>
        <tr><td class="email-detail-label">Téléphone</td><td class="email-detail-value">{{ $order->customer_phone ?: 'Non renseigné' }}</td></tr>
        <tr><td class="email-detail-label">Offre</td><td class="email-detail-value">{{ strtoupper($order->plan) }}</td></tr>
        <tr><td class="email-detail-label">Transaction</td><td class="email-detail-value">{{ $order->transaction_id }}</td></tr>
        <tr><td class="email-detail-label email-detail-last">Domaine prévu</td><td class="email-detail-value email-detail-last">{{ \Illuminate\Support\Str::slug($order->company_name) }}.solutcloud.com</td></tr>
    </table>
@endsection

@section('action')
    <a href="https://login.solutcloud.com/admin" class="email-button">Ouvrir l’administration</a>
@endsection

@section('notice')
    Vérifiez la transaction, créez l’instance puis contrôlez son accessibilité avant l’envoi des identifiants au client.
@endsection
