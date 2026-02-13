<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * お問い合わせフォーム表示
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * お問い合わせ送信処理
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:purchase,usage,billing,bulk,data,report,other',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'device_id' => 'nullable|string|max:10',
            'message' => 'required|string|max:5000',
        ], [
            'category.required' => 'お問い合わせ種別を選択してください。',
            'name.required' => 'お名前を入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '正しいメールアドレスを入力してください。',
            'message.required' => 'お問い合わせ内容を入力してください。',
            'message.max' => 'お問い合わせ内容は5000文字以内で入力してください。',
        ]);

        // カテゴリ名の変換
        $categoryNames = [
            'purchase' => '購入前のご質問',
            'usage' => '使い方・設定について',
            'billing' => '料金・お支払いについて',
            'bulk' => '法人・大量購入のご相談',
            'data' => 'データの開示・削除請求',
            'report' => '不正利用の通報',
            'other' => 'その他',
        ];

        $categoryName = $categoryNames[$validated['category']] ?? $validated['category'];

        // 管理者宛メール送信（TODO: 実際のメールアドレスに変更）
        try {
            Mail::raw(
                "【お問い合わせ】\n\n" .
                "種別: {$categoryName}\n" .
                "お名前: {$validated['name']}\n" .
                "メール: {$validated['email']}\n" .
                "デバイスID: " . ($validated['device_id'] ?? '未入力') . "\n\n" .
                "内容:\n{$validated['message']}\n",
                function ($mail) use ($validated, $categoryName) {
                    $mail->to(config('mail.admin_address', 'admin@example.com'))
                         ->replyTo($validated['email'], $validated['name'])
                         ->subject("【みまもりデバイス】お問い合わせ: {$categoryName}");
                }
            );
        } catch (\Exception $e) {
            Log::error('Contact form mail failed: ' . $e->getMessage());
        }

        return redirect('/contact')->with('contact_success', true);
    }
}
