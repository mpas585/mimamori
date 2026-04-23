<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PruneOldLogs extends Command
{
    protected $signature = 'logs:prune';
    protected $description = 'プライバシーポリシー第5条に基づき、90日を超えた古いログを削除する';

    /** @var int 保管期間（日） */
    private const RETENTION_DAYS = 90;

    /** @var int chunk削除のバッチサイズ */
    private const CHUNK_SIZE = 1000;

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(self::RETENTION_DAYS);
        $this->info("削除対象: {$cutoff->format('Y-m-d H:i:s')} より前のログ");

        $totalDetection    = $this->pruneDetectionLogs($cutoff);
        $totalNotification = $this->pruneNotificationLogs($cutoff);
        $totalAiCall       = $this->pruneAiCallTranscripts($cutoff);

        $summary = sprintf(
            'detection_logs: %d件削除, notification_logs: %d件削除, ai_call_logs transcript匿名化: %d件',
            $totalDetection,
            $totalNotification,
            $totalAiCall
        );
        $this->info($summary);
        Log::info('[logs:prune] ' . $summary);

        return Command::SUCCESS;
    }

    /**
     * detection_logs を削除（received_at 基準）
     */
    private function pruneDetectionLogs(Carbon $cutoff): int
    {
        $total = 0;
        do {
            $deleted = DB::table('detection_logs')
                ->where('received_at', '<', $cutoff)
                ->limit(self::CHUNK_SIZE)
                ->delete();
            $total += $deleted;
        } while ($deleted > 0);

        return $total;
    }

    /**
     * notification_logs を削除（created_at 基準）
     */
    private function pruneNotificationLogs(Carbon $cutoff): int
    {
        $total = 0;
        do {
            $deleted = DB::table('notification_logs')
                ->where('created_at', '<', $cutoff)
                ->limit(self::CHUNK_SIZE)
                ->delete();
            $total += $deleted;
        } while ($deleted > 0);

        return $total;
    }

    /**
     * ai_call_logs の transcript / recording_sid を NULL 化
     * （called_at 基準、行は残して監査用にjudgment等を保持）
     */
    private function pruneAiCallTranscripts(Carbon $cutoff): int
    {
        $total = 0;
        do {
            $updated = DB::table('ai_call_logs')
                ->where('called_at', '<', $cutoff)
                ->where(function ($q) {
                    $q->whereNotNull('transcript')
                      ->orWhereNotNull('recording_sid');
                })
                ->limit(self::CHUNK_SIZE)
                ->update([
                    'transcript'    => null,
                    'recording_sid' => null,
                ]);
            $total += $updated;
        } while ($updated > 0);

        return $total;
    }
}
