<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>サービスを停止しました</title>
</head>
<body style="margin:0;padding:0;background:#faf8f4;font-family:'Noto Sans JP','Hiragino Sans','Hiragino Kaku Gothic ProN',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    {{-- ヘッダー（濃い赤） --}}
                    <tr>
                        <td style="background:#7f1d1d;padding:20px 24px;text-align:center;">
                            <span style="font-size:24px;">🧈</span>
                            <br>
                            <span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス</span>
                        </td>
                    </tr>
                    {{-- タイトル --}}
                    <tr>
                        <td style="padding:28px 24px 0;">
                            <h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
                                🛑 サービスを停止しました
                            </h1>
                        </td>
                    </tr>
                    {{-- 本文 --}}
                    <tr>
                        <td style="padding:20px 24px;">
                            <div style="background:#faf8f4;border-radius:8px;padding:20px;border-left:4px solid #7f1d1d;">
                                <p style="margin:0 0 12px;font-size:14px;color:#44403c;line-height:1.7;">
                                    <strong>{{ $displayName }}</strong> は、お支払い確認ができないまま30日が経過したため、サービスを停止いたしました。
                                </p>
                                <table cellpadding="0" cellspacing="0" style="width:100%;margin:12px 0;">
                                    <tr>
                                        <td style="padding:4px 0;font-size:13px;color:#78716c;width:100px;">停止日時</td>
                                        <td style="padding:4px 0;font-size:13px;color:#292524;font-weight:600;">{{ $suspendedAt }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    {{-- 影響 --}}
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#44403c;">停止中に制限される機能</p>
                            <ul style="margin:0;padding-left:20px;font-size:13px;color:#57534e;line-height:1.7;">
                                <li>デバイスからのデータ受信（人感センサー検知）</li>
                                <li>未検知アラートなどの通知</li>
                                <li>マイページおよび管理画面へのログイン</li>
                            </ul>
                        </td>
                    </tr>
                    {{-- 再開方法 --}}
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <div style="background:#fef2f2;border-radius:8px;padding:16px;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#7f1d1d;">
                                    サービスを再開するには
                                </p>
                                <p style="margin:0;font-size:12px;color:#57534e;line-height:1.7;">
                                    お問い合わせフォームより、サービス再開のご希望をお知らせください。ご契約内容を確認の上、再開のご案内をいたします。
                                </p>
                            </div>
                        </td>
                    </tr>
                    {{-- ボタン --}}
                    <tr>
                        <td style="padding:0 24px 20px;text-align:center;">
                            <a href="{{ url('/contact') }}"
                               style="display:inline-block;padding:14px 32px;background:#7f1d1d;color:#ffffff;
                                      font-size:14px;font-weight:600;text-decoration:none;border-radius:8px;">
                                お問い合わせフォームへ
                            </a>
                        </td>
                    </tr>
                    {{-- 注意書き --}}
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <p style="margin:0;font-size:11px;color:#a8a29e;line-height:1.6;text-align:center;">
                                ※ このメールは自動送信です。返信はできません。
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
