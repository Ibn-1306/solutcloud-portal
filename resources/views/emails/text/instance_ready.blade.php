SOLUTCLOUD — Votre instance est opérationnelle

Bonjour,

L’instance dédiée à {{ $company->name }} est prête.

Adresse de connexion : {{ $url }}

ACCÈS ERP
@foreach($credentials as $credential)

{{ $credential['label'] }}
Identifiant : {{ $credential['login'] }}
Mot de passe initial : {{ $credential['password'] }}
@endforeach

@if($company->package === 'premium')
L’accès super administrateur vous permet de configurer librement l’ERP et de créer autant d’utilisateurs que nécessaire.
@else
Ces comptes correspondent au nombre d’utilisateurs inclus dans votre offre SOLUTCLOUD.
@endif

Modifiez chaque mot de passe initial après la première connexion.

I-SOLUTIONS · SOLUTCLOUD
sales@i-solutions.ci · https://solutcloud.com
