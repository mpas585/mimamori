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
     *   基本料金    = 課金開始済み台数 × unit_price
     *   SMS料金     = 課金開始済み×SMS申込み台数 × ¥100
     *   AIコール料金 = 課金開始済み×AIコール申込み台数 × ¥300
     *
     * B2B（organization_idあり）の場合、billing_started_at が立っているデバイスのみを対象とする。
     * B2C（organization_idなし）の場合は従来通り固定値で計算。
     */
    public function calcAmount(): int
    {
        // B2C（個人契約）は従来通り固定値で返す
        if (!$this->organization_id) {
            return $this->device_count * $this->unit_price;
        }

        // B2B（組織契約）: billing_started_at が立ったデバイスのみ動的計算
        $本体台数 = Device::where('organization_id', $this->organization_id)
            ->whereNotNull('billing_started_at')
            ->count();

        // この組織で課金開始済み かつ SMS通知を申し込んでいるデバイス台数
        $SMS申込み台数 = Device::where('organization_id', $this->organization_id)
            ->whereNotNull('billing_started_at')
            ->whereHas('notificationSetting', fn($q) => $q->where('sms_enabled', true))
            ->count();

        // この組織で課金開始済み かつ AIコールを申し込んでいるデバイス台数
        $AIコール申込み台数 = Device::where('organization_id', $this->organization_id)
            ->whereNotNull('billing_started_at')
            ->whereHas('notificationSetting', fn($q) => $q->where('voice_enabled', true))
            ->count();

        return $本体台数 * $this->unit_price
            + ($SMS申込み台数 * 100)
            + ($AIコール申込み台数 * 300);
    }
}
