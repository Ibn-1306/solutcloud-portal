@extends('emails.layouts.transactional', [
    'emailTitle' => 'Votre passage à BUSINESS est en cours de traitement',
    'preheader' => 'Votre paiement est confirmé. Notre équipe finalise maintenant votre évolution vers SOLUTCLOUD BUSINESS.',
    'emailCategory' => 'Évolution d’offre',
    'emailBadge' => 'Traitement en cours',
    'emailIntro' => 'Votre demande a bien été prise en charge par l’équipe SOLUTCLOUD.',
])

@section('content')
    <p>Bonjour {{ $payment->customer_name }},</p>
    <p>Nous confirmons la réception de votre paiement <strong>{{ $payment->reference }}</strong> pour le passage de votre entreprise <strong>{{ $payment->company_name }}</strong> à <strong>SOLUTCLOUD BUSINESS</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-panel">
        <tr><td class="email-detail-label">Nouvelle offre</td><td class="email-detail-value"><strong>SOLUTCLOUD BUSINESS</strong></td></tr>
        <tr><td class="email-detail-label">Durée</td><td class="email-detail-value">{{ $payment->duration_months }} mois</td></tr>
        <tr><td class="email-detail-label email-detail-last">Statut</td><td class="email-detail-value email-detail-last"><strong>En cours de traitement</strong></td></tr>
    </table>

    <p>Votre espace reste provisoirement sur l’offre START pendant que notre équipe prépare et valide l’évolution. Merci de patienter : le passage à BUSINESS sera effectif dès la finalisation administrative.</p>
@endsection

@section('notice')
    Aucune action supplémentaire ni aucun nouveau paiement ne sont nécessaires. Votre espace client affichera automatiquement BUSINESS dès que l’évolution sera finalisée.
@endsection
