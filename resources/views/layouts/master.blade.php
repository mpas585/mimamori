<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'マスター管理') - みまもりトーフ</title>
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
            --green-light: #dcfce7;
            --green: #22c55e;
            --green-dark: #16a34a;
            --yellow-light: #fef9c3;
            --yellow: #eab308;
            --red-light: #fee2e2;
            --red: #ef4444;
            --blue-light: #dbeafe;
            --blue: #3b82f6;
            --purple-light: #f3e8ff;
            --purple: #a855f7;
            --orange-light: #ffedd5;
            --orange: #f97316;
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

        /* ===== ヘッダー ===== */
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-bottom: 1px solid var(--gray-700);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--white);
        }
        .header-logo-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .header-logo-text {
            font-size: 16px;
            font-weight: 500;
        }
        .header-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            background: var(--purple);
            color: var(--white);
            border-radius: 4px;
            margin-left: 8px;
        }
        .header-nav {
            display: flex;
            gap: 4px;
        }
        .header-nav-item {
            padding: 8px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: var(--radius);
            transition: all 0.2s;
        }
        .header-nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: var(--white);
        }
        .header-nav-item.active {
            background: rgba(255,255,255,0.15);
            color: var(--white);
            font-weight: 600;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .header-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--white);
            font-size: 13px;
        }
        .header-user-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .header-btn {
            padding: 8px 12px;
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            background: transparent;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .header-btn:hover {
            background: rgba(255,255,255,0.1);
            color: var(--white);
        }

        /* ===== コンテナ ===== */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        /* ===== フラッシュメッセージ ===== */
        .flash-success {
            background: var(--green-light);
            border: 1px solid #bbf7d0;
            border-left: 4px solid var(--green);
            color: var(--green-dark);
            padding: 14px 20px;
            margin-bottom: 16px;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 500;
        }
        .flash-error {
            background: var(--red-light);
            border: 1px solid #fecaca;
            border-left: 4px solid var(--red);
            color: var(--red);
            padding: 14px 20px;
            margin-bottom: 16px;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 500;
        }

        /* ===== 共通ボタン ===== */
        .btn {
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary { background: var(--gray-800); color: var(--white); }
        .btn-primary:hover { background: var(--gray-700); }
        .btn-secondary { background: var(--beige); color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-secondary:hover { background: var(--gray-200); }
        .btn-success { background: var(--green); color: var(--white); }
        .btn-success:hover { background: var(--green-dark); }
        .btn-sm { padding: 8px 14px; font-size: 13px; }

        /* ===== 共通カード ===== */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== モーダル ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            padding: 20px;
        }
        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal {
            background: var(--white);
            width: 100%;
            max-width: 500px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 { font-size: 18px; font-weight: 600; }
        .modal-close {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--beige);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
        }
        .modal-close:hover { background: var(--gray-200); }
        .modal-body { padding: 24px; max-height: 60vh; overflow-y: auto; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        /* ===== フォーム ===== */
        .form-group { margin-bottom: 20px; }
        .form-group:last-child { margin-bottom: 0; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            font-family: inherit;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: var(--cream);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--gray-500);
            background: var(--white);
        }
        .form-hint {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 4px;
        }

        @media (max-width: 1024px) {
            .header-nav { display: none; }
        }
        @media (max-width: 768px) {
            .container { padding: 16px; }
            .header-inner { padding: 12px 16px; }
        }

        @yield('styles')
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-left">
                <a href="{{ route('admin.dashboard') }}" class="header-logo">
                    <div class="header-logo-icon">🧈</div>
                    <span class="header-logo-text">みまもりトーフ</span>
                    <span class="header-badge">MASTER</span>
                </a>
                <nav class="header-nav">
                    @yield('nav')
                </nav>
            </div>
            <div class="header-right">
                @auth('admin')
                    <div class="header-user">
                        <span class="header-user-icon">👤</span>
                        <span>{{ Auth::guard('admin')->user()->name }}</span>
                    </div>
                    <a href="#" class="header-btn">🔐 パスワード変更</a>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="header-btn">ログアウト</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
