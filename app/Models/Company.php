<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'name', 
        'email',
        'phone',
        'subdomain', 
        'custom_domain', 
        'package', 
        'status', 
        'expires_at',
    ];

    /**
     * Le cast des types de colonnes.
     */
    protected $casts = [
        'expires_at' => 'datetime',
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