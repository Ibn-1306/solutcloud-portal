<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    // Méthode standard pour autoriser l'écriture en base de données
    protected $fillable = [
        'name', 
        'subdomain', 
        'custom_domain', 
        'package', 
        'status', 
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}