<?php

namespace App\Console\Commands;

use App\Mail\BillingReminderMail;
use App\Mail\DeviceSuspendedMail;
use App\Models\BillingContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessOverdueContracts extends Command
{
    protected $signature = 'billing:process-overdue
                            {--dry-run : 実行せず対象のみ表示}';

    protected $description = 'past_due契約の猶予経過チェック：Day15リマインダー + Day30デバイス停止';

    /** @var int Day15リマインダー送信の基準日 */
    private const REMINDER_DAY = 15;

    /** @var int デバイス停止の基準日 */
    private const SUSPEND_DAY = 30;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $now    = now();

        // past_due状態の契約を対象
        $contracts = BillingContract::where('status', 'past_due')
            ->whereNotNull('past_due_at')
            ->get();

        if ($contracts->isEmpty()) {
            $this->info('past_due契約なし');
            return Command::SUCCESS;
        }

        $this->info("past_due契約: {$contracts->count()}件");

        $reminderCount = 0;
        $suspendCount  = 0;

        foreach ($contracts as $contract) {
            $daysPassed = (int) $contract->past_due_at->diffInDays($now);

            // Day 30以上経過 → 停止処理
            if ($daysPassed >= self::SUSPEND_DAY) {
                if ($dryRun) {
                    $this->line("[DRY-RUN] SUSPEND: contract#{$contract->id} ({$contract->getNotificationDisplayName()}) past_due_at={$contract->past_due_at}, {$daysPassed}日経過");
                } else {
                    $contract->markSuspended();
                    $this->sendSuspendedMail($contract);
                    $this->info("SUSPEND: contract#{$contract->id} ({$contract->getNotificationDisplayName()})");
                    $suspendCount++;
                }
                continue;
            }

            // Day 15〜16日経過 → リマインダー送信（1日分のウィンドウ）
            // dailyバッチなので、Day15.0〜Day16.0の範囲に入った日に1度だけ発火
            if ($daysPassed >= self::REMINDER_DAY && $daysPassed < self::REMINDER_DAY + 1) {
                $remainingDays = self::SUSPEND_DAY - $daysPassed;
                if ($dryRun) {
                    $this->line("[DRY-RUN] REMINDER: contract#{$contract->id} ({$contract->getNotificationDisplayName()}) past_due_at={$contract->past_due_at}, {$daysPassed}日経過, 残り{$remainingDays}日");
                } else {
                    $this->sendReminderMail($contract, $remainingDays);
                    $this->info("REMINDER: contract#{$contract->id} ({$contract->getNotificationDisplayName()}) 残り{$remainingDays}日");
                    $reminderCount++;
                }
            }
        }

        $summary = $dryRun
            ? "[DRY-RUN] 実行されませんでした"
            : "リマインダー送信: {$reminderCount}件, 停止処理: {$suspendCount}件";
        $this->info($summary);
        Log::info("[billing:process-overdue] {$summary}");

        return Command::SUCCESS;
    }

    /**
     * Day 15リマインダーメール送信
     */
    private function sendReminderMail(BillingContract $contract, int $remainingDays): void
    {
        $email = $contract->getNotificationEmail();
        if (!$email) {
            Log::warning("ProcessOverdueContracts: contract#{$contract->id} no notification email, skip reminder");
            return;
        }

        try {
            Mail::to($email)->send(new BillingReminderMail($contract, $remainingDays));
            Log::info("ProcessOverdueContracts: contract#{$contract->id} reminder sent to {$email}");
        } catch (\Exception $e) {
            Log::error("ProcessOverdueContracts: contract#{$contract->id} reminder mail failed: " . $e->getMessage());
        }
    }

    /**
     * Day 30停止通知メール送信
     */
    private function sendSuspendedMail(BillingContract $contract): void
    {
        $email = $contract->getNotificationEmail();
        if (!$email) {
            Log::warning("ProcessOverdueContracts: contract#{$contract->id} no notification email, skip suspended mail");
            return;
        }

        try {
            Mail::to($email)->send(new DeviceSuspendedMail($contract));
            Log::info("ProcessOverdueContracts: contract#{$contract->id} suspended mail sent to {$email}");
        } catch (\Exception $e) {
            Log::error("ProcessOverdueContracts: contract#{$contract->id} suspended mail failed: " . $e->getMessage());
        }
    }
}
