<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン - みまもりデバイス</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #262626;
        }
        .logo-badge {
            display: inline-block;
            margin-top: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #737373;
            background: #e5e5e5;
            padding: 3px 10px;
            border-radius: 4px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 32px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #262626;
            margin-bottom: 24px;
            text-align: center;
        }
        .alert {
            background: #fbe9e7;
            color: #c62828;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #404040;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
            border: 1px solid #d4d4d4;
            border-radius: 8px;
            background: #fff;
            color: #262626;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #737373;
            box-shadow: 0 0 0 3px rgba(115,115,115,0.15);
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #525252;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            background: #262626;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #404040; }
        .form-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #737373;
        }
        .form-footer a {
            color: #525252;
            text-decoration: underline;
        }
        .notice {
            font-size: 11px;
            color: #a3a3a3;
            text-align: center;
            margin-top: 12px;
        }
        .copyright {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #a3a3a3;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="logo">
            <div class="logo-text">🧈 みまもりデバイス</div>
            <span class="logo-badge">ADMIN</span>
        </div>

        <div class="card">
            <h1 class="card-title">🔐 管理者ログイン</h1>

            @if(session('success'))
                <div class="alert" style="background:#e8f5e9;color:#2e7d32;">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('partner.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">メールアドレス</label>
                    <input type="email" class="form-input" id="email" name="email"
                           value="{{ old('email') }}" autocomplete="email" autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">パスワード</label>
                    <input type="password" class="form-input" id="password" name="password"
                           autocomplete="current-password">
                </div>
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">ログイン状態を保持する</label>
                </div>
                <button type="submit" class="btn-submit">ログイン</button>
            </form>

            <div class="form-footer">
                <a href="{{ route('partner.password-reset') }}">パスワードをお忘れの方</a>
            </div>
            <p class="notice">※連続してログインに失敗すると一定時間操作できなくなります</p>
        </div>

        <p class="copyright">&copy; {{ date('Y') }} みまもりデバイス</p>
    </div>
</body>
</html>


