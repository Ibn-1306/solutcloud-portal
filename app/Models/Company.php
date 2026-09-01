<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    public const SUSPENSION_ADMINISTRATIVE = 'administrative';

    public const SUSPENSION_EXPIRATION = 'expiration';

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
        'suspension_reason',
        'expires_at',
        'erp_login',
        'erp_password',
        'subscription_started_at',
    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'expires_at' => 'datetime',

        'subscription_started_at' => 'datetime',

    ];

    public function getRenewalPlans()
    {
        return SubscriptionPlan::where(
            'package',
            strtoupper($this->package)
        )
            ->where('active', true)
            ->get()
            ->map(function ($plan) {

                $plan->display_price = $plan->promo_price;

                return $plan;

            });
    }

    /**
     * Utilisateurs liés à l'entreprise.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->oldestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * URL publique de l'instance Dolibarr.
     */
    public function getInstanceUrlAttribute(): string
    {
        if ($this->package === 'premium' && ! empty($this->custom_domain)) {
            return 'https://'.$this->custom_domain;
        }

        return 'https://'.$this->subdomain.'.solutcloud.com';
    }

    public function getPackageUpperAttribute(): string
    {
        return strtoupper($this->package);
    }

    public function isAdministrativelySuspended(): bool
    {
        return $this->status === 'suspended'
            && $this->suspension_reason === self::SUSPENSION_ADMINISTRATIVE;
    }

    /**
     * Liste les emplacements LWS possibles, par ordre de priorité.
     * START/BUSINESS utilisent client.solutcloud.com et PREMIUM entreprise.com,
     * avec un repli sous htdocs si la configuration FTP LWS l'exige.
     *
     * @return array<int, string>
     */
    public function ftpPathCandidates(): array
    {
        if ($this->package === 'premium') {
            if (! filled($this->custom_domain)) {
                return [];
            }

            $domain = trim(strtolower($this->custom_domain), " /\\\t\n\r\0\x0B");

            return array_values(array_unique([
                $domain,
                'htdocs/'.$domain,
            ]));
        }

        if (! filled($this->subdomain)) {
            return [];
        }

        $subdomain = trim(strtolower($this->subdomain), " /\\\t\n\r\0\x0B");
        $domain = $subdomain.'.solutcloud.com';

        return array_values(array_unique([
            $domain,
            'htdocs/'.$domain,
        ]));
    }

    public function getResolvedFtpPathAttribute(): ?string
    {
        return $this->ftpPathCandidates()[0] ?? null;
    }
}
