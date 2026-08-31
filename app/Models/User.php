<?php

namespace App\Models;

use App\Mail\ClientPasswordResetMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * CONSTANTES DE RÔLES
     * Je centralise les rôles ici pour pouvoir les utiliser partout
     * (ex: User::ROLE_ADMIN) au lieu de manipuler des chaînes de caractères.
     */
    const ROLE_ADMIN = 'admin';

    const ROLE_CLIENT = 'client';

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_initialized_at',
        'role',
        'company_id',
    ];

    /**
     * Les attributs à masquer pour les tableaux (JSON/API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Typage automatique des données (Laravel 11).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_initialized_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS (Eloquent)
    |--------------------------------------------------------------------------
    */

    /**
     * Un utilisateur appartient à une Entreprise (Company).
     * Vital pour le PortalController et l'affichage du Dashboard Client.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE RÔLES (Méthodes de confort)
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'utilisateur est l'administrateur de SolutCloud.
     * Utilisation : if($user->isAdmin()) { ... }
     */
    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url('/reset-password/'.$token.'?email='.urlencode($this->email));

        Mail::to($this->email)->send(new ClientPasswordResetMail($this, $resetUrl));
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est un client (locataire d'une instance).
     */
    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }
}
