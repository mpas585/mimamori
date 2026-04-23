<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PIN再設定のご案内</title>
</head>
<body style="margin:0;padding:0;background:#faf8f4;font-family:'Noto Sans JP','Hiragino Sans','Hiragino Kaku Gothic ProN',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    {{-- ヘッダー --}}
                    <tr>
                        <td style="background:#44403c;padding:20px 24px;text-align:center;">
                            <span style="font-size:24px;">🧈</span>
                            <br>
                            <span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス</span>
                        </td>
                    </tr>
                    {{-- タイトル --}}
                    <tr>
                        <td style="padding:28px 24px 0;">
                            <h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
                                PIN再設定のご案内
                            </h1>
                        </td>
                    </tr>
                    {{-- 本文 --}}
                    <tr>
                        <td style="padding:20px 24px;">
                            <div style="background:#faf8f4;border-radius:8px;padding:20px;border-left:4px solid #78716c;">
                                <p style="margin:0 0 12px;font-size:14px;color:#44403c;line-height:1.7;">
                                    デバイス「<strong>{{ $deviceName }}</strong>」のPIN再設定リクエストを受け付けました。
                                </p>
                                <p style="margin:0;font-size:14px;color:#44403c;line-height:1.7;">
                                    下のボタンから、新しいPINを設定してください。
                                </p>
                            </div>
                        </td>
                    </tr>
                    {{-- ボタン --}}
                    <tr>
                        <td style="padding:0 24px 20px;text-align:center;">
                            <a href="{{ $resetUrl }}"
                               style="display:inline-block;padding:14px 32px;background:#44403c;color:#ffffff;
                                      font-size:14px;font-weight:600;text-decoration:none;border-radius:8px;">
                                新しいPINを設定する
                            </a>
                        </td>
                    </tr>
                    {{-- URLフォールバック --}}
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <p style="margin:0 0 8px;font-size:12px;color:#78716c;line-height:1.6;">
                                ボタンが動作しない場合は、以下のURLをブラウザに貼り付けてください：
                            </p>
                            <p style="margin:0;font-size:11px;color:#44403c;line-height:1.5;word-break:break-all;">
                                {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>
                    {{-- 注意書き --}}
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <p style="margin:0;font-size:12px;color:#a8a29e;line-height:1.6;text-align:center;">
                                ※ このリンクの有効期限は <strong>1時間</strong> です。<br>
                                ※ このメールに心当たりがない場合は、破棄してください。<br>
                                &nbsp;&nbsp;&nbsp;PINは変更されません。<br>
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
