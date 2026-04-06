<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiCallLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCallController extends Controller
{
    /**
     * TwiML: ガイダンス再生 + 録音
     * GET/POST /api/ai-call/twiml
     */
    public function twiml(Request $request)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Response>'
            . '<Say language="ja-JP" voice="Polly.Mizuki">'
            . 'こちらは見守りシステムの自動確認サービスです。'
            . 'ピーという音の後に、今日のご体調やお気持ちをお話しください。'
            . '30秒以内でお話しいただけます。'
            . '</Say>'
            . '<Record maxLength="30" timeout="5" transcribe="false" '
            . 'recordingStatusCallback="' . config('app.url') . '/api/ai-call/recording-webhook" '
            . 'recordingStatusCallbackMethod="POST"/>'
            . '<Say language="ja-JP" voice="Polly.Mizuki">ありがとうございました。</Say>'
            . '</Response>';

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * 通話ステータスWebhook（発信結果）
     * POST /api/ai-call/status-webhook
     */
    public function statusWebhook(Request $request)
    {
        $callSid    = $request->input('CallSid');
        $callStatus = $request->input('CallStatus'); // completed, no-answer, busy, failed
        $duration   = $request->input('CallDuration');

        $log = AiCallLog::where('call_sid', $callSid)->first();
        if (!$log) {
            return response('', 204);
        }

        if (in_array($callStatus, ['no-answer', 'busy'])) {
            $log->update([
                'call_status' => 'no_answer',
                'duration_sec' => 0,
            ]);
            // 不在通知を送信
            $this->sendNoAnswerNotification($log->device);
        } elseif ($callStatus === 'failed') {
            $log->update([
                'call_status' => 'failed',
                'error_message' => 'Twilio call failed',
            ]);
        }

        return response('', 204);
    }

    /**
     * 録音完了Webhook
     * POST /api/ai-call/recording-webhook
     */
    public function recordingWebhook(Request $request)
    {
        $callSid      = $request->input('CallSid');
        $recordingSid = $request->input('RecordingSid');
        $duration     = $request->input('RecordingDuration');
        $recordingUrl = $request->input('RecordingUrl');

        $log = AiCallLog::where('call_sid', $callSid)->first();
        if (!$log) {
            Log::warning('AiCallLog not found for CallSid: ' . $callSid);
            return response('', 204);
        }

        $log->update([
            'recording_sid' => $recordingSid,
            'duration_sec'  => $duration,
            'call_status'   => 'completed',
        ]);

        // 録音URLからMP3取得 → GPT-4o Audioで文字起こし＋判定
        try {
            $mp3Url  = $recordingUrl . '.mp3';
            $authStr = base64_encode(config('services.twilio.sid') . ':' . config('services.twilio.token'));

            // TwilioからMP3をダウンロード
            $mp3Response = Http::withHeaders(['Authorization' => 'Basic ' . $authStr])
                ->timeout(30)
                ->get($mp3Url);

            if (!$mp3Response->successful()) {
                throw new \Exception('録音DL失敗: HTTP ' . $mp3Response->status());
            }

            $mp3Data = $mp3Response->body();

            // GPT-4o Audioで文字起こし＋判定
            $result = $this->analyzeWithGpt($mp3Data);

            $log->update([
                'transcript'   => $result['transcript'],
                'judgment'     => $result['judgment'],
                'gpt_response' => $result['reason'],
            ]);

            // 要確認・異常の場合は通知
            if (in_array($result['judgment'], ['check', 'alert'])) {
                $this->sendJudgmentNotification($log->device, $result['judgment'], $result['transcript']);
            }

        } catch (\Exception $e) {
            Log::error('AiCall recording process failed: ' . $e->getMessage());
            $log->update([
                'call_status'   => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 500),
            ]);
        }

        return response('', 204);
    }

    /**
     * GPT-4o Audioで文字起こし＋判定
     */
    private function analyzeWithGpt(string $mp3Data): array
    {
        $base64Audio = base64_encode($mp3Data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type'  => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-audio-preview',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => implode("\n", [
                        'あなたは高齢者の安否確認AIです。',
                        '音声を聞いて以下を行ってください：',
                        '1. 音声をそのまま文字起こしする',
                        '2. 以下の基準で安否を判定する',
                        '   - good: 元気そう・普通の受け答えができている',
                        '   - check: 元気がなさそう・不安な発言がある・受け答えが不自然',
                        '   - alert: 助けを求めている・痛みを訴えている・意識が混濁している',
                        '   - unclear: 無音・雑音のみ・判定不能',
                        '必ずJSON形式で返してください：',
                        '{"transcript":"文字起こし内容","judgment":"good/check/alert/unclear","reason":"判定理由"}',
                        'JSONのみ返してください。マークダウンは不要です。',
                    ]),
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_audio',
                            'input_audio' => [
                                'data'   => $base64Audio,
                                'format' => 'mp3',
                            ],
                        ],
                    ],
                ],
            ],
            'modalities' => ['text'],
        ]);

        if (!$response->successful()) {
            throw new \Exception('GPT API error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        $parsed  = json_decode($content, true);

        if (!$parsed || !isset($parsed['judgment'])) {
            throw new \Exception('GPT response parse error: ' . $content);
        }

        return [
            'transcript' => $parsed['transcript'] ?? '',
            'judgment'   => $parsed['judgment'] ?? 'unclear',
            'reason'     => $parsed['reason'] ?? '',
        ];
    }

    /**
     * 不在通知
     */
    private function sendNoAnswerNotification(Device $device): void
    {
        $name = $device->nickname ?: $device->device_id;
        $notif = $device->notificationSetting;
        if (!$notif) {
            return;
        }

        $subject = "[みまもりデバイス] 電話に出ませんでした";
        $body    = "【不在のお知らせ】\n\n"
            . "デバイス: {$name}\n"
            . "日時: " . now()->format('Y/m/d H:i') . "\n\n"
            . "自動音声確認の電話に出ませんでした。\n"
            . "お早めにご確認ください。";

        if ($notif->email_enabled) {
            foreach (['email_1', 'email_2', 'email_3'] as $field) {
                if (!empty($notif->$field)) {
                    \Illuminate\Support\Facades\Mail::raw($body, function ($m) use ($notif, $field, $subject) {
                        $m->to($notif->$field)->subject($subject);
                    });
                }
            }
        }

        if ($notif->sms_enabled) {
            $smsBody = "【みまもりデバイス】{$name}：自動確認の電話に出ませんでした。ご確認ください。";
            $twilio  = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
            foreach (['sms_phone_1', 'sms_phone_2'] as $field) {
                if (!empty($notif->$field)) {
                    $twilio->messages->create($notif->$field, [
                        'from' => config('services.twilio.from'),
                        'body' => $smsBody,
                    ]);
                }
            }
        }
    }

    /**
     * 判定結果通知（要確認・異常）
     */
    private function sendJudgmentNotification(Device $device, string $judgment, string $transcript): void
    {
        $name   = $device->nickname ?: $device->device_id;
        $notif  = $device->notificationSetting;
        if (!$notif) {
            return;
        }

        $label   = $judgment === 'alert' ? '異常' : '要確認';
        $subject = "[みまもりデバイス] AIコール判定：{$label}";
        $body    = "【AIコール判定：{$label}】\n\n"
            . "デバイス: {$name}\n"
            . "日時: " . now()->format('Y/m/d H:i') . "\n"
            . "判定: {$label}\n"
            . "発言内容: {$transcript}\n\n"
            . "お早めにご確認ください。";

        if ($notif->email_enabled) {
            foreach (['email_1', 'email_2', 'email_3'] as $field) {
                if (!empty($notif->$field)) {
                    \Illuminate\Support\Facades\Mail::raw($body, function ($m) use ($notif, $field, $subject) {
                        $m->to($notif->$field)->subject($subject);
                    });
                }
            }
        }

        if ($notif->sms_enabled) {
            $smsBody = "【みまもりデバイス】{$name}：AIコール判定「{$label}」。発言：{$transcript}";
            $twilio  = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
            foreach (['sms_phone_1', 'sms_phone_2'] as $field) {
                if (!empty($notif->$field)) {
                    $twilio->messages->create($notif->$field, [
                        'from' => config('services.twilio.from'),
                        'body' => mb_substr($smsBody, 0, 140),
                    ]);
                }
            }
        }
    }
}


