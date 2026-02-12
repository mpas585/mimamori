<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN再設定 - みまもりデバイス</title>
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
            --green-light: #f0fdf4;
            --green: #22c55e;
            --green-dark: #15803d;
            --red-light: #fef2f2;
            --red: #ef4444;
            --yellow-light: #fefce8;
            --yellow: #eab308;
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
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 500;
            letter-spacing: 0.02em;
            color: var(--gray-800);
        }
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
            padding: 32px;
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
        .form-input.id-input {
            text-transform: uppercase;
            letter-spacing: 0.15em;
            text-align: center;
            font-size: 18px;
        }
        .form-hint { font-size: 12px; color: var(--gray-500); margin-top: 6px; font-weight: 500; }
        .form-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 6px;
            font-weight: 500;
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
        .security-note { font-size: 11px; color: var(--gray-400); text-align: center; margin-top: 16px; }
        .message {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 14px;
        }
        .message.error { background: var(--red-light); color: var(--red); border: 1px solid var(--red); }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card { animation: fadeIn 0.4s ease; }
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
            <h1 class="card-title">PIN再設定</h1>
            <p class="card-desc">デバイスIDを入力してください</p>

            @if(session('error'))
                <div class="message error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="/pin-reset">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="deviceId">デバイスID</label>
                    <input type="text" id="deviceId" name="device_id" class="form-input id-input"
                           placeholder="A3K9X2" value="{{ old('device_id') }}"
                           maxlength="6" autocomplete="off" autofocus>
                    <p class="form-hint">製品ラベルに記載の6文字</p>
                    @error('device_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">次へ</button>
            </form>

            <p class="back-link"><a href="/login">ログインに戻る</a></p>
            <p class="security-note">※連続して入力を間違えると一定時間操作できなくなります</p>
        </div>
    </div>

    <script>
        document.getElementById('deviceId').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    </script>
</body>
</html>
