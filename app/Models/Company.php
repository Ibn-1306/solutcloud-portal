<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /**
     * Attributs modifiables.
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
        'erp_login',
        'erp_password',
        'ftp_path',
    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Utilisateurs liés à l'entreprise.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * URL publique de l'instance Dolibarr.
     */
    public function getInstanceUrlAttribute(): string
    {
        if ($this->package === 'premium' && ! empty($this->custom_domain)) {
            return 'https://' . $this->custom_domain;
        }

        return 'https://' . $this->subdomain . '.solutcloud.com';
    }

        /**
         * Chemin FTP LWS de l'instance.
         *
         * START / BUSINESS :
         * htdocs/client.solutcloud.com
         *
         * PREMIUM :
         * client.com
         */
        /**
     * Chemin FTP réellement utilisé pour gérer l'instance.
     *
     * Si un chemin FTP a été enregistré manuellement,
     * il est prioritaire.
     *
     * START / BUSINESS :
     * client.solutcloud.com
     *
     * PREMIUM :
     * chemin FTP réel renseigné lors de l'installation.
     */
    public function getResolvedFtpPathAttribute(): ?string
    {
        if (! empty($this->ftp_path)) {
            return trim($this->ftp_path, '/');
        }

        if ($this->package !== 'premium') {
            return $this->subdomain . '.solutcloud.com';
        }

        return null;
    }
}