<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteLead extends Model
{
    protected $fillable = [
        'type',
        'fullname',
        'email',
        'phone',
        'company_name',
        'profile',
        'offer',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function commercialReference(): string
    {
        $prefix = $this->type === 'quote' ? 'DEVIS-REQ' : 'CMD';
        $year = $this->created_at?->format('y') ?? now()->format('y');

        return sprintf('%s-%s-%04d', $prefix, $year, $this->getKey());
    }

    public function commercialTypeLabel(): string
    {
        return $this->type === 'quote' ? 'Demande de devis' : 'Commande';
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
