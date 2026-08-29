@php
    $requestLabel = match ($lead->type) {
        'trial' => 'Demande de test',
        'order' => 'Commande',
        'quote' => 'Demande de devis',
        default => 'Message de contact',
    };
@endphp
SOLUTCLOUD — {{ $requestLabel }} à traiter

Type : {{ $requestLabel }}
Nom : {{ $lead->fullname }}
E-mail : {{ $lead->email }}
Téléphone : {{ $lead->phone ?: 'Non renseigné' }}
Entreprise : {{ $lead->company_name ?: 'Non renseignée' }}
Profil : {{ $lead->profile ?: 'Non renseigné' }}
Offre : {{ $lead->offer ? 'SOLUTCLOUD '.$lead->offer : 'Non applicable' }}

Message :
{{ $lead->message ?: 'Aucun message complémentaire.' }}

Source : solutcloud.com