<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - みまもりトーフ ADMIN</title>
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

        /* ===== 送信完了 ===== */
        .sent-icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .sent-email {
            font-weight: 700;
            color: var(--gray-800);
        }
        .help-section {
            background: var(--cream);
            border-radius: var(--radius);
            padding: 16px;
            margin-top: 20px;
        }
        .help-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        .help-list {
            list-style: none;
            padding: 0;
        }
        .help-list li {
            font-size: 12px;
            color: var(--gray-500);
            padding: 3px 0;
            padding-left: 16px;
            position: relative;
        }
        .help-list li::before {
            content: '•';
            position: absolute;
            left: 4px;
            color: var(--gray-400);
        }

        /* ===== フッター ===== */
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

            @if(isset($sent) && $sent)
                {{-- ===== 送信完了表示 ===== --}}
                <div class="sent-icon">✉️</div>
                <h1 class="card-title">メールを送信しました</h1>
                <p class="card-desc" style="text-align:center;">
                    <span class="sent-email">{{ $email }}</span> 宛に<br>
                    パスワード再設定用のメールを送信しました。<br><br>
                    メール内のリンクから<br>
                    新しいパスワードを設定してください。<br><br>
                    <small style="color:var(--gray-400);">リンクの有効期限は1時間です</small>
                </p>

                <div class="help-section">
                    <div class="help-title">メールが届かない場合</div>
                    <ul class="help-list">
                        <li>迷惑メールフォルダをご確認ください</li>
                        <li>メールアドレスが正しいかご確認ください</li>
                        <li>数分待っても届かない場合は再度お試しください</li>
                    </ul>
                </div>

                <div class="back-link">
                    <a href="{{ url('/admin/login') }}">← ログインに戻る</a>
                </div>

            @else
                {{-- ===== メール入力フォーム ===== --}}
                <h1 class="card-title">🔑 パスワード再設定</h1>
                <p class="card-desc">
                    登録されているメールアドレスを入力してください。<br>
                    パスワード再設定用のリンクをお送りします。
                </p>

                @if($errors->any())
                    <div class="error-message">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ url('/admin/password-reset') }}" id="resetForm">
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

                    <button type="submit" class="submit-btn" id="submitBtn">リセットリンクを送信</button>
                </form>

                <div class="back-link">
                    <a href="{{ url('/admin/login') }}">← ログインに戻る</a>
                </div>
            @endif

        </div>
    </div>

</div>

<footer class="footer">
    &copy; {{ date('Y') }} みまもりトーフ
</footer>

@if(!isset($sent))
<script>
document.getElementById('resetForm').addEventListener('submit', function() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '送信中...';
    setTimeout(function() {
        btn.disabled = false;
        btn.textContent = 'リセットリンクを送信';
    }, 5000);
});
</script>
@endif

</body>
</html>
