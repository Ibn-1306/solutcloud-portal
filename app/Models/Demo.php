<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demo extends Model
{
    public const DEFAULT_SUBDOMAIN = 'demo';

    protected $fillable = [
        'company_name',
        'subdomain',
        'email',
        'phone',
        'erp_login',
        'erp_password',
        'starts_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function getUrlAttribute(): string
    {
        return 'https://'.$this->subdomain.'.solutcloud.com';
    }
}
