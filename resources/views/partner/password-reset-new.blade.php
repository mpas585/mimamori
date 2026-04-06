<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいパスワード設定 - みまもりデバイス</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .wrap { width: 100%; max-width: 400px; }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-text { font-size: 22px; font-weight: 700; color: #262626; }
        .logo-badge {
            display: inline-block; margin-top: 6px; font-size: 11px;
            font-weight: 700; letter-spacing: 1px; color: #737373;
            background: #e5e5e5; padding: 3px 10px; border-radius: 4px;
        }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 32px; }
        .card-title { font-size: 18px; font-weight: 700; color: #262626; margin-bottom: 8px; text-align: center; }
        .card-desc { font-size: 13px; color: #737373; text-align: center; margin-bottom: 24px; }
        .alert { background: #fbe9e7; color: #c62828; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #404040; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 10px 12px; font-size: 14px; font-family: inherit;
            border: 1px solid #d4d4d4; border-radius: 8px; background: #fff; color: #262626;
        }
        .form-input:focus { outline: none; border-color: #737373; box-shadow: 0 0 0 3px rgba(115,115,115,0.15); }
        .form-hint { font-size: 11px; color: #a3a3a3; margin-top: 4px; }
        .btn-submit {
            width: 100%; padding: 12px; font-size: 15px; font-weight: 700; font-family: inherit;
            background: #262626; color: #fff; border: none; border-radius: 8px; cursor: pointer;
        }
        .btn-submit:hover { background: #404040; }
        .back-link { text-align: center; margin-top: 16px; font-size: 13px; }
        .back-link a { color: #525252; text-decoration: underline; }
        .copyright { text-align: center; margin-top: 24px; font-size: 12px; color: #a3a3a3; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">
            <div class="logo-text">🧈 みまもりデバイス</div>
            <span class="logo-badge">ADMIN</span>
        </div>

        <div class="card">
            <h1 class="card-title">🔐 新しいパスワード設定</h1>
            <p class="card-desc">新しいパスワードを入力してください。</p>

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ url('/partner/password-reset/' . $token) }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label" for="password">新しいパスワード</label>
                    <input type="password" class="form-input" id="password" name="password"
                           placeholder="8文字以上" autocomplete="new-password">
                    <p class="form-hint">8文字以上で入力してください</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">新しいパスワード（確認）</label>
                    <input type="password" class="form-input" id="password_confirmation" name="password_confirmation"
                           placeholder="もう一度入力" autocomplete="new-password">
                </div>
                <button type="submit" class="btn-submit">パスワードを設定する</button>
            </form>

            <div class="back-link">
                <a href="{{ url('/partner/login') }}">← ログインに戻る</a>
            </div>
        </div>

        <p class="copyright">&copy; {{ date('Y') }} みまもりデバイス</p>
    </div>
</body>
</html>


