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
        'payjp_charge_id',
        'device_count',
        'unit_price',
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
     * 請求額を計算してamountカラムを更新する
     * ※ MonthlyBillingJobの課金前に呼び出す
     */
    public function recalculate(): void
    {
        $this->update(['amount' => $this->calcAmount()]);
    }

    /**
     * 今月の請求額を計算する（DBの申込み状況から動的に算出）
     *
     * 内訳：
     *   基本料金    = 契約台数 × ¥700
     *   SMS料金     = SMS申込み台数 × ¥100
     *   AIコール料金 = AIコール申込み台数 × ¥300
     */
    public function calcAmount(): int
    {
        // 基本料金
        $基本料金合計 = $this->device_count * $this->unit_price;

        if (!$this->organization_id) {
            return $基本料金合計;
        }

        // この組織でSMS通知を申し込んでいるデバイス台数
        $SMS申込み台数 = Device::where('organization_id', $this->organization_id)
            ->whereHas('notificationSetting', fn($q) => $q->where('sms_enabled', true))
            ->count();

        // この組織でAIコールを申し込んでいるデバイス台数
        $AIコール申込み台数 = Device::where('organization_id', $this->organization_id)
            ->whereHas('notificationSetting', fn($q) => $q->where('voice_enabled', true))
            ->count();

        return $基本料金合計
            + ($SMS申込み台数 * 100)
            + ($AIコール申込み台数 * 300);
    }
}
