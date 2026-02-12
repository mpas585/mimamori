@extends('layouts.app')

@section('title', 'PIN再設定 - 初期PINで再設定')

@section('styles')
<style>
    .login-container {
        max-width: 400px;
        margin: 60px auto 0;
    }
    .login-logo {
        text-align: center;
        margin-bottom: 32px;
    }
    .login-logo .name {
        font-size: 20px;
        font-weight: 500;
        color: #8b7e6a;
    }
    .login-card {
        background: #fff;
        border-radius: 12px;
        padding: 32px 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .card-heading {
        font-size: 16px;
        font-weight: 500;
        color: #5a5245;
        text-align: center;
        margin-bottom: 8px;
    }
    .device-id-display {
        text-align: center;
        font-family: monospace;
        font-size: 18px;
        letter-spacing: 4px;
        color: #8b7e6a;
        background: #faf8f4;
        padding: 8px;
        border-radius: 6px;
        margin-bottom: 24px;
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
        font-size: 16px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        color: #4a4a4a;
        transition: border-color 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #8b7e6a;
        background: #fff;
    }
    .form-input::placeholder {
        color: #c0b8aa;
    }
    .form-input.pin {
        letter-spacing: 8px;
        text-align: center;
        font-size: 20px;
    }
    .form-hint {
        font-size: 11px;
        color: #aaa;
        margin-top: 4px;
    }
    .form-error {
        color: #c62828;
        font-size: 12px;
        margin-top: 4px;
    }
    .form-divider {
        border: none;
        border-top: 1px solid #e0d8cc;
        margin: 24px 0;
    }
    .section-label {
        font-size: 13px;
        font-weight: 500;
        color: #8b7e6a;
        margin-bottom: 16px;
    }
    .submit-btn {
        width: 100%;
        padding: 14px;
        background: #8b7e6a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        transition: background 0.2s;
    }
    .submit-btn:hover {
        background: #7a6e5b;
    }
    .login-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 12px;
        color: #aaa;
    }
    .login-footer a {
        color: #8b7e6a;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-logo">
        <span class="name">みまもりデバイス</span>
    </div>

    <div class="login-card">
        <div class="card-heading">初期PINでリセット</div>
        <div class="device-id-display">{{ $device_id }}</div>

        <form method="POST" action="/pin-reset/initial">
            @csrf

            <div class="form-group">
                <label class="form-label">初期PIN</label>
                <input
                    type="password"
                    name="initial_pin"
                    class="form-input pin"
                    placeholder="••••"
                    maxlength="4"
                    inputmode="numeric"
                    autocomplete="off"
                    autofocus
                >
                <div class="form-hint">端末ラベルまたは納品書に記載の数字4桁</div>
                @error('initial_pin')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr class="form-divider">
            <div class="section-label">新しいPINを設定</div>

            <div class="form-group">
                <label class="form-label">新しいPIN</label>
                <input
                    type="password"
                    name="new_pin"
                    class="form-input pin"
                    placeholder="••••"
                    maxlength="4"
                    inputmode="numeric"
                    autocomplete="new-password"
                >
                <div class="form-hint">数字4桁</div>
                @error('new_pin')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">新しいPIN（確認）</label>
                <input
                    type="password"
                    name="new_pin_confirmation"
                    class="form-input pin"
                    placeholder="••••"
                    maxlength="4"
                    inputmode="numeric"
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="submit-btn">PINを再設定する</button>
        </form>
    </div>

    <div class="login-footer">
        <a href="/pin-reset">← 品番入力に戻る</a>
    </div>
</div>
@endsection
