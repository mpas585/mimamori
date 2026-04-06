<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiCallLog;
use App\Models\Device;
use App\Models\Organization;
use App\Mail\DeviceAlertMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Twilio\Rest\Client as TwilioClient;

class CheckUndetectedDevices extends Command
{
    protected $signature = 'devices:check-undetected';
    protected $description = '未検知デバイスをチェックしてアラートを発行する';

    public function handle(): int
    {
        $now = Carbon::now();
        $alertCount = 0;
        $offlineCount = 0;

        // 稼働中のデバイスを取得（inactive以外）
        $devices = Device::where('status', '!=', 'inactive')->get();

        foreach ($devices as $device) {
            // --- 通信途絶チェック ---
            // last_received_at が閾値×2 を超えている場合 → offline
            if ($device->last_received_at) {
                $hoursSinceReceived = (int) abs($now->diffInHours($device->last_received_at));

                if ($hoursSinceReceived > $device->alert_threshold_hours * 2) {
                    if ($device->status !== 'offline') {
                        $device->update(['status' => 'offline']);
                        $this->sendNotification($device, 'offline', '通信途絶');
                        $offlineCount++;
                        $this->info("OFFLINE: {$device->device_id} (最終受信: {$device->last_received_at})");
                    }
                    continue;
                }
            }

            // --- 外出モード中はスキップ ---
            if ($device->away_mode) {
                if ($device->away_until && $now->gt($device->away_until)) {
                    // 外出モード期限切れ → 自動解除
                    $device->update(['away_mode' => false, 'away_until' => null]);
                } else {
                    continue;
                }
            }

            // --- 未検知チェック ---
            $lastDetected = $device->last_human_detected_at;

            // 一度も検知がない場合は last_received_at を基準にする
            if (!$lastDetected) {
                $lastDetected = $device->last_received_at;
            }

            // 基準時刻がない場合はスキップ
            if (!$lastDetected) {
                continue;
            }

            $hoursSinceDetected = (int) abs($now->diffInHours($lastDetected));

            if ($hoursSinceDetected >= $device->alert_threshold_hours) {
                // 未検知アラート
                if ($device->status !== 'alert') {
                    $device->update(['status' => 'alert']);
                    $this->sendNotification($device, 'alert', '未検知アラート');
                    $alertCount++;
                    $this->info("ALERT: {$device->device_id} (最終検知: {$lastDetected}, 閾値: {$device->alert_threshold_hours}時間)");
                }
            } elseif ($device->status === 'alert' || $device->status === 'offline') {
                // 閾値内に戻った → normal に復帰
                $device->update(['status' => 'normal']);
                $this->info("RECOVERED: {$device->device_id}");
            }
        }

        $this->info("チェック完了: アラート {$alertCount}件, 通信途絶 {$offlineCount}件");

        return Command::SUCCESS;
    }

    /**
     * 通知を送信
     */
    private function sendNotification(Device $device, string $type, string $subject): void
    {
        $this->sendDeviceNotification($device, $type, $subject);
        $this->sendOrgNotification($device, $type, $subject);
    }

    /**
     * デバイス個別の通知設定に基づいて送信
     */
    private function sendDeviceNotification(Device $device, string $type, string $subject): void
    {
        $notif = $device->notificationSetting;
        if (!$notif) {
            return;
        }

        // AIコール契約済み（voice_enabled）かつアラート時 → 電話を先に発信
        // 通話結果の通知はAiCallControllerのWebhookで処理するのでここでは終了
        if ($notif->voice_enabled && !empty($notif->voice_phone_1) && $type === 'alert') {
            $this->makeAiCall($device, $notif->voice_phone_1);
            $this->info("  AI CALL: {$notif->voice_phone_1}");
            return;
        }

        $body = $this->buildNotificationBody($device, $type);
        $mailSubject = "[みまもりデバイス] {$subject}";

        // メール通知
        if ($notif->email_enabled) {
            foreach (['email_1', 'email_2', 'email_3'] as $field) {
                if (empty($notif->$field)) {
                    continue;
                }
                $this->sendMailWithLog($device, $type, $notif->$field, $mailSubject, $body);
            }
        }

        // SMS通知（プレミアム機能）
        if ($notif->sms_enabled) {
            $smsBody = $this->buildSmsBody($device, $type);
            foreach (['sms_phone_1', 'sms_phone_2'] as $field) {
                if (empty($notif->$field)) {
                    continue;
                }
                $this->sendSmsWithLog($device, $type, $notif->$field, $smsBody);
            }
        }
    }

