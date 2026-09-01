SOLUTCLOUD — Votre instance est en cours d’installation

Bonjour {{ $user->name }},

Notre équipe a lancé l’installation de l’environnement SOLUTCLOUD dédié à {{ $company->name }}. Merci de patienter pendant sa finalisation.

Entreprise : {{ $company->name }}
Offre : SOLUTCLOUD {{ strtoupper($company->package) }}
@if($payment)
Référence : {{ $payment->reference }}
@endif
État : Installation en cours

@if($activationUrl)
Vous pouvez déjà activer votre espace client SOLUTCLOUD afin de gérer votre abonnement en toute simplicité.

Créez votre mot de passe personnel depuis ce lien sécurisé :
{{ $activationUrl }}
@else
Votre espace client est déjà activé. Vous pouvez continuer à y gérer votre abonnement pendant l’installation.
@endif

Lorsque l’installation sera terminée, vous recevrez un dernier e-mail contenant l’adresse et les accès de votre instance ERP.

Aucune action technique n’est requise pendant l’installation. Le lien d’activation est personnel et ne doit pas être partagé.

I-SOLUTIONS · SOLUTCLOUD
sales@i-solutions.ci
