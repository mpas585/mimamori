<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BillingContract;
use App\Models\BillingLog;
use App\Models\Organization;

class BillingController extends Controller
{
    /**
     * 課金管理画面
     */
    public function index()
    {
        $contracts = BillingContract::with(['organization', 'logs' => function ($q) {
            $q->orderByDesc('billed_at')->limit(5);
        }])->orderByDesc('id')->paginate(20);

        return view('billing.index', compact('contracts'));
    }

    /**
     * カード登録 + 契約作成 + 初月即時課金
     */
    public function store(Request $request)
    {
        $request->validate([
            'payjp_token'          => 'required|string',
            'organization_id'      => 'nullable|exists:organizations,id',
            'device_count'         => 'required|integer|min:1|max:999',
            'premium_device_count' => 'required|integer|min:0|max:999',
        ]);

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $orgName = $request->organization_id
                ? Organization::find($request->organization_id)?->name
                : 'individual';

            // Customer 作成
            $customer = \Payjp\Customer::create([
                'card'        => $request->payjp_token,
                'description' => 'みまもりデバイス - ' . $orgName,
                'metadata'    => ['organization_id' => $request->organization_id ?? 'none'],
            ]);

            // 金額計算
            $amount = ($request->device_count * 1000)
                    + ($request->premium_device_count * 500);

            // 初月即時課金
            $charge = \Payjp\Charge::create([
                'amount'      => $amount,
                'currency'    => 'jpy',
                'customer'    => $customer->id,
                'description' => "みまもりデバイス 月額利用料（初月）- {$orgName} 本体{$request->device_count}台 プレミアム{$request->premium_device_count}台",
            ]);

            // BillingContract 作成
            $contract = BillingContract::create([
                'organization_id'      => $request->organization_id,
                'payjp_customer_id'    => $customer->id,
                'device_count'         => $request->device_count,
                'premium_device_count' => $request->premium_device_count,
                'unit_price'           => 1000,
                'premium_unit_price'   => 500,
                'amount'               => $amount,
                'status'               => 'active',
                'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
            ]);

            // 課金ログ
            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $amount,
                'device_count'         => $request->device_count,
                'premium_device_count' => $request->premium_device_count,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            return response()->json([
                'ok'      => true,
                'message' => '契約を登録しました（初月 ¥' . number_format($amount) . ' を課金しました）',
                'amount'  => $amount,
            ]);

        } catch (\Exception $e) {
            Log::error('BillingController store error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '登録に失敗しました: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 台数変更
     */
    public function update(Request $request, BillingContract $contract)
    {
        $request->validate([
            'device_count'         => 'required|integer|min:1|max:999',
            'premium_device_count' => 'required|integer|min:0|max:999',
        ]);

        $contract->update([
            'device_count'         => $request->device_count,
            'premium_device_count' => $request->premium_device_count,
        ]);

        $contract->recalculate();

        return response()->json([
            'ok'     => true,
            'amount' => $contract->amount,
        ]);
    }

    /**
     * 解約
     */
    public function cancel(BillingContract $contract)
    {
        $contract->update([
            'status'      => 'canceled',
            'canceled_at' => now(),
        ]);

        return response()->json(['ok' => true, 'message' => '解約しました']);
    }

    /**
     * カード変更
     */
    public function updateCard(Request $request, BillingContract $contract)
    {
        $request->validate(['payjp_token' => 'required|string']);

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $customer = \Payjp\Customer::retrieve($contract->payjp_customer_id);
            $customer->cards->create(['card' => $request->payjp_token]);

            return response()->json(['ok' => true, 'message' => 'カードを更新しました']);

        } catch (\Exception $e) {
            Log::error('BillingController updateCard error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'カード更新に失敗しました'], 500);
        }
    }

    /**
     * 即時課金（テスト・手動実行用）
     */
    public function chargeNow(BillingContract $contract)
    {
        \App\Jobs\MonthlyBillingJob::dispatchSync($contract->id);
        return response()->json(['ok' => true, 'message' => '課金を実行しました']);
    }
}
