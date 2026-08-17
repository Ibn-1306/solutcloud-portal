<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    const STATUS_DRAFT   = 'draft';
    const STATUS_SENT    = 'sent';
    const STATUS_PAID    = 'paid';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'quote_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'amount',
        'duration',
        'description',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:0',
        'sent_at' => 'datetime',
    ];

    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest('id')->first();
        $next = $last ? ((int) substr($last->quote_number, -4)) + 1 : 1;
        return sprintf('DEV-%s-%04d', $year, $next);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}