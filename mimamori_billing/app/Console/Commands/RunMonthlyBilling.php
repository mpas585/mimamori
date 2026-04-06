<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BillingContract;
use App\Jobs\MonthlyBillingJob;

class RunMonthlyBilling extends Command
{
    protected $signature = 'billing:run-monthly
                            {--id= : 特定のcontract_idのみ実行}
                            {--dry-run : 課金せずに対象と金額を表示}';

    protected $description = '月次課金を実行する（毎月1日スケジューラーから呼ばれる）';

    public function handle(): void
    {
        $targetId = $this->option('id');
        $dryRun   = $this->option('dry-run');

        $query = BillingContract::where('status', 'active')
            ->whereNotNull('payjp_customer_id');

        if ($targetId) {
            $query->where('id', $targetId);
        } else {
            // 通常実行: next_billing_date が今日以前のもの
            $query->where('next_billing_date', '<=', now()->toDateString());
        }

        $contracts = $query->get();

        if ($contracts->isEmpty()) {
            $this->info('課金対象なし');
            return;
        }

        $this->info("課金対象: {$contracts->count()} 件");
        $this->newLine();

        foreach ($contracts as $contract) {
            $amount = $contract->calcAmount();
            $orgName = $contract->organization?->name ?? "contract#{$contract->id}";

            if ($dryRun) {
                $this->line("[DRY-RUN] {$orgName}: ¥{$amount} (本体{$contract->device_count}台 + プレミアム{$contract->premium_device_count}台)");
                continue;
            }

            $this->line("課金実行: {$orgName} ¥{$amount}...");
            MonthlyBillingJob::dispatchSync($contract->id);
            $this->info("  → 完了");
        }

        $this->newLine();
        $this->info($dryRun ? 'DRY-RUN 終了' : '月次課金 完了');
    }
}
