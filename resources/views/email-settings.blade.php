@extends('layouts.app')

@section('title', 'メールアドレス設定 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <a href="/settings" class="back-btn">←</a>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">📧 メールアドレス設定</h1>
        <span style="font-size:13px;font-weight:600;color:var(--gray-500);font-family:monospace;letter-spacing:0.05em;">{{ $device->device_id }}</span>
    </div>
</header>
@endsection

@section('styles')
<style>
    .back-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--beige);
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
        text-decoration: none;
        color: var(--gray-700);
        flex-shrink: 0;
    }
    .back-btn:hover { background: var(--gray-200); }

    .section {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gray-200);
    }
    .section-title span { font-size: 18px; }

    /* 現在の登録状況 */
    .current-email-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--beige);
        border-radius: var(--radius);
        margin-bottom: 20px;
    }
    .current-email-icon {
        width: 44px;
        height: 44px;
        background: var(--white);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        border: 1px solid var(--gray-200);
    }
    .current-email-info { flex: 1; min-width: 0; }
    .current-email-label {
        font-size: 12px;
        color: var(--gray-500);
        font-weight: 500;
    }
    .current-email-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-800);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .current-email-none {
        font-size: 15px;
        font-weight: 600;
        color: var(--red);
    }

    /* フォーム */
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        padding: 12px 14px;
        font-size: 16px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
        transition: all 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--gray-500);
        background: var(--white);
    }
    .form-hint {
        font-size: 11px;
        color: var(--gray-500);
        margin-top: 6px;
    }
    .form-error {
        font-size: 12px;
        color: var(--red);
        margin-top: 6px;
    }

    .btn-primary {
        width: 100%;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        background: var(--gray-800);
        color: var(--white);
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary:hover { opacity: 0.85; }
    .btn-primary:active { transform: scale(0.98); }

    /* 削除セクション */
    .delete-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
    }
    .delete-section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 12px;
    }
    .btn-danger {
        width: 100%;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        background: var(--red-light);
        color: var(--red);
        border: 1px solid var(--red);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger:hover { opacity: 0.85; }
</style>
@endsection

@section('content')
{{-- 現在の登録状況 --}}
<section class="section">
    <h2 class="section-title"><span>📧</span>現在の登録状況</h2>
    <div class="current-email-box">
        <div class="current-email-icon">✉️</div>
        <div class="current-email-info">
            <p class="current-email-label">通知先メールアドレス</p>
            @if($currentEmail)
                <p class="current-email-value">{{ $currentEmail }}</p>
            @else
                <p class="current-email-none">未登録</p>
            @endif
        </div>
    </div>
</section>

{{-- メールアドレス変更 --}}
<section class="section">
    <h2 class="section-title"><span>✏️</span>{{ $currentEmail ? 'メールアドレスを変更' : 'メールアドレスを登録' }}</h2>

    <form method="POST" action="/email-settings/send">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">{{ $currentEmail ? '新しいメールアドレス' : 'メールアドレス' }}</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="example@email.com"
                value="{{ old('email') }}"
                autocomplete="off"
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email_confirmation">メールアドレス（確認）</label>
            <input
                type="email"
                id="email_confirmation"
                name="email_confirmation"
                class="form-input"
                placeholder="example@email.com"
                value="{{ old('email_confirmation') }}"
                autocomplete="off"
                required
            >
            <p class="form-hint">確認のためもう一度入力してください</p>
            @error('email_confirmation')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">確認メールを送信</button>
    </form>

    {{-- メアド削除（登録済みの場合のみ） --}}
    @if($currentEmail)
        <div class="delete-section">
            <p class="delete-section-title">メールアドレスの削除</p>
            <form method="POST" action="/email-settings/delete" id="deleteForm">
                @csrf
                <button type="button" class="btn-danger" onclick="confirmDelete()">メールアドレスを削除する</button>
            </form>
        </div>
    @endif
</section>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    if (confirm('メールアドレスを削除しますか？\nメール通知は無効になります。')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection


