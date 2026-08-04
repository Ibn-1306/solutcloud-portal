<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     * On ajoute 'total_paid' pour le suivi financier.
     */
    protected $fillable = [
        'name', 
        'subdomain', 
        'custom_domain', 
        'package', 
        'status', 
        'expires_at',
        'total_paid' // AJOUTÉ ICI
    ];

    /**
     * Le cast des types de colonnes.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'total_paid' => 'decimal:2', // Force le format numérique avec 2 décimales
    ];

    /**
     * SP1 : Relation avec les utilisateurs.
     * Permet de récupérer l'email du client lié.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}