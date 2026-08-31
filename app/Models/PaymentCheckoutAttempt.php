<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCheckoutAttempt extends Model
{
    protected $fillable = [
        'payment_id',
        'moneroo_payment_id',
        'checkout_url',
        'initialized_at',
        'superseded_at',
    ];

    protected function casts(): array
    {
        return [
            'initialized_at' => 'datetime',
            'superseded_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
