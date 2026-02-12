@extends('layouts.admin')

@section('title', 'ログイン - 管理画面')

@section('styles')
<style>
    .login-container {
        max-width: 380px;
        margin: 80px auto 0;
    }
    .login-logo {
        text-align: center;
        margin-bottom: 32px;
    }
    .login-logo .name {
        font-size: 18px;
        font-weight: 500;
        color: #5a5245;
    }
    .login-logo .badge {
        display: inline-block;
        background: #5a5245;
        color: #fff;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .login-card {
        background: #fff;
        border-radius: 12px;
        padding: 32px 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #6b6358;
        margin-bottom: 6px;
    }
    .form-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d8d0c4;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        color: #4a4a4a;
        transition: border-color 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #5a5245;
        background: #fff;
    }
    .form-error {
        color: #c62828;
        font-size: 12px;
        margin-top: 4px;
    }
    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }
    .remember-row label {
        font-size: 13px;
        color: #888;
    }
    .login-btn {
        width: 100%;
        padding: 14px;
        background: #5a5245;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        transition: background 0.2s;
    }
    .login-btn:hover {
        background: #4a4338;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-logo">
        <span class="name">みまもりデバイス</span>
        <span class="badge">ADMIN</span>
    </div>

    <div class="login-card">
        <form method="POST" action="/admin/login">
            @csrf

            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input
                    type="email"
                    name="email"
                    class="form-input"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">パスワード</label>
                <input
                    type="password"
                    name="password"
                    class="form-input"
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">ログイン状態を保持する</label>
            </div>

            <button type="submit" class="login-btn">ログイン</button>
        </form>
    </div>
</div>
@endsection
