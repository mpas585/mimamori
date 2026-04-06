<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingLog extends Model
{
    protected $fillable = [
        'billing_contract_id',
        'amount',
        'device_count',
        'premium_device_count',
        'payjp_charge_id',
        'status',
        'error_message',
        'billed_at',
    ];

    protected $casts = [
        'billed_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(BillingContract::class, 'billing_contract_id');
    }
}
