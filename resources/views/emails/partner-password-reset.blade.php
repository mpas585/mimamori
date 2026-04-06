<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - みまもりデバイス</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f0e8; font-family: 'Helvetica Neue', Arial, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .header { background: #262626; padding: 28px 32px; text-align: center; }
        .header-title { color: #fff; font-size: 20px; font-weight: 700; margin: 0; }
        .header-sub { color: #a3a3a3; font-size: 12px; margin: 4px 0 0; }
        .body { padding: 32px; }
        .greeting { font-size: 15px; color: #262626; font-weight: 600; margin-bottom: 16px; }
        .message { font-size: 14px; color: #525252; line-height: 1.8; margin-bottom: 24px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; padding: 14px 36px; background: #262626; color: #fff !important; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 700; }
        .url-box { background: #f5f0e8; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
        .url-box p { font-size: 11px; color: #737373; margin: 0 0 6px; }
        .url-box a { font-size: 12px; color: #525252; word-break: break-all; }
        .note { background: #fef9c3; border-left: 3px solid #eab308; border-radius: 4px; padding: 12px 16px; font-size: 13px; color: #713f12; margin-bottom: 24px; }
        .footer { background: #f5f5f5; padding: 20px 32px; text-align: center; }
        .footer p { font-size: 11px; color: #a3a3a3; margin: 0; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <p class="header-title">🧈 みまもりデバイス</p>
            <p class="header-sub">PARTNER ADMIN</p>
        </div>

        <div class="body">
            <p class="greeting">{{ $adminName }} 様</p>
            <p class="message">
                パスワード再設定のリクエストを受け付けました。<br>
                以下のボタンからパスワードを再設定してください。
            </p>

            <div class="btn-wrap">
                <a href="{{ $resetUrl }}" class="btn">パスワードを再設定する</a>
            </div>

            <div class="url-box">
                <p>ボタンが機能しない場合は、以下のURLをブラウザで開いてください：</p>
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </div>

            <div class="note">
                ⚠️ このリンクの有効期限は<strong>1時間</strong>です。期限切れの場合は再度申請してください。<br>
                身に覚えのない場合は、このメールを無視してください。パスワードは変更されません。
            </div>
        </div>

        <div class="footer">
            <p>
                みまもりデバイス 管理システム<br>
                このメールは自動送信されています。返信はできません。
            </p>
        </div>
    </div>
</body>
</html>


