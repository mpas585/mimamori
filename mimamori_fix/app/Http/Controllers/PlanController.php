<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;

class PlanController extends Controller
{
    // ============================================================
    // プランページ表示
    // ============================================================
    public function index()
    {
        $device = Auth::user();
        $subscription = $device->subscription;

        return view('plan', compact('device', 'subscription'));
    }

    // ============================================================
    // プレミアム購読開始
    // フロント: payjp.js でカードトークン取得 → POST
    // ============================================================
    public function subscribe(Request $request)
    {
        $request->validate([
            'payjp_token' => 'required|string',
        ]);

        $device = Auth::user();

        if ($device->premium_enabled) {
            return response()->json(['ok' => false, 'message' => 'すでにプレミアムです'], 422);
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $subscription = $device->subscription;

            // Customer 作成 or 既存取得
            if ($subscription && $subscription->stripe_customer_id) {
                $customer = \Payjp\Customer::retrieve($subscription->stripe_customer_id);
                $customer->cards->create(['card' => $request->payjp_token]);
            } else {
                $customer = \Payjp\Customer::create([
                    'card'        => $request->payjp_token,
                    'description' => $device->device_id,
                    'metadata'    => ['device_id' => $device->device_id],
                ]);
            }

            // Subscription 作成
            $payjpSub = \Payjp\Subscription::create([
                'customer' => $customer->id,
                'plan'     => config('services.payjp.plan_id_monthly'),
            ]);

            // DB 更新
            Subscription::updateOrCreate(
                ['device_id' => $device->id],
                [
                    'plan'                   => 'premium',
                    'billing_cycle'          => 'monthly',
                    'stripe_customer_id'     => $customer->id,
                    'stripe_subscription_id' => $payjpSub->id,
                    'current_period_start'   => now()->toDateString(),
                    'current_period_end'     => now()->addMonth()->toDateString(),
                    'status'                 => 'active',
                    'canceled_at'            => null,
                ]
            );

            $device->update(['premium_enabled' => true]);

            return response()->json(['ok' => true, 'message' => 'プレミアムプランを開始しました']);

        } catch (\Payjp\Error\Card $e) {
            Log::error('Payjp card error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'カードエラー: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Payjp subscribe error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '決済処理に失敗しました'], 500);
        }
    }

    // ============================================================
    // 解約（期間終了まで有効）
    // ============================================================
    public function cancel(Request $request)
    {
        $device = Auth::user();
        $subscription = $device->subscription;

        if (!$subscription || !$device->premium_enabled) {
            return response()->json(['ok' => false, 'message' => 'プレミアムプランではありません'], 422);
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            if ($subscription->stripe_subscription_id) {
                $payjpSub = \Payjp\Subscription::retrieve($subscription->stripe_subscription_id);
                $payjpSub->cancel(['prorate' => false]);
            }

            $subscription->update([
                'status'      => 'canceled',
                'canceled_at' => now(),
            ]);

            return response()->json([
                'ok'      => true,
                'message' => '解約しました。' . $subscription->current_period_end->format('Y年n月j日') . 'まで引き続きご利用いただけます。',
                'end_date' => $subscription->current_period_end->format('Y年n月j日'),
            ]);

        } catch (\Exception $e) {
            Log::error('Payjp cancel error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '解約処理に失敗しました'], 500);
        }
    }

    // ============================================================
    // Pay.jp Webhook 受信
    // ============================================================
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('X-Payjp-Signature');
        $secret  = config('services.payjp.webhook_secret');

        if ($secret) {
            $expected = hash_hmac('sha256', $payload, $secret);
            if (!hash_equals($expected, $sig ?? '')) {
                Log::warning('Payjp webhook signature mismatch');
                return response('Signature mismatch', 400);
            }
        }

        $event = json_decode($payload, true);
        $type  = $event['type'] ?? '';

        Log::info('Payjp webhook: ' . $type);

        switch ($type) {
            case 'charge.succeeded':
                $this->handleChargeSucceeded($event);
                break;
            case 'charge.failed':
                $this->handleChargeFailed($event);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event);
                break;
        }

        return response('OK', 200);
    }

    private function handleChargeSucceeded(array $event): void
    {
        $customerId = $event['data']['object']['customer'] ?? null;
        if (!$customerId) return;
        $sub = Subscription::where('stripe_customer_id', $customerId)->first();
        if (!$sub) return;
        $sub->update(['status' => 'active']);
        $sub->device->update(['premium_enabled' => true]);
    }

    private function handleChargeFailed(array $event): void
    {
        $customerId = $event['data']['object']['customer'] ?? null;
        if (!$customerId) return;
        $sub = Subscription::where('stripe_customer_id', $customerId)->first();
        if (!$sub) return;
        $sub->update(['status' => 'past_due']);
        Log::warning('Payjp charge failed for customer: ' . $customerId);
    }

    private function handleSubscriptionUpdated(array $event): void
    {
        $payjpSubId = $event['data']['object']['id'] ?? null;
        if (!$payjpSubId) return;
        $sub = Subscription::where('stripe_subscription_id', $payjpSubId)->first();
        if (!$sub) return;
        $periodEnd = isset($event['data']['object']['current_period_end'])
            ? date('Y-m-d', $event['data']['object']['current_period_end'])
            : null;
        $sub->update([
            'status'             => 'active',
            'current_period_end' => $periodEnd ?? $sub->current_period_end,
        ]);
    }

    private function handleSubscriptionDeleted(array $event): void
    {
        $payjpSubId = $event['data']['object']['id'] ?? null;
        if (!$payjpSubId) return;
        $sub = Subscription::where('stripe_subscription_id', $payjpSubId)->first();
        if (!$sub) return;
        $sub->update(['status' => 'canceled']);
        $sub->device->update(['premium_enabled' => false]);
    }
}
