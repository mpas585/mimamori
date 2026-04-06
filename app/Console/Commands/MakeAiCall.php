<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiCallLog;
use App\Models\Device;
use Twilio\Rest\Client as TwilioClient;

class MakeAiCall extends Command
{
    protected $signature = 'ai-call:make {device_id? : デバイスID（省略時は全対象デバイス）}';
    protected $description = 'AIコール発信';

    public function handle(): int
    {
        if ($this->argument('device_id')) {
            $devices = Device::where('device_id', $this->argument('device_id'))->get();
        } else {
            // voice_enabledのデバイスを対象
            $devices = Device::whereHas('notificationSetting', function ($q) {
                $q->where('voice_enabled', true)
                  ->whereNotNull('voice_phone_1');
            })->where('status', '!=', 'inactive')->get();
        }

        if ($devices->isEmpty()) {
            $this->info('対象デバイスなし');
            return Command::SUCCESS;
        }

        $twilio   = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
        $twimlUrl = config('app.url') . '/api/ai-call/twiml';
        $statusUrl = config('app.url') . '/api/ai-call/status-webhook';

        foreach ($devices as $device) {
            $notif = $device->notificationSetting;
            if (!$notif || empty($notif->voice_phone_1)) {
                continue;
            }

            // ログ作成（発信前）
            $log = AiCallLog::create([
                'device_id'   => $device->id,
                'call_status' => 'failed',
                'called_at'   => now(),
            ]);

            try {
                $call = $twilio->calls->create(
                    $notif->voice_phone_1,
                    config('services.twilio.from'),
                    [
                        'url'                  => $twimlUrl,
                        'statusCallback'       => $statusUrl,
                        'statusCallbackMethod' => 'POST',
                        'statusCallbackEvent'  => ['completed', 'no-answer', 'busy', 'failed'],
                        'timeout'              => 30,
                        'machineDetection'     => 'Enable',
                    ]
                );

                $log->update(['call_sid' => $call->sid]);
                $this->info("発信: {$device->device_id} → {$notif->voice_phone_1} ({$call->sid})");

            } catch (\Exception $e) {
                $log->update(['error_message' => mb_substr($e->getMessage(), 0, 500)]);
                $this->error("発信失敗: {$device->device_id} - {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}


