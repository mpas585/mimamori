<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN再設定 - 完了</title>
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
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--green-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
        }
        .success-title {
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 12px;
            color: var(--gray-800);
        }
        .success-text {
            font-size: 14px;
            color: var(--gray-500);
            text-align: center;
            line-height: 1.7;
            margin-bottom: 24px;
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
            <div class="success-icon">✅</div>
            <h2 class="success-title">PINを再設定しました</h2>
            <p class="success-text">新しいPINでログインしてください。</p>
            <a href="/login" class="btn btn-primary">ログインへ</a>
        </div>
    </div>
</body>
</html>
