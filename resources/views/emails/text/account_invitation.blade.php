SOLUTCLOUD — Activation de votre espace client

Bonjour {{ $user->name }},

Votre espace client a été créé pour {{ $company->name }}.

Détails de votre offre

Entreprise : {{ $company->name }}
Offre : SOLUTCLOUD {{ $offerDetails['label'] }}
Profil : {{ $offerDetails['audience'] }}
@if($payment)
Référence : {{ $payment->reference }}
Montant : {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}
@endif

@foreach($offerDetails['details'] as $detail)
{{ $detail['label'] }} : {{ $detail['value'] }}
@endforeach

Description / Notes additionnelles

{{ $payment?->description ?: 'Aucune note additionnelle.' }}

Activation de votre compte

Nom : {{ $user->name }}
E-mail de connexion : {{ $user->email }}

Créez votre mot de passe depuis cette adresse personnelle :
{{ $resetUrl }}

Ce lien expire automatiquement. Ne le transférez à personne.

I-SOLUTIONS · SOLUTCLOUD
sales@i-solutions.ci
