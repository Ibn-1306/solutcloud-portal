<?php

namespace App\Support;

final class OfferCatalog
{
    /**
     * @return array{label: string, audience: string, details: array<int, array{label: string, value: string}>}
     */
    public static function details(string $package): array
    {
        return match (strtolower($package)) {
            'business' => [
                'label' => 'BUSINESS',
                'audience' => 'PME en pleine croissance',
                'details' => [
                    ['label' => 'Utilisateurs', 'value' => 'Jusqu’à 5 utilisateurs'],
                    ['label' => 'Modules', 'value' => 'Pack START, CRM, projets, temps, marketing et enquêtes'],
                    ['label' => 'Stockage', 'value' => '20 Go de stockage SSD'],
                    ['label' => 'Sauvegardes', 'value' => 'Sauvegardes quotidiennes'],
                ],
            ],
            'premium' => [
                'label' => 'PREMIUM',
                'audience' => 'Environnement complet et personnalisé',
                'details' => [
                    ['label' => 'Utilisateurs', 'value' => 'Utilisateurs illimités'],
                    ['label' => 'Modules', 'value' => 'Tous les modules Dolibarr, API et interconnexions'],
                    ['label' => 'Infrastructure', 'value' => 'Serveur dédié et isolé'],
                    ['label' => 'Sauvegardes', 'value' => 'Rétention des sauvegardes pendant 30 jours'],
                ],
            ],
            default => [
                'label' => 'START',
                'audience' => 'Indépendants, artisans et TPE',
                'details' => [
                    ['label' => 'Utilisateurs', 'value' => 'Jusqu’à 2 utilisateurs'],
                    ['label' => 'Modules', 'value' => 'Ventes, stocks, finance, facturation et point de vente'],
                    ['label' => 'Stockage', 'value' => '5 Go de stockage SSD'],
                    ['label' => 'Sauvegardes', 'value' => 'Sauvegardes hebdomadaires'],
                ],
            ],
        };
    }
}
