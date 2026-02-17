<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '管理画面') - みまもりデバイス</title>
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
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--gray-800);
        }
        .header-logo-icon {
            font-size: 20px;
        }
        .header-logo-text {
            font-size: 15px;
            font-weight: 600;
        }
        .header-org-name {
            font-size: 13px;
            color: var(--gray-500);
            margin-left: 12px;
            padding-left: 12px;
            border-left: 1px solid var(--gray-300);
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-link {
            font-size: 13px;
            color: var(--gray-500);
            text-decoration: none;
            padding: 6px 10px;
            border-radius: var(--radius);
            transition: all 0.2s;
        }
        .header-link:hover {
            background: var(--beige);
            color: var(--gray-700);
        }

        /* ===== コンテナ ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ===== PWAバナー ===== */
        .pwa-banner {
            background: var(--blue-light);
            border: 1px solid #bfdbfe;
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--blue);
        }
        .pwa-banner-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pwa-banner button {
            padding: 6px 12px;
            font-size: 12px;
            font-family: inherit;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        .pwa-add { background: var(--blue); color: var(--white); }
        .pwa-close { background: transparent; color: var(--gray-400); font-size: 16px; }

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

        /* ===== アラートバナー ===== */
        .alert-banner {
            padding: 14px 20px;
            margin-bottom: 12px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-banner.warning {
            background: var(--red-light);
            border: 1px solid #fecaca;
            border-left: 4px solid var(--red);
            color: var(--red);
        }
        .alert-banner.offline {
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
            border-left: 4px solid var(--gray-600);
            color: var(--gray-600);
        }
        .alert-banner strong { font-weight: 700; }
        .alert-banner-btn {
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
        }
        .alert-banner.warning .alert-banner-btn { background: var(--red); color: var(--white); }
        .alert-banner.offline .alert-banner-btn { background: var(--gray-600); color: var(--white); }

        /* ===== 共通ボタン ===== */
        .btn {
            padding: 10px 20px;
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
        .btn-danger { background: var(--red); color: var(--white); }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 8px 14px; font-size: 13px; }

        /* ===== カード ===== */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 16px;
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
        .form-group { margin-bottom: 16px; }
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
            padding: 10px 14px;
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

        @media (max-width: 768px) {
            .container { padding: 12px; }
            .header-org-name { display: none; }
        }

        @yield('styles')
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <div style="display:flex;align-items:center;">
                <a href="#" class="header-logo">
                    <span class="header-logo-icon">🧈</span>
                    <span class="header-logo-text">みまもりデバイス</span>
                </a>
                @if(isset($organization))
                    <span class="header-org-name">{{ $organization->name }} 管理画面</span>
                @endif
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.password-change') }}" class="header-link">🔐 パスワード変更</a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="header-link" style="border:none;background:none;cursor:pointer;font-family:inherit;">ログアウト</button>
                </form>
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
