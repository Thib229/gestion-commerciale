<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'client_limit_per_day',
        'pdf_enabled',
        'statistics_enabled',
        'multi_users',
    ];

    protected $casts = [
        'pdf_enabled'        => 'boolean',
        'statistics_enabled' => 'boolean',
        'multi_users'        => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
