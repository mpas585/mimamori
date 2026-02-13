@extends('layouts.app')

@section('title', '確認メール送信完了 - みまもりデバイス')

@section('styles')
<style>
    .page-header {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        padding: 16px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .page-header-inner {
        max-width: 640px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 16px;
    }
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
        text-decoration: none;
        color: var(--gray-700);
        transition: background 0.2s;
    }
    .back-btn:hover { background: var(--gray-200); }
    .page-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }

    .content-area {
        max-width: 640px;
        margin: 0 auto;
        padding: 20px;
    }
    .card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 40px 24px;
        box-shadow: var(--shadow-sm);
        text-align: center;
    }
    .success-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .success-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 16px;
    }
    .success-text {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.8;
        margin-bottom: 8px;
    }
    .success-highlight {
        font-weight: 600;
        color: var(--gray-800);
    }
    .expiry-note {
        font-size: 12px;
        color: var(--gray-400);
        margin-top: 8px;
    }

    .mail-help {
        background: var(--beige);
        border-radius: var(--radius);
        padding: 16px;
        margin-top: 24px;
        text-align: left;
    }
    .mail-help-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    .mail-help-list {
        font-size: 12px;
        color: var(--gray-500);
        margin: 0;
        padding-left: 20px;
    }
    .mail-help-list li {
        margin-bottom: 4px;
    }
    .mail-help-list li:last-child { margin-bottom: 0; }

    .btn-secondary {
        display: inline-block;
        padding: 12px 32px;
        background: var(--beige);
        color: var(--gray-700);
        border: none;
        border-radius: var(--radius);
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 24px;
    }
    .btn-secondary:hover { background: var(--gray-200); }

    .app-header { display: none; }
    .main-content { padding-top: 0; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="{{ route('settings') }}" class="back-btn">←</a>
        <div class="page-title">メールアドレス設定</div>
    </div>
</div>

<div class="content-area">
    <div class="card">
        <div class="success-icon">✉️</div>
        <h2 class="success-title">確認メールを送信しました</h2>
        <p class="success-text">
            <span class="success-highlight">{{ $maskedEmail }}</span> に<br>
            確認メールを送信しました。
        </p>
        <p class="success-text">
            メール内のリンクをクリックすると<br>
            メールアドレスの登録が完了します。
        </p>
        <p class="expiry-note">リンクの有効期限は24時間です</p>

        <div class="mail-help">
            <p class="mail-help-title">メールが届かない場合</p>
            <ul class="mail-help-list">
                <li>迷惑メールフォルダをご確認ください</li>
                <li>メールアドレスが正しいかご確認ください</li>
                <li>数分待っても届かない場合は再度お試しください</li>
            </ul>
        </div>

        <a href="{{ route('settings') }}" class="btn-secondary">設定に戻る</a>
    </div>
</div>
@endsection
