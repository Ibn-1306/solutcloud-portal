<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'transaction_id',
        'company_name',
        'customer_name',
        'customer_email',
        'customer_phone',
        'plan',
        'amount',
        'status'
    ];
}