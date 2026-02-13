@extends('layouts.app')

@section('title', 'メールアドレス確認 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <div style="width:36px;"></div>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">📧 メールアドレス確認</h1>
        <div style="width:36px;"></div>
    </div>
</header>
@endsection

@section('styles')
<style>
    .section {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 40px 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        text-align: center;
    }
    .result-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .result-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 16px;
    }
    .result-text {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.8;
        margin-bottom: 32px;
    }
    .result-email {
        font-weight: 700;
        color: var(--gray-800);
    }
    .btn-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn-actions .btn {
        display: inline-block;
        padding: 12px 32px;
        font-size: 14px;
        font-weight: 600;
        border-radius: var(--radius);
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-actions .btn-primary {
        background: var(--gray-800);
        color: var(--white);
        border: none;
    }
    .btn-actions .btn-primary:hover { opacity: 0.85; }
    .btn-actions .btn-secondary {
        background: var(--beige);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    .btn-actions .btn-secondary:hover { background: var(--gray-200); }
</style>
@endsection

@section('content')
<section class="section">
    @if($success)
        <div class="result-icon">✅</div>
        <h2 class="result-title">登録完了</h2>
        <p class="result-text">
            {{ $message }}<br><br>
            @if(isset($email))
                <span class="result-email">{{ $email }}</span><br>
                が通知先として設定されました。
            @endif
        </p>
        <div class="btn-actions">
            <a href="/mypage" class="btn btn-primary">マイページへ</a>
            <a href="/settings" class="btn btn-secondary">設定へ</a>
        </div>
    @else
        <div class="result-icon">❌</div>
        <h2 class="result-title">確認失敗</h2>
        <p class="result-text">{{ $message }}</p>
        <div class="btn-actions">
            <a href="/email-settings" class="btn btn-primary">もう一度設定する</a>
        </div>
    @endif
</section>
@endsection
