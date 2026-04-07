<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\BillingContract;
use App\Models\BillingLog;

class PlanController extends Controller
{
    // ============================================================
    // プランページ表示
    // ============================================================
    public function index()
    {
        $device = Auth::user();
        $subscription = $device->subscription;
        $billingContract = BillingContract::where('organization_id', null)
            ->whereHas('logs', function ($q) use ($device) {
                // device_idで紐付けられないのでsubscriptionsのpayjp_customer_idと一致するものを探す
            })
            ->first();

        // subscriptionsのstripe_customer_idからBillingContractを取得
        if ($subscription?->stripe_customer_id) {
            $billingContract = BillingContract::where('payjp_customer_id', $subscription->stripe_customer_id)->first();
        } else {
            $billingContract = null;
        }

        return view('plan', compact('device', 'subscription', 'billingContract'));
    }

    // ============================================================
    // プレミアム購読開始（B2C）
    // 初月即時課金 → BillingContract作成 → 翌月以降はJobが実行
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
            $amount = 500; // B2Cはプレミアムオプション¥500固定

            // Customer 作成 or 既存取得
            if ($subscription?->stripe_customer_id) {
                $customer = \Payjp\Customer::retrieve($subscription->stripe_customer_id);
                $customer->cards->create(['card' => $request->payjp_token]);
            } else {
                $customer = \Payjp\Customer::create([
                    'card'        => $request->payjp_token,
                    'description' => 'みまもりデバイス - ' . $device->device_id,
                    'metadata'    => ['device_id' => $device->device_id],
                ]);
            }

            // 初月即時課金
            $charge = \Payjp\Charge::create([
                'amount'      => $amount,
                'currency'    => 'jpy',
                'customer'    => $customer->id,
                'description' => 'みまもりデバイス プレミアムプラン（初月）',
            ]);

            // BillingContract 作成（翌月以降の自動課金用）
            $contract = BillingContract::updateOrCreate(
                ['payjp_customer_id' => $customer->id],
                [
                    'organization_id'      => null,
                    'device_count'         => 0,
                    'premium_device_count' => 1,
                    'unit_price'           => 1000,
                    'premium_unit_price'   => 500,
                    'amount'               => $amount,
                    'status'               => 'active',
                    'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
                ]
            );

            // 課金ログ
            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => 0,
                'premium_device_count' => 1,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            // Subscriptions テーブル更新（プレミアム状態管理）
            Subscription::updateOrCreate(
                ['device_id' => $device->id],
                [
                    'plan'                   => 'premium',
                    'billing_cycle'          => 'monthly',
                    'stripe_customer_id'     => $customer->id,
                    'stripe_subscription_id' => null,
                    'current_period_start'   => now()->toDateString(),
                    'current_period_end'     => now()->addMonth()->startOfMonth()->toDateString(),
                    'status'                 => 'active',
                    'canceled_at'            => null,
                ]
            );

            $device->update(['premium_enabled' => true]);

            return response()->json([
                'ok'      => true,
                'message' => 'プレミアムプランを開始しました（¥500を課金しました）',
            ]);

        } catch (\Exception $e) {
            Log::error('PlanController subscribe error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '決済処理に失敗しました: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // 解約（当月末まで有効）
    // ============================================================
    public function cancel(Request $request)
    {
        $device = Auth::user();
        $subscription = $device->subscription;

        if (!$subscription || !$device->premium_enabled) {
            return response()->json(['ok' => false, 'message' => 'プレミアムプランではありません'], 422);
        }

        try {
            // BillingContractをキャンセル
            if ($subscription->stripe_customer_id) {
                $contract = BillingContract::where('payjp_customer_id', $subscription->stripe_customer_id)->first();
                $contract?->update([
                    'status'      => 'canceled',
                    'canceled_at' => now(),
                ]);
            }

            // Subscriptionをキャンセル（当月末まで有効）
            $subscription->update([
                'status'      => 'canceled',
                'canceled_at' => now(),
            ]);

            // premium_enabled は current_period_end まで true のまま
            // MonthlyBillingJob はキャンセル済みなのでスキップされる

            $endDate = $subscription->current_period_end?->format('Y年n月j日') ?? '今月末';

            return response()->json([
                'ok'      => true,
                'message' => '解約しました。' . $endDate . 'まで引き続きご利用いただけます。',
                'end_date' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('PlanController cancel error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '解約処理に失敗しました'], 500);
        }
    }

    // ============================================================
    // Pay.jp Webhook 受信（charge.failed のみ対応）
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

        if ($type === 'charge.failed') {
            $customerId = $event['data']['object']['customer'] ?? null;
            if ($customerId) {
                $contract = BillingContract::where('payjp_customer_id', $customerId)->first();
                $contract?->update(['status' => 'past_due']);

                $sub = Subscription::where('stripe_customer_id', $customerId)->first();
                $sub?->update(['status' => 'past_due']);

                Log::warning('Payjp charge failed for customer: ' . $customerId);
            }
        }

        return response('OK', 200);
    }
}
