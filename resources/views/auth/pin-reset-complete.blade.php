@extends('layouts.app')

@section('title', 'PIN再設定 - 完了')

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
    .login-btn {
        display: inline-block;
        padding: 14px 48px;
        background: #8b7e6a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
    }
    .login-btn:hover {
        background: #7a6e5b;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-logo">
        <span class="name">みまもりデバイス</span>
    </div>

    <div class="login-card">
        <div class="icon-large">✅</div>
        <div class="card-heading">PINを再設定しました</div>
        <div class="card-message">
            新しいPINでログインしてください。
        </div>

        <a href="/login" class="login-btn">ログインへ</a>
    </div>
</div>
@endsection
