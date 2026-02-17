<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f5f3ef; font-family:'Noto Sans JP', -apple-system, sans-serif;">
    <div style="max-width:560px; margin:0 auto; padding:40px 20px;">
        <div style="background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- ヘッダー --}}
            <div style="text-align:center; margin-bottom:28px; padding-bottom:20px; border-bottom:2px solid #e7e5e4;">
                <span style="font-size:24px;">🧈</span>
                <span style="font-size:18px; font-weight:500; color:#292524; margin-left:8px;">みまもりトーフ</span>
            </div>

            {{-- 本文 --}}
            <p style="font-size:14px; color:#44403c; margin-bottom:12px;">
                {{ $adminName }} 様
            </p>

            <p style="font-size:14px; color:#44403c; margin-bottom:24px; line-height:1.7;">
                管理者アカウントのパスワード再設定のリクエストを受け付けました。<br>
                下のボタンをクリックして、新しいパスワードを設定してください。
            </p>

            {{-- ボタン --}}
            <div style="text-align:center; margin-bottom:24px;">
                <a href="{{ $resetUrl }}"
                   style="display:inline-block; background:#292524; color:#ffffff; text-decoration:none; padding:14px 40px; border-radius:8px; font-size:15px; font-weight:600;">
                    パスワードを再設定する
                </a>
            </div>

            <p style="font-size:12px; color:#a8a29e; margin-bottom:20px; line-height:1.6;">
                ※ このリンクの有効期限は <strong>1時間</strong> です。<br>
                ※ このメールに心当たりがない場合は、無視してください。パスワードは変更されません。
            </p>

            {{-- URLフォールバック --}}
            <div style="background:#faf8f4; border-radius:8px; padding:14px; margin-bottom:20px;">
                <p style="font-size:11px; color:#78716c; margin-bottom:4px;">ボタンが機能しない場合は、以下のURLをブラウザに貼り付けてください：</p>
                <p style="font-size:11px; color:#57534e; word-break:break-all;">{{ $resetUrl }}</p>
            </div>

            {{-- フッター --}}
            <div style="border-top:1px solid #e7e5e4; padding-top:16px; text-align:center;">
                <p style="font-size:11px; color:#a8a29e;">
                    このメールは自動送信されています。返信はできません。<br>
                    &copy; {{ date('Y') }} みまもりトーフ
                </p>
            </div>

        </div>
    </div>
</body>
</html>
