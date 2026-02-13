<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f5f4f0;font-family:'Helvetica Neue',Arial,'Noto Sans JP',sans-serif;">
    <div style="max-width:480px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <!-- ヘッダー -->
        <div style="background:#44403c;padding:24px;text-align:center;">
            <span style="font-size:28px;">🧈</span>
            <h1 style="color:#ffffff;font-size:16px;margin:8px 0 0;font-weight:600;">みまもりデバイス</h1>
        </div>

        <!-- 本文 -->
        <div style="padding:32px 24px;">
            <h2 style="font-size:18px;color:#292524;margin-bottom:16px;">メールアドレスの確認</h2>

            <p style="font-size:14px;color:#57534e;line-height:1.8;margin-bottom:24px;">
                デバイス <strong>{{ $device->device_id }}</strong> のメール通知先として<br>
                このメールアドレスが登録されました。
            </p>

            <p style="font-size:14px;color:#57534e;line-height:1.8;margin-bottom:24px;">
                以下のボタンをクリックして、メールアドレスの登録を完了してください。
            </p>

            <!-- 確認ボタン -->
            <div style="text-align:center;margin:32px 0;">
                <a href="{{ $verifyUrl }}"
                   style="display:inline-block;background:#44403c;color:#ffffff;padding:14px 40px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:600;">
                    メールアドレスを確認する
                </a>
            </div>

            <p style="font-size:12px;color:#a8a29e;line-height:1.6;">
                ※ このリンクの有効期限は24時間です。<br>
                ※ ボタンが押せない場合は、以下のURLをブラウザに貼り付けてください。
            </p>

            <div style="background:#f5f4f0;padding:12px;border-radius:6px;margin-top:12px;word-break:break-all;">
                <code style="font-size:11px;color:#78716c;">{{ $verifyUrl }}</code>
            </div>

            <hr style="border:none;border-top:1px solid #e5e2dc;margin:24px 0;">

            <p style="font-size:12px;color:#a8a29e;line-height:1.6;">
                このメールに心当たりがない場合は、無視してください。<br>
                リンクをクリックしない限り、メールアドレスは変更されません。
            </p>
        </div>

        <!-- フッター -->
        <div style="background:#f5f4f0;padding:16px 24px;text-align:center;">
            <p style="font-size:11px;color:#a8a29e;">
                みまもりデバイス - 安心の見守りサービス<br>
                このメールは自動送信です。返信はできません。
            </p>
        </div>
    </div>
</body>
</html>
