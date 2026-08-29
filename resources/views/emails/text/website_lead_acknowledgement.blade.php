@php
    $requestLabel = match ($lead->type) {
        'trial' => 'demande de test',
        'order' => 'commande',
        'quote' => 'demande de devis',
        default => 'message',
    };
    $referencePrefix = match ($lead->type) {
        'trial' => 'TEST',
        'order' => 'CMD',
        'quote' => 'DEVIS-REQ',
        default => 'SC',
    };
@endphp
SOLUTCLOUD — Confirmation de votre {{ $requestLabel }}

Bonjour {{ $lead->fullname }},

Votre {{ $requestLabel }} a bien été transmise à l’équipe SOLUTCLOUD.
Référence : {{ $referencePrefix }}-{{ now()->format('y') }}-{{ str_pad((string) $lead->id, 4, '0', STR_PAD_LEFT) }}
@if($lead->offer)
Offre : SOLUTCLOUD {{ $lead->offer }}
@endif
E-mail : {{ $lead->email }}

Notre équipe reviendra vers vous dans les meilleurs délais.

I-SOLUTIONS · SOLUTCLOUD
sales@i-solutions.ci · https://solutcloud.com