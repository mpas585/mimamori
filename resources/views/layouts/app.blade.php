<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'みまもりデバイス')</title>
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

        /* ヘッダー */
        .header {
            background: var(--white);
            border-bottom: 1px solid var(--gray-100);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-inner {
            max-width: 640px;
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
            color: inherit;
        }
        .header-logo-text { font-size: 16px; font-weight: 500; }
        .header-menu { display: flex; align-items: center; gap: 8px; }
        .header-btn {
            padding: 8px 12px; font-size: 13px; color: var(--gray-600);
            background: transparent; border: none; border-radius: var(--radius);
            cursor: pointer; transition: all 0.2s; text-decoration: none;
            font-family: 'Noto Sans JP', sans-serif;
        }
        .header-btn:hover { background: var(--beige); color: var(--gray-800); }

        .main-content {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px 20px 100px;
        }

        /* 汎用 */
        .btn {
            display: inline-block; padding: 12px 20px; font-size: 14px;
            font-weight: 600; font-family: 'Noto Sans JP', sans-serif;
            border: none; border-radius: var(--radius); cursor: pointer;
            transition: all 0.2s; text-decoration: none; text-align: center;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: var(--gray-800); color: var(--white); }
        .btn-secondary { background: var(--beige); color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-block { display: block; width: 100%; }

        .card {
            background: var(--white); border-radius: var(--radius-lg);
            padding: 24px; margin-bottom: 16px;
            box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200);
        }
        .card-title { font-size: 14px; font-weight: 500; color: var(--gray-500); margin-bottom: 16px; }

        /* トースト通知 */
        .toast {
            position: fixed; top: 80px; left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: var(--gray-800); color: var(--white);
            padding: 12px 24px; border-radius: var(--radius);
            font-size: 14px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            opacity: 0; visibility: hidden;
            transition: all 0.3s ease; z-index: 200;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toast.show {
            opacity: 1; visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
        .toast-icon { color: var(--green); }

        /* フラッシュメッセージ */
        .flash-success {
            background: var(--green-light); color: var(--green-dark);
            padding: 12px 16px; border-radius: var(--radius);
            margin-bottom: 16px; font-size: 13px;
        }
        .flash-error {
            background: var(--red-light); color: var(--red);
            padding: 12px 16px; border-radius: var(--radius);
            margin-bottom: 16px; font-size: 13px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .main-content { padding: 16px 16px 100px; }
        }
</style>
    @yield('styles')
</head>
<body>
    <!-- トースト通知 -->
    <div id="toast" class="toast">
        <span class="toast-icon">✓</span>
        <span id="toastText">保存しました</span>
    </div>

    @auth
        @section('header')
        <header class="header">
            <div class="header-inner">
                <a href="/mypage" class="header-logo">
                    <span class="header-logo-text">みまもりデバイス</span>
                </a>
                <div class="header-menu">
                    <a href="/settings" class="header-btn">⚙️ 設定</a>
                    <form method="POST" action="/logout" style="display:inline;">
                        @csrf
                        <button type="submit" class="header-btn">ログアウト</button>
                    </form>
                </div>
            </div>
        </header>
        @show
    @endauth

    <div class="main-content">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script>
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toastText').textContent = message || '保存しました';
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 2000);
        }
    </script>

    @yield('scripts')
</body>
</html>
