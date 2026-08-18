<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteLead extends Model
{
    protected $fillable = [
        'type',
        'fullname',
        'email',
        'phone',
        'company_name',
        'profile',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }
}
