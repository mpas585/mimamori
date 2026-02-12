@extends('layouts.app')

@section('title', 'PIN再設定 - 方法選択')

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
    .method-card {
        display: block;
        width: 100%;
        padding: 16px;
        background: #faf8f4;
        border: 1px solid #e0d8cc;
        border-radius: 8px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }
    .method-card:hover {
        background: #f0ebe1;
        border-color: #8b7e6a;
    }
    .method-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .method-card.disabled:hover {
        background: #faf8f4;
        border-color: #e0d8cc;
    }
    .method-title {
        font-size: 14px;
        font-weight: 500;
        color: #5a5245;
        margin-bottom: 4px;
    }
    .method-desc {
        font-size: 12px;
        color: #999;
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
        <div class="card-heading">PIN再設定方法を選択</div>
        <div class="device-id-display">{{ $device_id }}</div>

        {{-- メールでPIN再設定 --}}
        @if($has_email)
            <form method="POST" action="/pin-reset/send-email">
                @csrf
                <button type="submit" class="method-card">
                    <div class="method-title">📧 メールでPIN再設定</div>
                    <div class="method-desc">{{ $masked_email }} にリセットリンクを送信します</div>
                </button>
            </form>
        @else
            <div class="method-card disabled">
                <div class="method-title">📧 メールでPIN再設定</div>
                <div class="method-desc">メールアドレスが未登録のため利用できません</div>
            </div>
        @endif

        {{-- 初期PINでリセット --}}
        <form method="GET" action="/pin-reset/initial">
            <button type="submit" class="method-card">
                <div class="method-title">🏷️ 初期PINでリセット</div>
                <div class="method-desc">端末ラベルに記載の初期PINを使って再設定します</div>
            </button>
        </form>
    </div>

    <div class="login-footer">
        <a href="/pin-reset">← 品番入力に戻る</a>
    </div>
</div>
@endsection
