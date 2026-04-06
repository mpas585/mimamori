<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BillingContract;
use App\Models\Organization;

class BillingController extends Controller
{
    /**
     * 課金管理画面（組織一覧）
     * partner管理者向け
     */
    public function index()
    {
        $contracts = BillingContract::with(['organization', 'logs' => function ($q) {
            $q->orderByDesc('billed_at')->limit(5);
        }])->orderByDesc('id')->paginate(20);

        return view('billing.index', compact('contracts'));
    }

    /**
     * カード登録 + 契約作成
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
            // Customer 作成
            $orgName = $request->organization_id
                ? Organization::find($request->organization_id)?->name
                : 'individual';

            $customer = \Payjp\Customer::create([
                'card'        => $request->payjp_token,
                'description' => $orgName,
                'metadata'    => ['organization_id' => $request->organization_id ?? 'none'],
            ]);

            $contract = BillingContract::create([
                'organization_id'      => $request->organization_id,
                'payjp_customer_id'    => $customer->id,
                'device_count'         => $request->device_count,
                'premium_device_count' => $request->premium_device_count,
                'unit_price'           => 1000,
                'premium_unit_price'   => 500,
                'amount'               => 0,
                'status'               => 'active',
                'next_billing_date'    => now()->addMonth()->startOfMonth()->toDateString(),
            ]);

            $contract->recalculate();

            return response()->json([
                'ok'      => true,
                'message' => '契約を登録しました',
                'amount'  => $contract->amount,
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
     * 即時課金（テスト用）
     */
    public function chargeNow(BillingContract $contract)
    {
        \App\Jobs\MonthlyBillingJob::dispatchSync($contract->id);
        return response()->json(['ok' => true, 'message' => '課金を実行しました']);
    }
}
