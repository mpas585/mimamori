<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '管理画面 - みまもりデバイス')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #f0ede6;
            color: #4a4a4a;
            min-height: 100vh;
        }
        .header {
            background: #5a5245;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-logo {
            font-size: 14px;
            color: #e0d8cc;
            text-decoration: none;
        }
        .header-logo span { margin-right: 4px; }
        .header-badge {
            background: #8b7e6a;
            color: #fff;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
        }
        .header-nav {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .header-nav span {
            font-size: 12px;
            color: #c0b8aa;
        }
        .header-nav button {
            background: none;
            border: none;
            color: #c0b8aa;
            font-size: 12px;
            cursor: pointer;
            font-family: 'Noto Sans JP', sans-serif;
        }
        .header-nav button:hover { color: #fff; }
        .main-content {
            max-width: 960px;
            margin: 0 auto;
            padding: 24px 16px;
        }
        .btn {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 6px;
            border: none;
            font-family: 'Noto Sans JP', sans-serif;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: #5a5245; color: #fff; }
        .btn-secondary { background: #e0d8cc; color: #5a5245; }
        .btn-sm { padding: 6px 14px; font-size: 12px; }
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
    @auth('admin')
    <div class="header">
        <a href="/admin" class="header-logo">
            みまもりデバイス<span class="header-badge">ADMIN</span>
        </a>
        <div class="header-nav">
            <span>{{ Auth::guard('admin')->user()->name }}</span>
            <form method="POST" action="/admin/logout" style="display:inline;">
                @csrf
                <button type="submit">ログアウト</button>
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
