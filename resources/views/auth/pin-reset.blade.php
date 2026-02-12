@extends('layouts.app')

@section('title', 'PIN再設定 - みまもりデバイス')

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
    .form-input.device-id {
        text-transform: uppercase;
        letter-spacing: 4px;
        text-align: center;
        font-family: monospace;
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
    .security-note {
        font-size: 11px;
        color: #aaa;
        text-align: center;
        margin-top: 16px;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-logo">
        <span class="name">みまもりデバイス</span>
    </div>

    <div class="login-card">
        <div class="card-heading">PIN再設定</div>

        <form method="POST" action="/pin-reset">
            @csrf

            <div class="form-group">
                <label class="form-label">品番（デバイスID）</label>
                <input
                    type="text"
                    name="device_id"
                    class="form-input device-id"
                    placeholder="A3K9X2"
                    value="{{ old('device_id') }}"
                    maxlength="6"
                    autocomplete="off"
                    autofocus
                >
                <div class="form-hint">端末に記載の英数字6文字</div>
                @error('device_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">次へ</button>
        </form>

        <p class="security-note">※連続して入力を間違えると一定時間操作できなくなります</p>
    </div>

    <div class="login-footer">
        <a href="/login">← ログインに戻る</a>
    </div>
</div>
@endsection
