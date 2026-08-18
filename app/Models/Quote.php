<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    const STATUS_DRAFT = 'draft';

    const STATUS_SENT = 'sent';

    const STATUS_PAID = 'paid';

    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'quote_number',
        'payment_transaction_id',
        'payment_url',
        'payment_initialized_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'amount',
        'duration',
        'description',
        'status',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'payment_initialized_at' => 'datetime',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest('id')->lockForUpdate()->first();
        $next = $last ? ((int) substr($last->quote_number, -4)) + 1 : 1;

        return sprintf('DEVIS-%s-%04d', now()->format('y'), $next);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
