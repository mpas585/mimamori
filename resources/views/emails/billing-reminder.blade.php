<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お支払いが確認できていません</title>
</head>
<body style="margin:0;padding:0;background:#faf8f4;font-family:'Noto Sans JP','Hiragino Sans','Hiragino Kaku Gothic ProN',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    {{-- ヘッダー（オレンジ系の注意） --}}
                    <tr>
                        <td style="background:#e67e22;padding:20px 24px;text-align:center;">
                            <span style="font-size:24px;">🧈</span>
                            <br>
                            <span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス</span>
                        </td>
                    </tr>
                    {{-- タイトル --}}
                    <tr>
                        <td style="padding:28px 24px 0;">
                            <h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
                                ⚠️ お支払いが確認できていません
                            </h1>
                            <p style="margin:8px 0 0;font-size:13px;color:#e67e22;text-align:center;font-weight:600;">
                                停止まで残り {{ $remainingDays }}日
                            </p>
                        </td>
                    </tr>
                    {{-- 本文 --}}
                    <tr>
                        <td style="padding:20px 24px;">
                            <div style="background:#faf8f4;border-radius:8px;padding:20px;border-left:4px solid #e67e22;">
                                <p style="margin:0 0 12px;font-size:14px;color:#44403c;line-height:1.7;">
                                    <strong>{{ $displayName }}</strong> の月額課金でお支払いが確認できていません。
                                </p>
                                <table cellpadding="0" cellspacing="0" style="width:100%;margin:12px 0;">
                                    <tr>
                                        <td style="padding:4px 0;font-size:13px;color:#78716c;width:100px;">請求金額</td>
                                        <td style="padding:4px 0;font-size:13px;color:#292524;font-weight:600;">¥{{ number_format($amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0;font-size:13px;color:#78716c;">停止予定日</td>
                                        <td style="padding:4px 0;font-size:13px;color:#c62828;font-weight:600;">{{ $suspendDate }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    {{-- 警告 --}}
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <div style="background:#fff7ed;border-radius:8px;padding:16px;border:1px solid #fed7aa;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#c2410c;">
                                    停止予定日までにお支払いが確認できない場合
                                </p>
                                <p style="margin:0;font-size:12px;color:#57534e;line-height:1.7;">
                                    デバイス自体のサービスを停止いたします。停止後はデバイスからのデータ受信・マイページへのログインができなくなります。<br><br>
                                    カード情報を更新いただくか、新たなカードをご登録ください。次回の課金で成功すれば、プレミアム通知を含むすべての機能が自動的に再開します。
                                </p>
                            </div>
                        </td>
                    </tr>
                    {{-- ボタン --}}
                    <tr>
                        <td style="padding:0 24px 20px;text-align:center;">
                            <a href="{{ $actionUrl }}"
                               style="display:inline-block;padding:14px 32px;background:#e67e22;color:#ffffff;
                                      font-size:14px;font-weight:600;text-decoration:none;border-radius:8px;">
                                @if($isB2B)
                                    課金管理画面で確認する
                                @else
                                    プラン画面で確認する
                                @endif
                            </a>
                        </td>
                    </tr>
                    {{-- URLフォールバック --}}
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <p style="margin:0 0 6px;font-size:11px;color:#78716c;line-height:1.6;">
                                ボタンが動作しない場合は、以下のURLをブラウザに貼り付けてください：
                            </p>
                            <p style="margin:0;font-size:11px;color:#44403c;line-height:1.5;word-break:break-all;">
                                {{ $actionUrl }}
                            </p>
                        </td>
                    </tr>
                    {{-- 注意書き --}}
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <p style="margin:0;font-size:11px;color:#a8a29e;line-height:1.6;text-align:center;">
                                ※ このメールは自動送信です。返信はできません。<br>
                                ※ ご不明な点はお問い合わせフォームよりご連絡ください。
                            </p>
                        </td>
                    </tr>
                    {{-- フッター --}}
                    <tr>
                        <td style="background:#faf8f4;padding:16px 24px;text-align:center;border-top:1px solid #e7e5e4;">
                            <p style="margin:0;font-size:11px;color:#a8a29e;">
                                みまもりデバイス — 安心の見守りサービス
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
