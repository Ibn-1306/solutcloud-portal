SOLUTCLOUD — Votre récapitulatif de règlement

Bonjour {{ explode(" ", trim($payment->customer_name))[0] }},

Voici le récapitulatif de votre règlement SOLUTCLOUD.

Numéro de reçu : {{ $payment->reference }}
Offre : SOLUTCLOUD {{ strtoupper($payment->package) }}
Montant : {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}

Choisissez le moyen de règlement disponible qui vous convient. Votre instance sera préparée dès confirmation.

Cordialement,
Equipe SOLUTCLOUD
sales@i-solutions.ci

Payer en toute sécurité : {{ $payment->customerCheckoutUrl() }}

I-SOLUTIONS · SOLUTCLOUD