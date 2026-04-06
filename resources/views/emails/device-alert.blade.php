<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f5f5f0;font-family:'Helvetica Neue',Arial,'Noto Sans JP',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f0;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    {{-- ヘッダー --}}
                    <tr>
                        <td style="background:{{ $alertType === 'alert' ? '#c62828' : '#44403c' }};padding:20px 24px;text-align:center;">
                            <span style="font-size:24px;">🧈</span>
                            <br>
                            <span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス</span>
                        </td>
                    </tr>

                    {{-- タイトル --}}
                    <tr>
                        <td style="padding:28px 24px 0;">
                            <h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
                                @if($alertType === 'alert')
                                    🔴 未検知アラート
                                @else
                                    ⚫ 通信途絶のお知らせ
                                @endif
                            </h1>
                        </td>
                    </tr>

                    {{-- 本文 --}}
                    <tr>
                        <td style="padding:20px 24px;">
                            <div style="background:#faf8f4;border-radius:8px;padding:20px;border-left:4px solid {{ $alertType === 'alert' ? '#c62828' : '#78716c' }};">
                                @foreach(explode("\n", $mailBody) as $line)
                                    @if(str_starts_with(trim($line), '【'))
                                        {{-- セクションタイトルはスキップ（ヘッダーで表示済み） --}}
                                    @elseif(trim($line) === '')
                                        <br>
                                    @else
                                        <p style="margin:0 0 4px;font-size:14px;color:#44403c;line-height:1.7;">{{ trim($line) }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>

                    {{-- 注意書き --}}
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <p style="margin:0;font-size:12px;color:#a8a29e;line-height:1.6;text-align:center;">
                                ※ このメールは自動送信です。返信はできません。<br>
                                ※ 通知設定の変更はマイページから行えます。
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


