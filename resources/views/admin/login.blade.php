<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン - みまもりトーフ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --white: #ffffff;
            --cream: #faf8f4;
            --beige: #f0ebe1;
            --gray-100: #f5f5f4;
            --gray-200: #e7e5e4;
            --gray-300: #d6d3d1;
            --gray-400: #a8a29e;
            --gray-500: #78716c;
            --gray-600: #57534e;
            --gray-700: #44403c;
            --gray-800: #292524;
            --red: #c62828;
            --red-light: #fbe9e7;
            --green: #2e7d32;
            --green-light: #e8f5e9;
            --radius: 8px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.06);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--cream);
            color: var(--gray-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex: 1;
            padding: 40px 16px 60px;
        }

        /* ===== ロゴエリア ===== */
        .logo-area {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeIn 0.6s ease;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .logo-emoji {
            font-size: 28px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 500;
            letter-spacing: 0.02em;
            color: var(--gray-800);
        }
        .logo-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--gray-500);
            background: var(--beige);
            border: 1px solid var(--gray-300);
            border-radius: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== ログインカード ===== */
        .login-container {
            max-width: 440px;
            width: 100%;
        }
        .login-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            padding: 32px;
            animation: fadeIn 0.6s ease 0.1s both;
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

        /* ===== 成功メッセージ ===== */
        .success-message {
            background: var(--green-light);
            color: var(--green);
            font-size: 13px;
            font-weight: 500;
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .success-message::before {
            content: '✓';
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ===== エラー表示 ===== */
        .error-message {
            background: var(--red-light);
            color: var(--red);
            font-size: 13px;
            font-weight: 500;
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .error-message::before {
            content: '⚠';
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ===== フォーム ===== */
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

        /* パスワード表示トグル */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .form-input {
            padding-right: 48px;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 18px;
            padding: 4px;
            transition: color 0.2s;
            line-height: 1;
        }
        .password-toggle:hover {
            color: var(--gray-600);
        }

        /* ===== Remember Me + パスワードリセット ===== */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
            user-select: none;
        }
        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border: 1.5px solid var(--gray-300);
            border-radius: 4px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            background: var(--white);
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .remember-label input[type="checkbox"]:checked {
            background: var(--gray-800);
            border-color: var(--gray-800);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z' fill='%23fff'/%3E%3C/svg%3E");
            background-size: 14px;
            background-position: center;
            background-repeat: no-repeat;
        }
        .remember-label input[type="checkbox"]:focus-visible {
            box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.3);
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

        /* ===== ボタン ===== */
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
        .login-btn:active {
            transform: translateY(0);
        }
        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ===== セキュリティノート ===== */
        .security-note {
            font-size: 11px;
            color: var(--gray-400);
            text-align: center;
            margin-top: 20px;
        }

        /* ===== フッター ===== */
        .footer {
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: var(--gray-400);
        }

        /* ===== レスポンシブ ===== */
        @media (max-width: 480px) {
            .main-content {
                padding: 32px 16px 40px;
            }
            .logo-text {
                font-size: 20px;
            }
            .login-card {
                padding: 24px 20px;
            }
            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="main-content">

    {{-- ロゴエリア --}}
    <div class="logo-area">
        <div class="logo">
            <span class="logo-emoji">🧈</span>
            <span class="logo-text">みまもりトーフ</span>
        </div>
        <div>
            <span class="logo-badge">ADMIN</span>
        </div>
    </div>

    {{-- ログインカード --}}
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-card-title">🔐 管理者ログイン</h1>

            {{-- 成功メッセージ（パスワードリセット後など） --}}
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            {{-- エラー表示 --}}
            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
                @csrf

                {{-- メールアドレス --}}
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

                {{-- パスワード --}}
                <div class="form-group">
                    <label class="form-label" for="password">パスワード</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="パスワードを表示">
                            👁
                        </button>
                    </div>
                </div>

                {{-- Remember Me + パスワードリセット --}}
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        ログイン状態を保持
                    </label>
                    <div class="forgot-link">
                        <a href="{{ url('/admin/password-reset') }}">パスワードを忘れた場合</a>
                    </div>
                </div>

                {{-- ログインボタン --}}
                <button type="submit" class="login-btn" id="loginBtn">ログイン</button>
            </form>

            <p class="security-note">※連続してログインに失敗すると一定時間操作できなくなります</p>
        </div>
    </div>

</div>

<footer class="footer">
    &copy; {{ date('Y') }} みまもりトーフ
</footer>

<script>
// パスワード表示/非表示トグル
document.getElementById('passwordToggle').addEventListener('click', function() {
    var input = document.getElementById('password');
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    this.textContent = isPassword ? '🔒' : '👁';
    this.setAttribute('aria-label', isPassword ? 'パスワードを非表示' : 'パスワードを表示');
});

// 二重送信防止
document.getElementById('loginForm').addEventListener('submit', function() {
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = 'ログイン中...';
    setTimeout(function() {
        btn.disabled = false;
        btn.textContent = 'ログイン';
    }, 3000);
});
</script>

</body>
</html>
