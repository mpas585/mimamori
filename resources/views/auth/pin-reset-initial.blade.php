<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN再設定 - 初期PINで再設定</title>
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
            --red-light: #fef2f2;
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
            line-height: 1.7;
        }
        .container {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: calc(100vh - 40px);
        }
        .logo-area { text-align: center; margin-bottom: 48px; }
        .logo { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .logo-text { font-size: 24px; font-weight: 500; letter-spacing: 0.02em; color: var(--gray-800); }
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
            padding: 32px;
            animation: fadeIn 0.4s ease;
        }
        .card-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--gray-800);
        }
        .card-desc {
            font-size: 14px;
            color: var(--gray-500);
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-200);
        }
        .device-id-badge {
            font-weight: 600;
            color: var(--gray-700);
            font-family: monospace;
            letter-spacing: 0.1em;
        }
        .info-box {
            background: var(--beige);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 24px;
        }
        .info-box-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
        }
        .info-box-text { font-size: 13px; color: var(--gray-600); line-height: 1.6; }
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
            box-shadow: 0 0 0 3px rgba(168,162,158,0.15);
        }
        .form-input::placeholder { color: var(--gray-400); }
        .form-input.pin-input {
            letter-spacing: 0.3em;
            text-align: center;
            font-size: 20px;
        }
        .form-hint { font-size: 12px; color: var(--gray-500); margin-top: 6px; font-weight: 500; }
        .form-error { font-size: 12px; color: var(--red); margin-top: 6px; font-weight: 500; }
        .form-divider {
            border: none;
            border-top: 2px solid var(--gray-200);
            margin: 24px 0;
        }
        .section-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 16px;
        }
        .btn {
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
            text-decoration: none;
            text-align: center;
        }
        .btn-primary { background: var(--gray-800); color: var(--white); }
        .btn-primary:hover { background: var(--gray-700); transform: translateY(-1px); box-shadow: var(--shadow); }
        .back-link { text-align: center; margin-top: 24px; }
        .back-link a {
            font-size: 14px;
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-link a::before { content: '←'; }
        .back-link a:hover { color: var(--gray-700); }
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
        <div class="logo-area">
            <div class="logo">
                <span class="logo-text">みまもりデバイス</span>
            </div>
        </div>

        <div class="card">
            <h1 class="card-title">初期PINでリセット</h1>
            <p class="card-desc">デバイス <span class="device-id-badge">{{ $device_id }}</span></p>

            <div class="info-box">
                <p class="info-box-title">初期PINとは</p>
                <p class="info-box-text">製品ラベルに記載されている4桁の数字です。初期PINを確認のうえ、新しいPINを設定してください。</p>
            </div>

            <form method="POST" action="/pin-reset/initial">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="initialPin">初期PIN</label>
                    <input type="password" id="initialPin" name="initial_pin" class="form-input pin-input"
                           placeholder="••••" maxlength="4" inputmode="numeric" autocomplete="off" autofocus>
                    <p class="form-hint">製品ラベルに記載の4桁</p>
                    @error('initial_pin')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="form-divider">
                <p class="section-label">新しいPINを設定</p>

                <div class="form-group">
                    <label class="form-label" for="newPin">新しいPIN</label>
                    <input type="password" id="newPin" name="new_pin" class="form-input pin-input"
                           placeholder="••••" maxlength="4" inputmode="numeric" autocomplete="new-password">
                    <p class="form-hint">数字4桁</p>
                    @error('new_pin')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="newPinConfirm">新しいPIN（確認）</label>
                    <input type="password" id="newPinConfirm" name="new_pin_confirmation" class="form-input pin-input"
                           placeholder="••••" maxlength="4" inputmode="numeric" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary">PINを再設定する</button>
            </form>

            <p class="back-link"><a href="/pin-reset">品番入力に戻る</a></p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.pin-input').forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>
