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
    protected $description = '譛ｪ讀懃衍繝・ヰ繧､繧ｹ繧偵メ繧ｧ繝・け縺励※繧｢繝ｩ繝ｼ繝医ｒ逋ｺ陦後☆繧・;

    public function handle(): int
    {
        $now = Carbon::now();
        $alertCount = 0;
        $offlineCount = 0;

        // 遞ｼ蜒堺ｸｭ縺ｮ繝・ヰ繧､繧ｹ繧貞叙蠕暦ｼ・nactive莉･螟厄ｼ・        $devices = Device::where('status', '!=', 'inactive')->get();

        foreach ($devices as $device) {
            // --- 騾壻ｿ｡騾皮ｵｶ繝√ぉ繝・け ---
            // last_received_at 縺碁明蛟､ﾃ・ 繧定ｶ・∴縺ｦ縺・ｋ蝣ｴ蜷・竊・offline
            if ($device->last_received_at) {
                $hoursSinceReceived = (int) abs($now->diffInHours($device->last_received_at));

                if ($hoursSinceReceived > $device->alert_threshold_hours * 2) {
                    if ($device->status !== 'offline') {
                        $device->update(['status' => 'offline']);
                        $this->sendNotification($device, 'offline', '騾壻ｿ｡騾皮ｵｶ');
                        $offlineCount++;
                        $this->info("OFFLINE: {$device->device_id} (譛邨ょ女菫｡: {$device->last_received_at})");
                    }
                    continue;
                }
            }

            // --- 螟門・繝｢繝ｼ繝我ｸｭ縺ｯ繧ｹ繧ｭ繝・・ ---
            if ($device->away_mode) {
                if ($device->away_until && $now->gt($device->away_until)) {
                    // 螟門・繝｢繝ｼ繝画悄髯仙・繧・竊・閾ｪ蜍戊ｧ｣髯､
                    $device->update(['away_mode' => false, 'away_until' => null]);
                } else {
                    continue;
                }
            }

            // --- 譛ｪ讀懃衍繝√ぉ繝・け ---
            $lastDetected = $device->last_human_detected_at;

            // 荳蠎ｦ繧よ､懃衍縺後↑縺・ｴ蜷医・ last_received_at 繧貞渕貅悶↓縺吶ｋ
            if (!$lastDetected) {
                $lastDetected = $device->last_received_at;
            }

            // 蝓ｺ貅匁凾蛻ｻ縺後↑縺・ｴ蜷医・繧ｹ繧ｭ繝・・
            if (!$lastDetected) {
                continue;
            }

            $hoursSinceDetected = (int) abs($now->diffInHours($lastDetected));

            if ($hoursSinceDetected >= $device->alert_threshold_hours) {
                // 譛ｪ讀懃衍繧｢繝ｩ繝ｼ繝・                if ($device->status !== 'alert') {
                    $device->update(['status' => 'alert']);
                    $this->sendNotification($device, 'alert', '譛ｪ讀懃衍繧｢繝ｩ繝ｼ繝・);
                    $alertCount++;
                    $this->info("ALERT: {$device->device_id} (譛邨よ､懃衍: {$lastDetected}, 髢ｾ蛟､: {$device->alert_threshold_hours}譎る俣)");
                }
            } elseif ($device->status === 'alert' || $device->status === 'offline') {
                // 髢ｾ蛟､蜀・↓謌ｻ縺｣縺・竊・normal 縺ｫ蠕ｩ蟶ｰ
                $device->update(['status' => 'normal']);
                $this->info("RECOVERED: {$device->device_id}");
            }
        }

        $this->info("繝√ぉ繝・け螳御ｺ・ 繧｢繝ｩ繝ｼ繝・{$alertCount}莉ｶ, 騾壻ｿ｡騾皮ｵｶ {$offlineCount}莉ｶ");

        return Command::SUCCESS;
    }

    /**
     * 騾夂衍繧帝∽ｿ｡
     */
    private function sendNotification(Device $device, string $type, string $subject): void
    {
        $this->sendDeviceNotification($device, $type, $subject);
        $this->sendOrgNotification($device, $type, $subject);
    }

    /**
     * 繝・ヰ繧､繧ｹ蛟句挨縺ｮ騾夂衍險ｭ螳壹↓蝓ｺ縺･縺・※騾∽ｿ｡
     */
    private function sendDeviceNotification(Device $device, string $type, string $subject): void
    {
        $notif = $device->notificationSetting;
        if (!$notif) {
            return;
        }

        // AI繧ｳ繝ｼ繝ｫ・嘛oice_enabled 縺九▽ 繧｢繝ｩ繝ｼ繝域凾縺ｮ縺ｿ逋ｺ菫｡
        if ($notif->voice_enabled && !empty($notif->voice_phone_1) && $type === 'alert'
            && $device->premium_enabled) {
            $this->makeAiCall($device, $notif->voice_phone_1);
            $this->info("  AI CALL: {$notif->voice_phone_1}");
            return;
        }

        $body = $this->buildNotificationBody($device, $type);
        $mailSubject = "[縺ｿ縺ｾ繧ゅｊ繝・ヰ繧､繧ｹ] {$subject}";

        // 繝｡繝ｼ繝ｫ騾夂衍
        if ($notif->email_enabled) {
            foreach (['email_1', 'email_2', 'email_3'] as $field) {
                if (empty($notif->$field)) {
                    continue;
                }
                $this->sendMailWithLog($device, $type, $notif->$field, $mailSubject, $body);
            }
        }

        // SMS騾夂衍・嘖ms_enabled 縺ｮ蝣ｴ蜷医・縺ｿ騾∽ｿ｡
        if ($notif->sms_enabled && $device->premium_enabled) {
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
     * AI繧ｳ繝ｼ繝ｫ逋ｺ菫｡
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
     * 邨・ｹ皮ｮ｡逅・・∈縺ｮ騾夂衍
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
        $mailSubject = "[縺ｿ縺ｾ繧ゅｊ繝・ヰ繧､繧ｹ] [{$organization->name}] {$subject}";
        $body = $this->buildOrgNotificationBody($device, $type, $organization);

        foreach ($orgEmails as $email) {
            if (in_array($email, $deviceEmails)) {
                continue;
            }
            $this->sendMailWithLog($device, $type, $email, $mailSubject, $body);
        }
    }

    /**
     * 繝｡繝ｼ繝ｫ騾∽ｿ｡ + 騾夂衍繝ｭ繧ｰ險倬鹸
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
     * SMS騾∽ｿ｡ + 騾夂衍繝ｭ繧ｰ險倬鹸
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
     * 繝・ヰ繧､繧ｹ蛟句挨縺ｫ險ｭ螳壹＆繧後※縺・ｋ騾夂衍繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ荳隕ｧ繧貞叙蠕・     */
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
     * SMS譛ｬ譁・ｒ逕滓・・育洒譁・ｼ・     */
    private function buildSmsBody(Device $device, string $type): string
    {
        $name = $device->nickname ?: $device->device_id;

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            return "縲舌∩縺ｾ繧ゅｊ繝・ヰ繧､繧ｹ縲捜$name}・嘴$hours}譎る俣莉･荳翫∽ｺｺ縺ｮ蜍輔″縺梧､懃衍縺輔ｌ縺ｦ縺・∪縺帙ｓ縲ゅ＃遒ｺ隱阪￥縺縺輔＞縲・;
        }

        if ($type === 'offline') {
            return "縲舌∩縺ｾ繧ゅｊ繝・ヰ繧､繧ｹ縲捜$name}・壹ョ繝舌う繧ｹ縺ｨ縺ｮ騾壻ｿ｡縺碁皮ｵｶ縺医※縺・∪縺吶る崕豎蛻・ｌ縺ｾ縺溘・髮ｻ豕｢迥ｶ豕√ｒ縺皮｢ｺ隱阪￥縺縺輔＞縲・;
        }

        return '';
    }

    /**
     * 繝・ヰ繧､繧ｹ蛟句挨騾夂衍縺ｮ譛ｬ譁・ｒ逕滓・
     */
    private function buildNotificationBody(Device $device, string $type): string
    {
        $name = $device->nickname ?: $device->device_id;
        $now = Carbon::now()->format('Y/m/d H:i');

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            $lastDetected = $device->last_human_detected_at
                ? $device->last_human_detected_at->format('Y/m/d H:i')
                : '荳肴・';

            return "縲先悴讀懃衍繧｢繝ｩ繝ｼ繝医曾n\n"
                . "繝・ヰ繧､繧ｹ: {$name}\n"
                . "譛邨よ､懃衍: {$lastDetected}\n"
                . "險ｭ螳夐明蛟､: {$hours}譎る俣\n"
                . "讀懃衍譎ょ綾: {$now}\n\n"
                . "{$hours}譎る俣莉･荳翫∽ｺｺ縺ｮ蜍輔″縺梧､懃衍縺輔ｌ縺ｦ縺・∪縺帙ｓ縲・n"
                . "縺皮｢ｺ隱阪ｒ縺企｡倥＞縺・◆縺励∪縺吶・;
        }

        if ($type === 'offline') {
            $lastReceived = $device->last_received_at
                ? $device->last_received_at->format('Y/m/d H:i')
                : '荳肴・';

            return "縲宣壻ｿ｡騾皮ｵｶ縲曾n\n"
                . "繝・ヰ繧､繧ｹ: {$name}\n"
                . "譛邨る壻ｿ｡: {$lastReceived}\n"
                . "讀懃衍譎ょ綾: {$now}\n\n"
                . "繝・ヰ繧､繧ｹ縺ｨ縺ｮ騾壻ｿ｡縺碁皮ｵｶ縺医※縺・∪縺吶・n"
                . "髮ｻ豎蛻・ｌ縺ｾ縺溘・髮ｻ豕｢迥ｶ豕√ｒ縺皮｢ｺ隱阪￥縺縺輔＞縲・;
        }

        return '';
    }

    /**
     * 邨・ｹ皮ｮ｡逅・・髄縺鷹夂衍縺ｮ譛ｬ譁・ｒ逕滓・
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
            $locationInfo .= "驛ｨ螻狗分蜿ｷ: {$roomNumber}\n";
        }
        if ($tenantName) {
            $locationInfo .= "蜈･螻・・錐: {$tenantName}\n";
        }

        if ($type === 'alert') {
            $hours = $device->alert_threshold_hours;
            $lastDetected = $device->last_human_detected_at
                ? $device->last_human_detected_at->format('Y/m/d H:i')
                : '荳肴・';

            return "縲先悴讀懃衍繧｢繝ｩ繝ｼ繝医曾n\n"
                . "邨・ｹ・ {$organization->name}\n"
                . "繝・ヰ繧､繧ｹ: {$name}\n"
                . $locationInfo
                . "譛邨よ､懃衍: {$lastDetected}\n"
                . "險ｭ螳夐明蛟､: {$hours}譎る俣\n"
                . "讀懃衍譎ょ綾: {$now}\n\n"
                . "{$hours}譎る俣莉･荳翫∽ｺｺ縺ｮ蜍輔″縺梧､懃衍縺輔ｌ縺ｦ縺・∪縺帙ｓ縲・n"
                . "縺皮｢ｺ隱阪ｒ縺企｡倥＞縺・◆縺励∪縺吶・n\n"
                . "邂｡逅・判髱｢縺九ｉ繝・ヰ繧､繧ｹ縺ｮ迥ｶ諷九ｒ遒ｺ隱阪〒縺阪∪縺吶・;
        }

        if ($type === 'offline') {
            $lastReceived = $device->last_received_at
                ? $device->last_received_at->format('Y/m/d H:i')
                : '荳肴・';

            return "縲宣壻ｿ｡騾皮ｵｶ縲曾n\n"
                . "邨・ｹ・ {$organization->name}\n"
                . "繝・ヰ繧､繧ｹ: {$name}\n"
                . $locationInfo
                . "譛邨る壻ｿ｡: {$lastReceived}\n"
                . "讀懃衍譎ょ綾: {$now}\n\n"
                . "繝・ヰ繧､繧ｹ縺ｨ縺ｮ騾壻ｿ｡縺碁皮ｵｶ縺医※縺・∪縺吶・n"
                . "髮ｻ豎蛻・ｌ縺ｾ縺溘・髮ｻ豕｢迥ｶ豕√ｒ縺皮｢ｺ隱阪￥縺縺輔＞縲・n\n"
                . "邂｡逅・判髱｢縺九ｉ繝・ヰ繧､繧ｹ縺ｮ迥ｶ諷九ｒ遒ｺ隱阪〒縺阪∪縺吶・;
        }

        return '';
    }
}
