<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>管理者ログイン - みまもりトーフ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --white: #ffffff;
            --cream: #faf9f7;
            --beige: #f5f3ef;
            --gray-100: #f0eeea;
            --gray-200: #e5e2dc;
            --gray-300: #d1ccc3;
            --gray-400: #a8a29e;
            --gray-500: #78716c;
            --gray-600: #57534e;
            --gray-700: #44403c;
            --gray-800: #292524;
            --red-light: #fee2e2;
            --red: #ef4444;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 4px 20px rgba(0,0,0,0.06);
            --radius: 8px;
            --radius-lg: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: var(--cream);
            color: var(--gray-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            line-height: 1.7;
        }
        .container {
            width: 100%;
            max-width: 480px;
        }

        /* ロゴ */
        .logo-area {
            text-align: center;
            margin-bottom: 48px;
            animation: fadeIn 0.6s ease;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 500;
            letter-spacing: 0.02em;
            color: var(--gray-800);
        }
        .logo-badge {
            display: inline-block;
            background: var(--gray-700);
            color: var(--white);
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 10px;
            vertical-align: middle;
            letter-spacing: 0.05em;
        }

        /* カード */
        .login-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
            padding: 32px;
        }
        .login-card-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--gray-800);
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-200);
        }

        /* エラー */
        .error-message {
            background: var(--red-light);
            border: 1px solid #fecaca;
            color: var(--red);
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* フォーム */
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: var(--cream);
            color: var(--gray-800);
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--gray-500);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
        }
        .form-input::placeholder {
            color: var(--gray-400);
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--gray-700);
        }
        .remember-row label {
            font-size: 13px;
            color: var(--gray-500);
            cursor: pointer;
        }

        /* ボタン */
        .login-btn {
            display: block;
            width: 100%;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--gray-800);
            color: var(--white);
        }
        .login-btn:hover {
            background: var(--gray-700);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        .forgot-link {
            text-align: center;
            margin-top: 16px;
        }
        .forgot-link a {
            font-size: 13px;
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-link a:hover {
            color: var(--gray-700);
            text-decoration: underline;
        }

        .security-note {
            font-size: 11px;
            color: var(--gray-400);
            text-align: center;
            margin-top: 24px;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 12px;
            color: var(--gray-400);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 480px) {
            .logo-text { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- ロゴエリア --}}
        <div class="logo-area">
            <div class="logo">
                <span class="logo-text">みまもりトーフ</span>
                <span class="logo-badge">ADMIN</span>
            </div>
        </div>

        {{-- ログインカード --}}
        <div class="login-card">
            <h1 class="login-card-title">管理者ログイン</h1>

            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">メールアドレス</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="admin@example.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">パスワード</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">ログイン状態を保持する</label>
                </div>

                <button type="submit" class="login-btn">ログイン</button>

                <p class="forgot-link"><a href="#">パスワードをお忘れの方</a></p>

                <p class="security-note">※連続してログインに失敗すると一定時間操作できなくなります</p>
            </form>
        </div>

        <p class="footer">&copy; {{ date('Y') }} みまもりトーフ</p>
    </div>
</body>
</html>
