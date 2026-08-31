<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSecurityLink extends Model
{
    public const TYPE_ACTIVATION = 'activation';

    public const TYPE_RESET = 'reset';

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'requested_by',
        'type',
        'status',
        'sent_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_ACTIVATION ? 'Activation initiale' : 'Mot de passe oublié';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'Envoyé',
            self::STATUS_FAILED => 'Échec',
            default => 'En cours',
        };
    }
}
