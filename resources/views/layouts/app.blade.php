<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'みまもりデバイス')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #f5f0e8;
            color: #4a4a4a;
            min-height: 100vh;
        }
        .header {
            background: #fff;
            border-bottom: 1px solid #e0d8cc;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-logo {
            font-size: 16px;
            font-weight: 500;
            color: #8b7e6a;
            text-decoration: none;
        }
        .header-logo span {
            font-size: 18px;
            margin-right: 4px;
        }
        .header-nav {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .header-nav a {
            color: #8b7e6a;
            text-decoration: none;
            font-size: 13px;
        }
        .header-nav a:hover {
            color: #5a5245;
        }
        .header-device-id {
            font-size: 12px;
            color: #aaa;
            font-family: monospace;
        }
        .main-content {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px 16px;
        }
        .btn {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 6px;
            border: none;
            font-family: 'Noto Sans JP', sans-serif;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.85;
        }
        .btn-primary {
            background: #8b7e6a;
            color: #fff;
        }
        .btn-secondary {
            background: #e0d8cc;
            color: #5a5245;
        }
        .btn-block {
            display: block;
            width: 100%;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .card-title {
            font-size: 14px;
            font-weight: 500;
            color: #8b7e6a;
            margin-bottom: 16px;
        }
        @yield('styles')
    </style>
</head>
<body>
    @auth
    <div class="header">
        <a href="/mypage" class="header-logo">
            <span>🧈</span>みまもりデバイス
        </a>
        <div class="header-nav">
            <span class="header-device-id">{{ Auth::user()->device_id }}</span>
            <a href="/mypage">マイページ</a>
            <a href="/settings">設定</a>
            <form method="POST" action="/logout" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;color:#8b7e6a;font-size:13px;cursor:pointer;font-family:'Noto Sans JP',sans-serif;">ログアウト</button>
            </form>
        </div>
    </div>
    @endauth

    <div class="main-content">
        @if(session('success'))
            <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fbe9e7;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
