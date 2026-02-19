<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
     * 通知ログを記録（メール送信はTODO）
     */
    private function sendNotification(Device $device, string $type, string $subject): void
    {
        // --- デバイス個別の通知先に送信 ---
        $this->sendDeviceNotification($device, $type, $subject);

        // --- 組織管理者への通知 ---
        $this->sendOrgNotification($device, $type, $subject);
    }

    /**
     * デバイス個別の通知設定に基づいて送信
     */
    private function sendDeviceNotification(Device $device, string $type, string $subject): void
    {
        $notif = $device->notificationSetting;

        // メールアドレスが登録されていて有効な場合
        if ($notif && $notif->email_enabled && $notif->email_1) {
            // 通知ログに記録
            DB::table('notification_logs')->insert([
                'device_id' => $device->id,
                'type' => $type,
                'channel' => 'email',
                'recipient' => $notif->email_1,
                'subject' => "[みまもりデバイス] {$subject}",
                'body' => $this->buildNotificationBody($device, $type),
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // TODO: 実際のメール送信
            // Mail::to($notif->email_1)->send(new DeviceAlertMail($device, $type));

            // email_2, email_3 にも送信
            foreach (['email_2', 'email_3'] as $field) {
                if ($notif->$field) {
                    DB::table('notification_logs')->insert([
                        'device_id' => $device->id,
                        'type' => $type,
                        'channel' => 'email',
                        'recipient' => $notif->$field,
                        'subject' => "[みまもりデバイス] {$subject}",
                        'body' => $this->buildNotificationBody($device, $type),
                        'status' => 'pending',
                        'created_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * 組織管理者への通知（デバイス個別の通知設定とは独立して動作）
     */
    private function sendOrgNotification(Device $device, string $type, string $subject): void
    {
        // 組織に所属していない場合はスキップ
        if (!$device->organization_id) {
            return;
        }

        $organization = Organization::find($device->organization_id);
        if (!$organization) {
            return;
        }

        // 組織の通知メールアドレスを取得（notification_enabledがfalseなら空配列）
        $orgEmails = $organization->getNotificationEmails();
        if (empty($orgEmails)) {
            return;
        }

        // デバイス個別の通知先と重複するメールは除外
        $deviceEmails = $this->getDeviceNotificationEmails($device);

        $body = $this->buildOrgNotificationBody($device, $type, $organization);

        foreach ($orgEmails as $email) {
            // デバイス個別通知と同じメアドには二重送信しない
            if (in_array($email, $deviceEmails)) {
                continue;
            }

            DB::table('notification_logs')->insert([
                'device_id' => $device->id,
                'type' => $type,
                'channel' => 'email',
                'recipient' => $email,
                'subject' => "[みまもりデバイス] [{$organization->name}] {$subject}",
                'body' => $body,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // TODO: 実際のメール送信
            // Mail::to($email)->send(new OrgDeviceAlertMail($device, $type, $organization));
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
     * 組織管理者向け通知の本文を生成（部屋番号等の追加情報あり）
     */
    private function buildOrgNotificationBody(Device $device, string $type, Organization $organization): string
    {
        $name = $device->nickname ?: $device->device_id;
        $now = Carbon::now()->format('Y/m/d H:i');

        // 部屋番号・入居者名を取得
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
