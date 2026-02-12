<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN再設定 - 方法選択</title>
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
        .option-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }
        .option-card {
            display: block;
            width: 100%;
            padding: 20px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
            background: var(--white);
            font-family: inherit;
        }
        .option-card:hover { border-color: var(--gray-400); background: var(--cream); }
        .option-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .option-card.disabled:hover { border-color: var(--gray-200); background: var(--white); }
        .option-card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .option-card-desc { font-size: 13px; color: var(--gray-500); line-height: 1.5; }
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
            <h1 class="card-title">再設定方法の選択</h1>
            <p class="card-desc">デバイス <span class="device-id-badge">{{ $device_id }}</span></p>

            <div class="option-cards">
                @if($has_email)
                    <form method="POST" action="/pin-reset/send-email">
                        @csrf
                        <button type="submit" class="option-card">
                            <p class="option-card-title">メールでPIN再設定</p>
                            <p class="option-card-desc">{{ $masked_email }} に再設定リンクを送信します</p>
                        </button>
                    </form>
                @else
                    <div class="option-card disabled">
                        <p class="option-card-title">メールでPIN再設定</p>
                        <p class="option-card-desc">メールアドレスが未登録のため利用できません</p>
                    </div>
                @endif

                <form method="GET" action="/pin-reset/initial">
                    <button type="submit" class="option-card">
                        <p class="option-card-title">初期PINにリセット</p>
                        <p class="option-card-desc">製品ラベル記載の初期PINを使って再設定します</p>
                    </button>
                </form>
            </div>

            <p class="back-link"><a href="/pin-reset">デバイスIDを修正</a></p>
        </div>
    </div>
</body>
</html>
