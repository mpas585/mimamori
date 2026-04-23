<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        'past_due_at',
    ];

    protected $casts = [
        'next_billing_date' => 'date',
        'canceled_at'       => 'datetime',
        'past_due_at'       => 'datetime',
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

    // ================================================================
    // 課金状態遷移メソッド（Phase 1 追加）
    // ================================================================

    /**
     * 対象デバイスを取得
     *   B2B: contract.organization_id に紐付く全デバイス
     *   B2C: Subscription.stripe_customer_id = contract.payjp_customer_id のデバイス
     */
    public function getTargetDevices(): Collection
    {
        if ($this->organization_id) {
            return Device::where('organization_id', $this->organization_id)->get();
        }

        // B2C: Subscription経由
        $deviceIds = Subscription::where('stripe_customer_id', $this->payjp_customer_id)
            ->pluck('device_id')
            ->all();

        if (empty($deviceIds)) {
            return new Collection();
        }

        return Device::whereIn('id', $deviceIds)->get();
    }

    /**
     * past_due 状態に遷移
     *   - contract.status = past_due
     *   - past_due_at = now()（既に設定済みの場合は維持、猶予起点はブレさせない）
     *   - 対象デバイスの premium_enabled = false
     *   - B2C の場合は Subscription.status も past_due に同期
     */
    public function markPastDue(?string $errorMessage = null): void
    {
        DB::transaction(function () {
            $this->update([
                'status'      => 'past_due',
                'past_due_at' => $this->past_due_at ?: now(),
            ]);

            // 対象デバイスの premium_enabled を false に
            foreach ($this->getTargetDevices() as $device) {
                if ($device->premium_enabled) {
                    $device->update(['premium_enabled' => false]);
                }
            }

            // B2C: Subscription も past_due に同期
            if (!$this->organization_id) {
                Subscription::where('stripe_customer_id', $this->payjp_customer_id)
                    ->update(['status' => 'past_due']);
            }
        });

        if ($errorMessage) {
            Log::warning("BillingContract[{$this->id}] marked as past_due: {$errorMessage}");
        }
    }

    /**
     * active 状態に遷移（復帰処理含む）
     *   - contract.status = active / past_due_at = null / next_billing_date 更新
     *   - 対象デバイスの premium_enabled = true、suspended_at = null（復帰）
     *   - B2C の場合は Subscription.status = active + current_period 更新
     */
    public function markActive(): void
    {
        DB::transaction(function () {
            $this->update([
                'status'            => 'active',
                'past_due_at'       => null,
                'next_billing_date' => now()->addMonth()->startOfMonth()->toDateString(),
            ]);

            // 対象デバイスの premium_enabled を true に、suspended状態なら復帰
            foreach ($this->getTargetDevices() as $device) {
                $device->update([
                    'premium_enabled' => true,
                    'suspended_at'    => null,
                ]);
            }

            // B2C: Subscription も active に + period 更新
            if (!$this->organization_id) {
                Subscription::where('stripe_customer_id', $this->payjp_customer_id)
                    ->update([
                        'status'               => 'active',
                        'current_period_start' => now()->toDateString(),
                        'current_period_end'   => now()->addMonth()->startOfMonth()->toDateString(),
                    ]);
            }
        });
    }

    /**
     * suspended 状態に遷移（30日猶予経過後のデバイス停止）
     *   - contract.status = suspended
     *   - 対象デバイスの suspended_at = now() + premium_enabled = false
     *   - B2C の場合は Subscription も canceled 扱い
     */
    public function markSuspended(): void
    {
        DB::transaction(function () {
            $this->update(['status' => 'suspended']);

            foreach ($this->getTargetDevices() as $device) {
                if (!$device->suspended_at) {
                    $device->update([
                        'suspended_at'    => now(),
                        'premium_enabled' => false,
                    ]);
                }
            }

            // B2C: Subscription も canceled に（契約終了）
            if (!$this->organization_id) {
                Subscription::where('stripe_customer_id', $this->payjp_customer_id)
                    ->update([
                        'status'      => 'canceled',
                        'canceled_at' => now(),
                    ]);
            }
        });

        Log::warning("BillingContract[{$this->id}] marked as suspended");
    }

    /**
     * 通知先メールアドレスを取得
     *   B2B: organization の operator 先頭1名
     *   B2C: Subscription経由の Device.notification_setting.email_1
     */
    public function getNotificationEmail(): ?string
    {
        if ($this->organization_id) {
            return $this->organization
                ?->partnerUsers()
                ->where('role', 'operator')
                ->first()
                ?->email;
        }

        $subscription = Subscription::where('stripe_customer_id', $this->payjp_customer_id)->first();
        if (!$subscription) {
            return null;
        }

        $device = Device::find($subscription->device_id);
        return $device?->notificationSetting?->email_1;
    }

    /**
     * 通知メールに表示する対象名称
     *   B2B: organization.name
     *   B2C: device.nickname or device.device_id
     */
    public function getNotificationDisplayName(): string
    {
        if ($this->organization_id) {
            return $this->organization?->name ?? "組織ID:{$this->organization_id}";
        }

        $subscription = Subscription::where('stripe_customer_id', $this->payjp_customer_id)->first();
        if (!$subscription) {
            return "契約ID:{$this->id}";
        }

        $device = Device::find($subscription->device_id);
        return $device?->nickname ?: ($device?->device_id ?? "契約ID:{$this->id}");
    }
}
