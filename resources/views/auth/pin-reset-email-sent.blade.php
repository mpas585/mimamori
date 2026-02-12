@extends('layouts.app')

@section('title', 'PIN再設定 - メール送信完了')

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
        text-align: center;
    }
    .icon-large {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .card-heading {
        font-size: 16px;
        font-weight: 500;
        color: #5a5245;
        margin-bottom: 12px;
    }
    .card-message {
        font-size: 13px;
        color: #888;
        line-height: 1.8;
        margin-bottom: 24px;
    }
    .card-message strong {
        color: #5a5245;
    }
    .note-box {
        background: #fffde7;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 12px;
        color: #666;
        text-align: left;
        line-height: 1.7;
    }
    .note-box strong {
        color: #f57c00;
    }
    .debug-box {
        background: #fbe9e7;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 11px;
        color: #c62828;
        text-align: left;
        margin-top: 16px;
        word-break: break-all;
    }
    .debug-box a {
        color: #c62828;
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
        <div class="icon-large">📧</div>
        <div class="card-heading">メールを送信しました</div>
        <div class="card-message">
            <strong>{{ $masked_email }}</strong> にPIN再設定リンクを送信しました。<br>
            メールに記載のリンクから新しいPINを設定してください。
        </div>

        <div class="note-box">
            <strong>ご注意：</strong><br>
            ・リンクの有効期限は1時間です<br>
            ・メールが届かない場合は迷惑メールフォルダをご確認ください
        </div>

        @if(isset($debug_token))
        <div class="debug-box">
            ⚠️ 開発モード：メール送信は未実装です<br>
            リセットリンク：<a href="{{ $debug_url }}">{{ $debug_url }}</a>
        </div>
        @endif
    </div>

    <div class="login-footer">
        <a href="/login">← ログインに戻る</a>
    </div>
</div>
@endsection
