<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\BillingContract;
use App\Models\BillingLog;
use App\Models\Device;

class MonthlyBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $contractId
    ) {}

    public function handle(): void
    {
        $contract = BillingContract::find($this->contractId);

        if (!$contract) {
            Log::warning("MonthlyBillingJob: contract {$this->contractId} not found");
            return;
        }

        if ($contract->status !== 'active') {
            Log::info("MonthlyBillingJob: contract {$this->contractId} is not active, skip");
            return;
        }

        if (!$contract->payjp_customer_id) {
            Log::warning("MonthlyBillingJob: contract {$this->contractId} has no payjp_customer_id");
            return;
        }

        // billing_start_at が設定済み かつ 今日以前のデバイスのみ課金対象
        $baseQuery = Device::where('organization_id', $contract->organization_id)
            ->whereNotNull('billing_start_at')
            ->where('billing_start_at', '<=', now()->toDateString());

        $activeDeviceCount  = (clone $baseQuery)->count();
        $activePremiumCount = (clone $baseQuery)->where('premium_enabled', 1)->count();

        $amount = ($activeDeviceCount  * $contract->unit_price)
                + ($activePremiumCount * $contract->premium_unit_price);

        if ($amount <= 0) {
            Log::info("MonthlyBillingJob: contract {$this->contractId} amount is 0 (no active devices), skip");
            return;
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $charge = \Payjp\Charge::create([
                'amount'      => $amount,
                'currency'    => 'jpy',
                'customer'    => $contract->payjp_customer_id,
                'description' => "みまもりトーフ 月額利用料 ({$activeDeviceCount}台 / プレミアム{$activePremiumCount}台)",
            ]);

            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => $activeDeviceCount,
                'premium_device_count' => $activePremiumCount,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            $contract->update([
                'device_count'         => $activeDeviceCount,
                'premium_device_count' => $activePremiumCount,
                'amount'               => $amount,
                'status'               => 'active',
                'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
            ]);

            Log::info("MonthlyBillingJob: contract {$this->contractId} charged ¥{$amount} ({$activeDeviceCount} devices / charge: {$charge->id})");

        } catch (\Exception $e) {
            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => $activeDeviceCount,
                'premium_device_count' => $activePremiumCount,
                'payjp_charge_id'      => null,
                'status'               => 'failed',
                'error_message'        => $e->getMessage(),
                'billed_at'            => now(),
            ]);

            $contract->update(['status' => 'past_due']);

            Log::error("MonthlyBillingJob: contract {$this->contractId} failed: " . $e->getMessage());
        }
    }
}
