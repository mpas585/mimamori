<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Device;
use App\Models\Subscription;
use App\Models\BillingContract;
use App\Models\BillingLog;

class PlanController extends Controller
{
    public function index()
    {
        $device = Auth::user();
        $subscription = $device->subscription;

        if ($subscription?->stripe_customer_id) {
            $billingContract = BillingContract::where('payjp_customer_id', $subscription->stripe_customer_id)->first();
        } else {
            $billingContract = null;
        }

        return view('plan', compact('device', 'subscription', 'billingContract'));
    }

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
            $amount = 500;

            // メールアドレス取得：パートナー紐づきがあればオペレーターのログインメール、なければ通知設定のemail_1
            $email = null;
            if ($device->organization_id) {
                $email = $device->organization?->partnerUsers()->where('role', 'operator')->first()?->email;
            } else {
                $email = $device->notificationSetting?->email_1;
            }

            if ($subscription?->stripe_customer_id) {
                $customer = \Payjp\Customer::retrieve($subscription->stripe_customer_id);
                $customer->cards->create(['card' => $request->payjp_token]);
            } else {
                $customer = \Payjp\Customer::create([
                    'card'        => $request->payjp_token,
                    'email'       => $email,
                    'description' => 'みまもりデバイス - ' . $device->device_id,
                    'metadata'    => ['device_id' => $device->device_id],
                ]);
            }

            $charge = \Payjp\Charge::create([
                'amount'         => $amount,
                'currency'       => 'jpy',
                'customer'       => $customer->id,
                'description'    => 'みまもりデバイス プレミアムプラン（初月）',
                'three_d_secure' => true,
                'tds_finish_url' => route('plan.tds-complete') . '?customer=' . $customer->id,
            ]);

            if (isset($charge->three_d_secure_status) && $charge->three_d_secure_status === 'unverified') {
                BillingContract::updateOrCreate(
                    ['payjp_customer_id' => $customer->id],
                    [
                        'organization_id'      => null,
                        'device_count'         => 0,
                        'premium_device_count' => 1,
                        'unit_price'           => 1000,
                        'premium_unit_price'   => 500,
                        'amount'               => $amount,
                        'status'               => 'pending',
                        'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
                        'payjp_charge_id'      => $charge->id,
                    ]
                );

                Subscription::updateOrCreate(
                    ['device_id' => $device->id],
                    [
                        'plan'                   => 'premium',
                        'billing_cycle'          => 'monthly',
                        'stripe_customer_id'     => $customer->id,
                        'stripe_subscription_id' => null,
                        'current_period_start'   => now()->toDateString(),
                        'current_period_end'     => now()->addMonth()->startOfMonth()->toDateString(),
                        'status'                 => 'pending',
                        'canceled_at'            => null,
                    ]
                );

                return response()->json([
                    'ok'          => false,
                    'tds'         => true,
                    'redirect_to' => $charge->redirect_to,
                ]);
            }

            return $this->completeSubscription($device, $customer->id, $charge, $amount);

        } catch (\Exception $e) {
            Log::error('PlanController subscribe error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '決済処理に失敗しました: ' . $e->getMessage()], 500);
        }
    }

    public function tdsComplete(Request $request)
    {
        $customerId = $request->input('customer');
        if (!$customerId) {
            return redirect('/plan')->with('error', '3Dセキュア認証に失敗しました');
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $contract = BillingContract::where('payjp_customer_id', $customerId)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$contract || !$contract->payjp_charge_id) {
                return redirect('/plan')->with('error', '契約情報が見つかりません');
            }

            $charge = \Payjp\Charge::retrieve($contract->payjp_charge_id);

            if ($charge->three_d_secure_status !== 'verified') {
                Subscription::where('stripe_customer_id', $customerId)
                    ->where('status', 'pending')
                    ->delete();
                $contract->delete();
                return redirect('/plan')->with('error', '3Dセキュア認証が完了していません');
            }

            $sub = Subscription::where('stripe_customer_id', $customerId)->first();
            if (!$sub) {
                return redirect('/plan')->with('error', 'サブスクリプション情報が見つかりません');
            }

            $device = Device::find($sub->device_id);

            $contract->update(['status' => 'active', 'payjp_charge_id' => null]);

            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $contract->amount,
                'device_count'         => 0,
                'premium_device_count' => 1,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            $sub->update(['status' => 'active']);
            $device?->update(['premium_enabled' => true]);

            return redirect('/plan')
                ->with('success', 'プレミアムプランを開始しました（¥500を課金しました）');

        } catch (\Exception $e) {
            Log::error('PlanController tdsComplete error: ' . $e->getMessage());
            return redirect('/plan')->with('error', '3Dセキュア処理に失敗しました');
        }
    }

    private function completeSubscription($device, $customerId, $charge, $amount)
    {
        $contract = BillingContract::updateOrCreate(
            ['payjp_customer_id' => $customerId],
            [
                'organization_id'      => null,
                'device_count'         => 0,
                'premium_device_count' => 1,
                'unit_price'           => 1000,
                'premium_unit_price'   => 500,
                'amount'               => $amount,
                'status'               => 'active',
                'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
                'payjp_charge_id'      => null,
            ]
        );

        BillingLog::create([
            'billing_contract_id'  => $contract->id,
            'amount'               => $amount,
            'device_count'         => 0,
            'premium_device_count' => 1,
            'payjp_charge_id'      => $charge->id,
            'status'               => 'success',
            'billed_at'            => now(),
        ]);

        Subscription::updateOrCreate(
            ['device_id' => $device->id],
            [
                'plan'                   => 'premium',
                'billing_cycle'          => 'monthly',
                'stripe_customer_id'     => $customerId,
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
    }

    public function cancel(Request $request)
    {
        $device = Auth::user();
        $subscription = $device->subscription;

        if (!$subscription || !$device->premium_enabled) {
            return response()->json(['ok' => false, 'message' => 'プレミアムプランではありません'], 422);
        }

        try {
            if ($subscription->stripe_customer_id) {
                $contract = BillingContract::where('payjp_customer_id', $subscription->stripe_customer_id)->first();
                $contract?->update([
                    'status'      => 'canceled',
                    'canceled_at' => now(),
                ]);
            }

            $subscription->update([
                'status'      => 'canceled',
                'canceled_at' => now(),
            ]);

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