    /**
     * AIコール発信
     */
    private function makeAiCall(Device $device, string $phone): void
    {
        $log = AiCallLog::create([
            'device_id'   => $device->id,
            'call_status' => 'failed',
            'called_at'   => now(),
        ]);

        try {
            $twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $call = $twilio->calls->create(
                $phone,
                config('services.twilio.from'),
                [
                    'url'                  => config('app.url') . '/api/ai-call/twiml',
                    'statusCallback'       => config('app.url') . '/api/ai-call/status-webhook',
                    'statusCallbackMethod' => 'POST',
                    'statusCallbackEvent'  => ['completed', 'no-answer', 'busy', 'failed'],
                    'timeout'              => 30,
                ]
            );

            $log->update(['call_sid' => $call->sid]);

        } catch (\Exception $e) {
            $log->update(['error_message' => mb_substr($e->getMessage(), 0, 500)]);
            $this->error("  AI CALL FAILED: {$e->getMessage()}");
        }
    }

    /**
     * 組織管理者への通知
     */
    private function sendOrgNotification(Device $device, string $type, string $subject): void
    {
        if (!$device->organization_id) {
            return;
        }

        $organization = Organization::find($device->organization_id);
        if (!$organization) {
            return;
        }

        $orgEmails = $organization->getNotificationEmails();
        if (empty($orgEmails)) {
            return;
        }

        $deviceEmails = $this->getDeviceNotificationEmails($device);
        $mailSubject = "[みまもりデバイス] [{$organization->name}] {$subject}";
        $body = $this->buildOrgNotificationBody($device, $type, $organization);

        foreach ($orgEmails as $email) {
            if (in_array($email, $deviceEmails)) {
                continue;
            }
            $this->sendMailWithLog($device, $type, $email, $mailSubject, $body);
        }
    }

