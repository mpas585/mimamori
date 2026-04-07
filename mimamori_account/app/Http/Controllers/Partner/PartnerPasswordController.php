<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\BillingContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PartnerPasswordController extends Controller
{
    /**
     * アカウント設定画面表示
     */
    public function showForm()
    {
        $admin = Auth::guard('partner')->user();
        $organization = $admin->organization;

        // カード情報取得
        $cardInfo = null;
        $contract = null;

        if ($organization) {
            $contract = BillingContract::where('organization_id', $organization->id)
                ->where('status', 'active')
                ->first();

            if ($contract?->payjp_customer_id) {
                try {
                    \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));
                    $customer = \Payjp\Customer::retrieve($contract->payjp_customer_id);
                    $card = $customer->cards->data[0] ?? null;
                    if ($card) {
                        $cardInfo = ['brand' => $card->brand, 'last4' => $card->last4];
                    }
                } catch (\Exception $e) {
                    Log::error('showForm card info error: ' . $e->getMessage());
                }
            }
        }

        return view('partner.password-change', compact('admin', 'organization', 'cardInfo', 'contract'));
    }

    /**
     * パスワード変更
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => '現在のパスワードを入力してください',
            'new_password.required'     => '新しいパスワードを入力してください',
            'new_password.min'          => '新しいパスワードは8文字以上にしてください',
            'new_password.confirmed'    => '新しいパスワードが一致しません',
        ]);

        $admin = Auth::guard('partner')->user();

        if (!Hash::check($request->current_password, $admin->password_hash)) {
            return back()->withErrors(['current_password' => '現在のパスワードが正しくありません'])->withInput();
        }

        if ($request->current_password === $request->new_password) {
            return back()->withErrors(['new_password' => '新しいパスワードは現在と異なるものにしてください'])->withInput();
        }

        $admin->update(['password_hash' => Hash::make($request->new_password)]);

        return back()->with('success', 'パスワードを変更しました');
    }

    /**
     * メールアドレス変更
     */
    public function updateEmail(Request $request)
    {
        $admin = Auth::guard('partner')->user();

        $request->validate([
            'email'          => 'required|email|unique:admin_users,email,' . $admin->id,
            'email_password' => 'required|string',
        ], [
            'email.required'          => 'メールアドレスを入力してください',
            'email.email'             => '正しいメールアドレスを入力してください',
            'email.unique'            => 'このメールアドレスは既に使用されています',
            'email_password.required' => 'パスワードを入力してください',
        ]);

        if (!Hash::check($request->email_password, $admin->password_hash)) {
            return back()->withErrors(['email_password' => 'パスワードが正しくありません'])->withInput();
        }

        if ($admin->email === $request->email) {
            return back()->withErrors(['email' => '現在と同じメールアドレスです'])->withInput();
        }

        $admin->update(['email' => $request->email]);

        return back()->with('success', 'メールアドレスを変更しました');
    }

    /**
     * クレジットカード変更
     */
    public function updateCard(Request $request)
    {
        $request->validate(['payjp_token' => 'required|string']);

        $admin = Auth::guard('partner')->user();
        $organization = $admin->organization;

        if (!$organization) {
            return response()->json(['ok' => false, 'message' => '組織が割り当てられていません'], 422);
        }

        $contract = BillingContract::where('organization_id', $organization->id)
            ->where('status', 'active')
            ->first();

        if (!$contract?->payjp_customer_id) {
            return response()->json(['ok' => false, 'message' => '契約情報が見つかりません'], 422);
        }

        try {
            \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));
            $customer = \Payjp\Customer::retrieve($contract->payjp_customer_id);
            $newCard  = $customer->cards->create(['card' => $request->payjp_token]);

            // デフォルトカードを新しいカードに切り替え
            $customer->default_card = $newCard->id;
            $customer->save();

            return response()->json(['ok' => true, 'message' => 'カードを変更しました']);

        } catch (\Exception $e) {
            Log::error('PartnerPasswordController updateCard error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'カード変更に失敗しました'], 500);
        }
    }

    /**
     * 配送先プリセット保存
     */
    public function updateDelivery(Request $request)
    {
        $request->validate([
            'delivery_name'    => 'required|string|max:100',
            'delivery_postal'  => 'required|string|max:10',
            'delivery_address' => 'required|string|max:500',
            'delivery_phone'   => 'required|string|max:20',
        ], [
            'delivery_name.required'    => '氏名を入力してください',
            'delivery_postal.required'  => '郵便番号を入力してください',
            'delivery_address.required' => '住所を入力してください',
            'delivery_phone.required'   => '電話番号を入力してください',
        ]);

        $admin = Auth::guard('partner')->user();
        $organization = $admin->organization;

        if (!$organization) {
            return back()->with('error', '組織が割り当てられていません');
        }

        $organization->update([
            'delivery_name'    => $request->delivery_name,
            'delivery_postal'  => $request->delivery_postal,
            'delivery_address' => $request->delivery_address,
            'delivery_phone'   => $request->delivery_phone,
        ]);

        return back()->with('success', '配送先を保存しました');
    }
}
