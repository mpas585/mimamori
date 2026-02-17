<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいパスワード設定 - みまもりトーフ ADMIN</title>
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
            --radius: 8px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.06);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
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

        /* ===== ロゴ ===== */
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
        .logo-emoji { font-size: 28px; }
        .logo-text { font-size: 22px; font-weight: 500; letter-spacing: 0.02em; color: var(--gray-800); }
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

        /* ===== カード ===== */
        .card-container { max-width: 440px; width: 100%; }
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            padding: 32px;
            animation: fadeIn 0.6s ease 0.1s both;
        }
        .card-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--gray-800);
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-200);
        }
        .card-desc {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        /* ===== エラー ===== */
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
        .error-message::before { content: '⚠'; font-size: 14px; flex-shrink: 0; }

        /* ===== フォーム ===== */
        .form-group { margin-bottom: 20px; }
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
        .form-input::placeholder { color: var(--gray-400); }
        .form-hint {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 6px;
            font-weight: 500;
        }

        /* パスワードフィールド */
        .password-wrapper { position: relative; }
        .password-wrapper .form-input { padding-right: 48px; }
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
        .password-toggle:hover { color: var(--gray-600); }

        /* 強度メーター */
        .strength-meter {
            margin-top: 8px;
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
        }
        .strength-meter-fill {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: all 0.3s;
        }
        .strength-text {
            font-size: 11px;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ===== ボタン ===== */
        .submit-btn {
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
        .submit-btn:hover { background: var(--gray-700); transform: translateY(-1px); box-shadow: var(--shadow); }
        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* ===== 戻るリンク ===== */
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            font-size: 13px;
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link a:hover { color: var(--gray-700); text-decoration: underline; }

        .footer {
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: var(--gray-400);
        }
        @media (max-width: 480px) {
            .main-content { padding: 32px 16px 40px; }
            .logo-text { font-size: 20px; }
            .card { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<div class="main-content">

    {{-- ロゴ --}}
    <div class="logo-area">
        <div class="logo">
            <span class="logo-emoji">🧈</span>
            <span class="logo-text">みまもりトーフ</span>
        </div>
        <div>
            <span class="logo-badge">ADMIN</span>
        </div>
    </div>

    <div class="card-container">
        <div class="card">
            <h1 class="card-title">🔐 新しいパスワード設定</h1>
            <p class="card-desc">
                新しいパスワードを入力してください。
            </p>

            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ url('/admin/password-reset/' . $token) }}" id="resetForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label" for="password">新しいパスワード</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="8文字以上"
                            minlength="8"
                            autocomplete="new-password"
                            autofocus
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="パスワードを表示">
                            👁
                        </button>
                    </div>
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="strengthFill"></div>
                    </div>
                    <p class="strength-text" id="strengthText"></p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">パスワード確認</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="もう一度入力"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="パスワードを表示">
                            👁
                        </button>
                    </div>
                    <p class="form-hint" id="matchHint"></p>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">パスワードを変更</button>
            </form>

            <div class="back-link">
                <a href="{{ url('/admin/login') }}">← ログインに戻る</a>
            </div>
        </div>
    </div>

</div>

<footer class="footer">
    &copy; {{ date('Y') }} みまもりトーフ
</footer>

<script>
// パスワード表示トグル
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.textContent = isPassword ? '🔒' : '👁';
}

// パスワード強度チェック
document.getElementById('password').addEventListener('input', function() {
    var pw = this.value;
    var fill = document.getElementById('strengthFill');
    var text = document.getElementById('strengthText');
    var score = 0;

    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;

    var levels = [
        { width: '0%', color: '#d6d3d1', label: '' },
        { width: '20%', color: '#c62828', label: '弱い' },
        { width: '40%', color: '#e65100', label: 'やや弱い' },
        { width: '60%', color: '#f9a825', label: '普通' },
        { width: '80%', color: '#558b2f', label: '強い' },
        { width: '100%', color: '#2e7d32', label: 'とても強い' },
    ];

    var level = pw.length === 0 ? levels[0] : levels[Math.min(score, 5)];
    fill.style.width = level.width;
    fill.style.background = level.color;
    text.textContent = level.label;
    text.style.color = level.color;

    checkMatch();
});

// 一致チェック
document.getElementById('password_confirmation').addEventListener('input', checkMatch);

function checkMatch() {
    var pw = document.getElementById('password').value;
    var confirm = document.getElementById('password_confirmation').value;
    var hint = document.getElementById('matchHint');

    if (confirm.length === 0) {
        hint.textContent = '';
    } else if (pw === confirm) {
        hint.textContent = '✓ パスワードが一致しています';
        hint.style.color = '#2e7d32';
    } else {
        hint.textContent = '× パスワードが一致しません';
        hint.style.color = '#c62828';
    }
}

// 二重送信防止
document.getElementById('resetForm').addEventListener('submit', function() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '変更中...';
    setTimeout(function() {
        btn.disabled = false;
        btn.textContent = 'パスワードを変更';
    }, 5000);
});
</script>

</body>
</html>
