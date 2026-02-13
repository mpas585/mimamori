@extends('layouts.app')

@section('title', 'メールアドレス確認 - みまもりデバイス')

@section('styles')
<style>
    .content-area {
        max-width: 640px;
        margin: 0 auto;
        padding: 60px 20px;
    }
    .card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 40px 24px;
        box-shadow: var(--shadow-sm);
        text-align: center;
    }
    .result-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .result-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 16px;
    }
    .result-text {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.8;
    }
    .btn-primary {
        display: inline-block;
        padding: 14px 40px;
        background: var(--gray-800);
        color: var(--white);
        border: none;
        border-radius: var(--radius);
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 28px;
    }
    .btn-primary:hover { background: var(--gray-700); }

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
        margin-top: 12px;
    }
    .btn-secondary:hover { background: var(--gray-200); }

    .app-header { display: none; }
    .main-content { padding-top: 0; }
</style>
@endsection

@section('content')
<div class="content-area">
    <div class="card">
        @if($success)
            <div class="result-icon">✅</div>
            <h2 class="result-title">{{ $message }}</h2>
            <p class="result-text">
                メール通知が有効になりました。<br>
                未検知アラートや電池残量低下の通知をお届けします。
            </p>
            <a href="{{ url('/mypage') }}" class="btn-primary">マイページへ</a><br>
            <a href="{{ url('/settings') }}" class="btn-secondary">設定を確認する</a>
        @else
            <div class="result-icon">❌</div>
            <h2 class="result-title">確認に失敗しました</h2>
            <p class="result-text">{{ $message }}</p>
            <a href="{{ url('/email-settings') }}" class="btn-primary">メールアドレスを再登録</a><br>
            <a href="{{ url('/mypage') }}" class="btn-secondary">マイページへ</a>
        @endif
    </div>
</div>
@endsection
