<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillingFailedMail;
use App\Models\BillingContract;
use App\Models\BillingLog;

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

        // active と past_due を課金対象とする
        // （past_due からの復帰を可能にするため・chargeNow 経由で手動リトライ可能）
        if (!in_array($contract->status, ['active', 'past_due'], true)) {
            Log::info("MonthlyBillingJob: contract {$this->contractId} is not billable (status: {$contract->status}), skip");
            return;
        }

        if (!$contract->payjp_customer_id) {
            Log::warning("MonthlyBillingJob: contract {$this->contractId} has no payjp_customer_id");
            return;
        }

        // 課金額をDBの申込み状況から動的に計算（基本料金 + SMS料金 + AIコール料金）
        $amount = $contract->calcAmount();

        if ($amount <= 0) {
            Log::info("MonthlyBillingJob: contract {$this->contractId} amount is 0, skip");
            return;
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $charge = \Payjp\Charge::create([
                'amount'      => $amount,
                'currency'    => 'jpy',
                'customer'    => $contract->payjp_customer_id,
                'description' => "みまもりデバイス 月額利用料 ({$contract->device_count}台)",
            ]);

            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => $contract->device_count,
                'premium_device_count' => 0,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            // amount を更新した上で active 遷移（past_due からの復帰も含む）
            $contract->update(['amount' => $amount]);
            $contract->markActive();

            Log::info("MonthlyBillingJob: contract {$this->contractId} charged ¥{$amount} (charge: {$charge->id})");

        } catch (\Exception $e) {
            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => $contract->device_count,
                'premium_device_count' => 0,
                'payjp_charge_id'      => null,
                'status'               => 'failed',
                'error_message'        => $e->getMessage(),
                'billed_at'            => now(),
            ]);

            // past_due 遷移（premium停止、Subscription同期）
            $contract->markPastDue($e->getMessage());

            Log::error("MonthlyBillingJob: contract {$this->contractId} failed: " . $e->getMessage());

            // 課金失敗通知メール送信（送信失敗してもJob自体は失敗させない）
            $this->sendFailureNotification($contract);
        }
    }

    /**
     * 課金失敗通知メールを送信する
     */
    private function sendFailureNotification(BillingContract $contract): void
    {
        $email = $contract->getNotificationEmail();

        if (!$email) {
            Log::warning("MonthlyBillingJob: contract {$contract->id} has no notification email, skip mail");
            return;
        }

        try {
            Mail::to($email)->send(new BillingFailedMail($contract));
            Log::info("MonthlyBillingJob: contract {$contract->id} failure notification sent to {$email}");
        } catch (\Exception $mailEx) {
            Log::error("MonthlyBillingJob: contract {$contract->id} failure mail send failed: " . $mailEx->getMessage());
        }
    }
}
