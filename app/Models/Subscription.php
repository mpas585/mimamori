<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'device_id',
        'plan',
        'billing_cycle',
        'stripe_customer_id',
        'stripe_subscription_id',
        'current_period_start',
        'current_period_end',
        'status',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'current_period_start' => 'date',
            'current_period_end' => 'date',
            'canceled_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
