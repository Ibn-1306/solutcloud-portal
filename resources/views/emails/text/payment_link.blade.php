SOLUTCLOUD — Règlement {{ $payment->reference }}

Bonjour {{ $payment->customer_name }},

Comme convenu avec notre équipe, votre règlement est disponible sur Moneroo.

Référence : {{ $payment->reference }}
Offre : SOLUTCLOUD {{ strtoupper($payment->package) }}
Montant : {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}

Accéder au paiement :
{{ $payment->checkout_url }}

Vous pourrez choisir le moyen de paiement disponible qui vous convient. La préparation de votre instance commencera après confirmation du règlement.

I-SOLUTIONS · SOLUTCLOUD
sales@i-solutions.ci
