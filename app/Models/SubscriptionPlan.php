<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'package',
        'duration_months',
        'promo_price',
        'regular_price',
        'active',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'promo_price' => 'integer',
        'regular_price' => 'integer',
        'active' => 'boolean',
    ];
}
