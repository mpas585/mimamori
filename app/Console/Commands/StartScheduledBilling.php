<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\BillingContract;
use App\Models\Device;

class StartScheduledBilling extends Command
{
    protected $signature = 'billing:start-scheduled
                            {--dry-run : 処理内容を表示のみ、実行しない}';

    protected $description = 'billing_start_date到来デバイスの課金開始フラグ（billing_started_at）を立てる。毎日cronで実行';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');
        $today  = now()->toDateString();

        // billing_start_date <= today かつ billing_started_at NULL かつ organization_id ありのデバイスを検索
        // B2C（organization_idなし）は既存のPlanController即時課金フローのため本コマンドの対象外
        $devices = Device::whereNotNull('billing_start_date')
            ->where('billing_start_date', '<=', $today)
            ->whereNull('billing_started_at')
            ->whereNotNull('organization_id')
            ->with('organization')
            ->get();

        if ($devices->isEmpty()) {
            $this->info('対象デバイスなし');
            return;
        }

        $this->info("対象デバイス: {$devices->count()} 件 (today = {$today})");
        $this->newLine();

        $started = 0;
        $skipped = 0;

        foreach ($devices as $device) {
            $orgName = $device->organization?->name ?? "org#{$device->organization_id}";

            // 組織のactiveなBillingContractを確認
            $contract = BillingContract::where('organization_id', $device->organization_id)
                ->where('status', 'active')
                ->first();

            if (!$contract) {
                $this->warn("  [SKIP] {$device->device_id} ({$orgName}): activeなBillingContractなし");
                Log::warning('StartScheduledBilling: no active contract', [
                    'device_id'       => $device->device_id,
                    'organization_id' => $device->organization_id,
                ]);
                $skipped++;
                continue;
            }

            if (!$contract->payjp_customer_id) {
                $this->warn("  [SKIP] {$device->device_id} ({$orgName}): pay.jp customer_id未登録");
                Log::warning('StartScheduledBilling: no payjp_customer_id', [
                    'device_id'       => $device->device_id,
                    'organization_id' => $device->organization_id,
                    'contract_id'     => $contract->id,
                ]);
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] {$device->device_id} ({$orgName}): billing_started_at を立てます（contract#{$contract->id}）");
                continue;
            }

            $device->update(['billing_started_at' => now()]);

            $this->info("  [OK] {$device->device_id} ({$orgName}): 課金開始（contract#{$contract->id}）");
            Log::info('StartScheduledBilling: started', [
                'device_id'       => $device->device_id,
                'organization_id' => $device->organization_id,
                'contract_id'     => $contract->id,
            ]);

            $started++;
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("DRY-RUN 終了（実行予定: " . ($devices->count() - $skipped) . " 件, スキップ: {$skipped} 件）");
        } else {
            $this->info("完了: 開始 {$started} 件, スキップ {$skipped} 件");
        }
    }
}
