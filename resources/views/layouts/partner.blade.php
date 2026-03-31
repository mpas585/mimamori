<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'みまもりトーフ 管理画面')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --beige: #f5f0e8;
            --cream: #faf8f4;
            --white: #ffffff;
            --gray-100: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-400: #a3a3a3;
            --gray-500: #737373;
            --gray-600: #525252;
            --gray-700: #404040;
            --gray-800: #262626;
            --green: #4caf50;
            --green-dark: #2e7d32;
            --green-light: #e8f5e9;
            --yellow: #ffc107;
            --yellow-light: #fff8e1;
            --orange: #ff9800;
            --red: #ef5350;
            --red-light: #fbe9e7;
            --radius: 8px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: var(--beige);
            color: var(--gray-800);
            min-height: 100vh;
        }

        /* ナビゲーション */
        .nav {
            background: var(--gray-800);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 52px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand {
            font-size: 15px;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-brand .org-name {
            font-size: 12px;
            font-weight: 400;
            color: var(--gray-400);
            margin-left: 8px;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .nav-link {
            font-size: 13px;
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.2s;
        }
        .nav-link:hover { color: var(--white); }

        /* メインコンテンツ */
        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        /* カード */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 16px;
        }

        /* ボタン */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--gray-800);
            color: var(--white);
        }
        .btn-primary:hover { background: var(--gray-700); }
        .btn-secondary {
            background: var(--white);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }
        .btn-secondary:hover { background: var(--beige); }
        .btn-danger {
            background: var(--red);
            color: var(--white);
        }
        .btn-danger:hover { background: #c62828; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* フォーム */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--gray-800);
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--gray-500);
            box-shadow: 0 0 0 3px rgba(168,162,158,0.15);
        }
        .form-hint {
            font-size: 11px;
            color: var(--gray-400);
            margin-top: 4px;
        }

        /* アラートバナー */
        .alert-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            font-size: 13px;
            font-weight: 500;
        }
        .alert-banner.warning {
            background: #fbe9e7;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .alert-banner.offline {
            background: var(--gray-100);
            color: var(--gray-600);
            border: 1px solid var(--gray-300);
        }
        .alert-banner-btn {
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            border: 1px solid currentColor;
            border-radius: 4px;
            background: transparent;
            color: inherit;
            cursor: pointer;
            white-space: nowrap;
            margin-left: 12px;
        }

        /* モーダル */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.show { display: flex; }
        .modal {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--gray-200);
        }
        .modal-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-800);
        }
        .modal-close {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--gray-400);
            background: transparent;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
        }
        .modal-close:hover { background: var(--gray-100); color: var(--gray-700); }
        .modal-body { padding: 20px; }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }

        /* フラッシュメッセージ */
        .flash-success {
            background: var(--green-light);
            color: var(--green-dark);
            border: 1px solid #c8e6c9;
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }
        .flash-error {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid #ffcdd2;
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) {
            .nav { padding: 0 12px; }
            .main { padding: 16px 12px; }
        }
    </style>
    <style>@yield('styles')</style>
</head>
<body>
    <nav class="nav">
        <a href="/partner" class="nav-brand">
            🧈 みまもりトーフ
            @if(isset($organization))
                <span class="org-name">{{ $organization->name }} 管理画面</span>
            @endif
        </a>
        <div class="nav-right">
            <a href="{{ route('partner.password-change') }}" class="nav-link">🔐 アカウント設定</a>
        </div>
    </nav>

    <main class="main">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