    /**
     * メール送信 + 通知ログ記録
     */
    private function sendMailWithLog(Device $device, string $type, string $recipient, string $subject, string $body): void
    {
        $logId = DB::table('notification_logs')->insertGetId([
            'device_id' => $device->id,
            'type' => $type,
            'channel' => 'email',
            'recipient' => $recipient,
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        try {
            Mail::to($recipient)->send(new DeviceAlertMail($subject, $body, $type));

            DB::table('notification_logs')
                ->where('id', $logId)
                ->update(['status' => 'sent', 'sent_at' => now()]);

            $this->info("  MAIL SENT: {$recipient}");
        } catch (\Exception $e) {
            DB::table('notification_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'failed',
                    'error_message' => mb_substr($e->getMessage(), 0, 500),
                ]);

            $this->error("  MAIL FAILED: {$recipient} - {$e->getMessage()}");
        }
    }

    /**
     * SMS送信 + 通知ログ記録
     */
    private function sendSmsWithLog(Device $device, string $type, string $recipient, string $body): void
    {
        $logId = DB::table('notification_logs')->insertGetId([
            'device_id' => $device->id,
            'type' => $type,
            'channel' => 'sms',
            'recipient' => $recipient,
            'subject' => null,
            'body' => $body,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        try {
            $twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $twilio->messages->create($recipient, [
                'from' => config('services.twilio.from'),
                'body' => $body,
            ]);

            DB::table('notification_logs')
                ->where('id', $logId)
                ->update(['status' => 'sent', 'sent_at' => now()]);

            $this->info("  SMS SENT: {$recipient}");
        } catch (\Exception $e) {
            DB::table('notification_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'failed',
                    'error_message' => mb_substr($e->getMessage(), 0, 500),
                ]);

            $this->error("  SMS FAILED: {$recipient} - {$e->getMessage()}");
        }
    }

    /**
     * デバイス個別に設定されている通知メールアドレス一覧を取得
     */
    private function getDeviceNotificationEmails(Device $device): array
    {
        $notif = $device->notificationSetting;
        if (!$notif || !$notif->email_enabled) {
            return [];
        }

        $emails = [];
        foreach (['email_1', 'email_2', 'email_3'] as $field) {
            if (!empty($notif->$field)) {
                $emails[] = $notif->$field;
            }
        }

        return $emails;
    }

    /**
     * SMS本文を生成（短文）
     */
    private function buildSmsBody(Device $device, string $type): string
    {
        $name = $device->nickname ?: $device->device_id;

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            return "【みまもりデバイス】{$name}：{$hours}時間以上、人の動きが検知されていません。ご確認ください。";
        }

        if ($type === 'offline') {
            return "【みまもりデバイス】{$name}：デバイスとの通信が途絶えています。電池切れまたは電波状況をご確認ください。";
        }

        return '';
    }

    /**
     * デバイス個別通知の本文を生成
     */
    private function buildNotificationBody(Device $device, string $type): string
    {
        $name = $device->nickname ?: $device->device_id;
        $now = Carbon::now()->format('Y/m/d H:i');

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            $lastDetected = $device->last_human_detected_at
                ? $device->last_human_detected_at->format('Y/m/d H:i')
                : '不明';

            return "【未検知アラート】\n\n"
                . "デバイス: {$name}\n"
                . "最終検知: {$lastDetected}\n"
                . "設定閾値: {$hours}時間\n"
                . "検知時刻: {$now}\n\n"
                . "{$hours}時間以上、人の動きが検知されていません。\n"
                . "ご確認をお願いいたします。";
        }

        if ($type === 'offline') {
            $lastReceived = $device->last_received_at
                ? $device->last_received_at->format('Y/m/d H:i')
                : '不明';

            return "【通信途絶】\n\n"
                . "デバイス: {$name}\n"
                . "最終通信: {$lastReceived}\n"
                . "検知時刻: {$now}\n\n"
                . "デバイスとの通信が途絶えています。\n"
                . "電池切れまたは電波状況をご確認ください。";
        }

        return '';
    }

    /**
     * 組織管理者向け通知の本文を生成
     */
    private function buildOrgNotificationBody(Device $device, string $type, Organization $organization): string
    {
        $name = $device->nickname ?: $device->device_id;
        $now = Carbon::now()->format('Y/m/d H:i');

        $assignment = $device->orgAssignment;
        $roomNumber = $assignment ? $assignment->room_number : null;
        $tenantName = $assignment ? $assignment->tenant_name : null;

        $locationInfo = '';
        if ($roomNumber) {
            $locationInfo .= "部屋番号: {$roomNumber}\n";
        }
        if ($tenantName) {
            $locationInfo .= "入居者名: {$tenantName}\n";
        }

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            $lastDetected = $device->last_human_detected_at
                ? $device->last_human_detected_at->format('Y/m/d H:i')
                : '不明';

            return "【未検知アラート】\n\n"
                . "組織: {$organization->name}\n"
                . "デバイス: {$name}\n"
                . $locationInfo
                . "最終検知: {$lastDetected}\n"
                . "設定閾値: {$hours}時間\n"
                . "検知時刻: {$now}\n\n"
                . "{$hours}時間以上、人の動きが検知されていません。\n"
                . "ご確認をお願いいたします。\n\n"
                . "管理画面からデバイスの状態を確認できます。";
        }

        if ($type === 'offline') {
            $lastReceived = $device->last_received_at
                ? $device->last_received_at->format('Y/m/d H:i')
                : '不明';

            return "【通信途絶】\n\n"
                . "組織: {$organization->name}\n"
                . "デバイス: {$name}\n"
                . $locationInfo
                . "最終通信: {$lastReceived}\n"
                . "検知時刻: {$now}\n\n"
                . "デバイスとの通信が途絶えています。\n"
                . "電池切れまたは電波状況をご確認ください。\n\n"
                . "管理画面からデバイスの状態を確認できます。";
        }

        return '';
    }
}


