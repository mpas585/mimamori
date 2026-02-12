@extends('layouts.app')

@section('title', 'ログイン - みまもりデバイス')

@section('styles')
<style>
    .main-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: calc(100vh - 40px);
        padding-bottom: 40px;
    }
    .login-container {
        max-width: 480px;
        margin: 0 auto;
        width: 100%;
    }

    /* ロゴ */
    .logo-area {
        text-align: center;
        margin-bottom: 48px;
        animation: fadeIn 0.6s ease;
    }
    .logo {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .logo-text {
        font-size: 24px;
        font-weight: 500;
        letter-spacing: 0.02em;
        color: var(--gray-800);
    }

    /* カード */
    .login-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border: 1px solid var(--gray-200);
        padding: 32px;
    }
    .login-card-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: var(--gray-800);
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    /* フォーム */
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        padding: 14px 16px;
        font-size: 15px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
        color: var(--gray-800);
        transition: all 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--gray-500);
        background: var(--white);
        box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
    }
    .form-input::placeholder {
        color: var(--gray-400);
    }
    .form-input.id-input {
        text-transform: uppercase;
        letter-spacing: 0.15em;
        text-align: center;
        font-size: 18px;
    }
    .form-hint {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 6px;
        font-weight: 500;
    }
    .form-error {
        color: var(--red);
        font-size: 12px;
        margin-top: 6px;
    }

    /* ボタン */
    .login-btn {
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
        background: var(--gray-800);
        color: var(--white);
    }
    .login-btn:hover {
        background: var(--gray-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }
    .forgot-link {
        text-align: center;
        margin-top: 16px;
    }
    .forgot-link a {
        font-size: 13px;
        color: var(--gray-500);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    .forgot-link a:hover {
        color: var(--gray-700);
        text-decoration: underline;
    }

    /* 区切り線 */
    .divider {
        display: flex;
        align-items: center;
        margin: 32px 0;
        color: var(--gray-500);
        font-size: 13px;
        font-weight: 500;
    }
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 2px;
        background: var(--gray-200);
    }
    .divider span {
        padding: 0 16px;
    }

    /* QRエリア */
    .qr-area {
        text-align: center;
    }
    .qr-box {
        display: inline-block;
        padding: 20px;
        background: var(--cream);
        border-radius: var(--radius-lg);
        margin-bottom: 12px;
        border: 1px solid var(--gray-200);
    }
    .qr-placeholder {
        width: 140px;
        height: 140px;
        background: var(--gray-100);
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 14px;
        font-weight: 500;
    }
    .qr-hint {
        font-size: 13px;
        color: var(--gray-500);
        font-weight: 500;
    }

    .security-note {
        font-size: 11px;
        color: var(--gray-400);
        text-align: center;
        margin-top: 24px;
    }

    @media (max-width: 480px) {
        .logo-text { font-size: 20px; }
    }
</style>
@endsection

@section('content')
<div class="login-container">

    {{-- ロゴエリア --}}
    <div class="logo-area">
        <div class="logo">
            <span class="logo-text">みまもりデバイス</span>
        </div>

    </div>

    {{-- ログインカード --}}
    <div class="login-card">
        <h1 class="login-card-title">ログイン</h1>

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label class="form-label" for="deviceId">デバイスID</label>
                <input
                    type="text"
                    id="deviceId"
                    name="device_id"
                    class="form-input id-input"
                    placeholder="A3K9X2"
                    value="{{ old('device_id') }}"
                    maxlength="6"
                    autocomplete="off"
                    autofocus
                    required
                >
                <p class="form-hint">製品ラベルに記載の6文字</p>
                @error('device_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="pin">PIN</label>
                <input
                    type="password"
                    id="pin"
                    name="pin"
                    class="form-input id-input"
                    placeholder="••••"
                    maxlength="4"
                    inputmode="numeric"
                    autocomplete="current-password"
                    required
                >
                <p class="form-hint">製品ラベルに記載の4桁</p>
                @error('pin')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-btn">ログイン</button>
            <p class="forgot-link"><a href="/pin-reset">PINを忘れた場合</a></p>
        </form>

        <div class="divider"><span>または</span></div>

        <div class="qr-area">
            <div class="qr-box">
                <div class="qr-placeholder">QRスキャン</div>
            </div>
            <p class="qr-hint">製品ラベルのQRコードを読み取り</p>
        </div>

        <p class="security-note">※連続してログインに失敗すると一定時間操作できなくなります</p>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.getElementById('deviceId').addEventListener('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (this.value.length > 6) this.value = this.value.slice(0, 6);
});
document.getElementById('pin').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 4) this.value = this.value.slice(0, 4);
});
</script>
@endsection
