<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingContract extends Model
{
    protected $fillable = [
        'organization_id',
        'payjp_customer_id',
        'device_count',
        'premium_device_count',
        'unit_price',
        'premium_unit_price',
        'amount',
        'status',
        'next_billing_date',
        'canceled_at',
    ];

    protected $casts = [
        'next_billing_date' => 'date',
        'canceled_at'       => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BillingLog::class);
    }

    /**
     * 請求額を計算して amount カラムを更新する
     */
    public function recalculate(): void
    {
        $amount = ($this->device_count * $this->unit_price)
                + ($this->premium_device_count * $this->premium_unit_price);

        $this->update(['amount' => $amount]);
    }

    /**
     * 金額計算（保存なし）
     */
    public function calcAmount(): int
    {
        return ($this->device_count * $this->unit_price)
             + ($this->premium_device_count * $this->premium_unit_price);
    }
}
