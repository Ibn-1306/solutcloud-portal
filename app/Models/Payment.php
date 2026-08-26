<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_INITIATED = 'initiated';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PURPOSE_INITIAL = 'initial';

    public const PURPOSE_RENEWAL = 'renewal';

    public const PURPOSE_UPGRADE = 'upgrade';

    protected $attributes = [
        'purpose' => self::PURPOSE_INITIAL,
    ];

    protected $fillable = [
        'reference',
        'website_lead_id',
        'company_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'package',
        'amount',
        'currency',
        'description',
        'purpose',
        'duration_months',
        'status',
        'moneroo_payment_id',
        'checkout_url',
        'failure_reason',
        'provider_payload',
        'initialized_at',
        'link_sent_at',
        'verified_at',
        'paid_at',
        'applied_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'duration_months' => 'integer',
            'provider_payload' => 'array',
            'initialized_at' => 'datetime',
            'link_sent_at' => 'datetime',
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
            'applied_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Payment $payment): void {
            if ($payment->reference === null) {
                $payment->forceFill([
                    'reference' => sprintf('PAY-%s-%04d', now()->format('y'), $payment->getKey()),
                ])->saveQuietly();
            }
        });
    }

    public function websiteLead(): BelongsTo
    {
        return $this->belongsTo(WebsiteLead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeVisibleInTracking(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function canSendLink(): bool
    {
        return ! $this->isPaid()
            && is_string($this->checkout_url)
            && $this->checkout_url !== '';
    }

    public function canRemoveFromTracking(): bool
    {
        return ! $this->isPaid()
            && $this->applied_at === null
            && $this->archived_at === null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'À initialiser',
            self::STATUS_INITIATED => 'Lien créé',
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PAID => 'Payé',
            self::STATUS_FAILED => 'Échoué',
            self::STATUS_CANCELLED => 'Annulé',
            default => ucfirst($this->status),
        };
    }
}
