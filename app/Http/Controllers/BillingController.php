<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\BillingContract;
use App\Models\BillingLog;
use App\Models\Organization;

class BillingController extends Controller
{
    /**
     * ログイン中パートナーユーザーを取得
     */
    private function authUser()
    {
        return Auth::guard('partner')->user();
    }

    /**
     * operatorの場合は自組織の契約か確認。違えばabort(403)
     */
    private function authorizeContract(BillingContract $contract): void
    {
        $user = $this->authUser();
        if ($user->isOperator() && $contract->organization_id !== $user->organization_id) {
            abort(403);
        }
    }

    /**
     * 課金管理画面
     */
    public function index()
    {
        $user = $this->authUser();

        $query = BillingContract::with(['organization', 'logs' => function ($q) {
            $q->orderByDesc('billed_at')->limit(5);
        }]);

        // operatorは自組織の契約のみ表示
        if ($user->isOperator()) {
            $query->where('organization_id', $user->organization_id);
        }

        $contracts = $query->orderByDesc('id')->paginate(20);

        return view('billing.index', compact('contracts'));
    }

    /**
     * カード登録 + 契約作成 + 初月即時課金（3Dセキュア対応）
     */
    public function store(Request $request)
    {
        $user = $this->authUser();

        $request->validate([
            'payjp_token'          => 'required|string',
            'organization_id'      => 'nullable|exists:organizations,id',
            'device_count'         => 'required|integer|min:1|max:999',
            'premium_device_count' => 'required|integer|min:0|max:999',
        ]);

        // operatorは自組織のみ登録可能（リクエストの organization_id を上書き）
        $organizationId = $user->isOperator()
            ? $user->organization_id
            : $request->organization_id;

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $orgName = $organizationId
                ? Organization::find($organizationId)?->name
                : 'individual';

            // ログイン中のパートナーユーザーのメアドを取得
            $email = $user->email ?? null;

            // Customer 作成（メアド付き）
            $customer = \Payjp\Customer::create([
                'card'        => $request->payjp_token,
                'email'       => $email,
                'description' => 'みまもりデバイス - ' . $orgName,
                'metadata'    => ['organization_id' => $organizationId ?? 'none'],
            ]);

            // 金額計算（ベース¥1000/台 + AIコール¥300/台）
            $amount = ($request->device_count * 1000)
                    + ($request->premium_device_count * 300);

            // 3Dセキュア付き初月即時課金
            $charge = \Payjp\Charge::create([
                'amount'         => $amount,
                'currency'       => 'jpy',
                'customer'       => $customer->id,
                'description'    => "みまもりデバイス 月額利用料（初月）- {$orgName} 本体{$request->device_count}台 AIコール{$request->premium_device_count}台",
                'three_d_secure' => true,
                'tds_finish_url' => route('partner.billing.tds-complete', ['customer' => $customer->id]),
            ]);

            // 3Dセキュア認証が必要な場合 → クライアントにリダイレクト先を返す
            if (isset($charge->three_d_secure_status) && $charge->three_d_secure_status === 'unverified') {
                // BillingContractを一時保存（未確定状態）
                BillingContract::create([
                    'organization_id'   => $organizationId,
                    'payjp_customer_id' => $customer->id,
                    'device_count'      => $request->device_count,
                    'unit_price'        => 1000,
                    'amount'            => $amount,
                    'status'            => 'pending',
                    'next_billing_date' => now()->addMonth()->startOfMonth()->toDateString(),
                    'payjp_charge_id'   => $charge->id,
                ]);

                return response()->json([
                    'ok'          => false,
                    'tds'         => true,
                    'redirect_to' => $charge->redirect_to,
                ]);
            }

            // 3DS不要 or 認証済み → 通常完了
            return $this->completeContract(
                $customer->id, $charge,
                $organizationId, $request->device_count, $amount
            );

        } catch (\Exception $e) {
            Log::error('BillingController store error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => '登録に失敗しました: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 3Dセキュア認証完了コールバック
     */
    public function tdsComplete(Request $request)
    {
        $customerId = $request->input('customer');
        if (!$customerId) {
            return redirect('/partner/billing')->with('error', '3Dセキュア認証に失敗しました');
        }

        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            // 未確定のBillingContractを取得
            $contract = BillingContract::where('payjp_customer_id', $customerId)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$contract || !$contract->payjp_charge_id) {
                return redirect('/partner/billing')->with('error', '契約情報が見つかりません');
            }

            // Chargeを取得して3DS完了を確認
            $charge = \Payjp\Charge::retrieve($contract->payjp_charge_id);

            if ($charge->three_d_secure_status !== 'verified') {
                $contract->delete();
                return redirect('/partner/billing')->with('error', '3Dセキュア認証が完了していません');
            }

            // 契約を確定
            $contract->update(['status' => 'active', 'payjp_charge_id' => null]);

            BillingLog::create([
                'billing_contract_id'  => $contract->id,
                'amount'               => $contract->amount,
                'device_count'         => $contract->device_count,
                'premium_device_count' => 0,
                'payjp_charge_id'      => $charge->id,
                'status'               => 'success',
                'billed_at'            => now(),
            ]);

            return redirect('/partner/billing')
                ->with('success', '契約を登録しました（初月 ¥' . number_format($contract->amount) . ' を課金しました）');

        } catch (\Exception $e) {
            Log::error('BillingController tdsComplete error: ' . $e->getMessage());
            return redirect('/partner/billing')->with('error', '3Dセキュア処理に失敗しました');
        }
    }

    /**
     * 契約完了処理（3DS不要の場合）
     */
    private function completeContract($customerId, $charge, $organizationId, $deviceCount, $amount)
    {
        $contract = BillingContract::create([
            'organization_id'   => $organizationId,
            'payjp_customer_id' => $customerId,
            'device_count'      => $deviceCount,
            'unit_price'        => 1000,
            'amount'            => $amount,
            'status'            => 'active',
            'next_billing_date' => now()->addMonth()->startOfMonth()->toDateString(),
        ]);

        BillingLog::create([
            'billing_contract_id'  => $contract->id,
            'amount'               => $amount,
            'device_count'         => $deviceCount,
            'premium_device_count' => 0,
            'payjp_charge_id'      => $charge->id,
            'status'               => 'success',
            'billed_at'            => now(),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => '契約を登録しました（初月 ¥' . number_format($amount) . ' を課金しました）',
            'amount'  => $amount,
        ]);
    }

    /**
     * 台数変更
     */
    public function update(Request $request, BillingContract $contract)
    {
        $this->authorizeContract($contract);

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
        $this->authorizeContract($contract);

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
        $this->authorizeContract($contract);

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
        $this->authorizeContract($contract);

        \App\Jobs\MonthlyBillingJob::dispatchSync($contract->id);
        return response()->json(['ok' => true, 'message' => '課金を実行しました']);
    }
}
