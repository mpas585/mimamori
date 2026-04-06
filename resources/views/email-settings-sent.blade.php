@extends('layouts.app')

@section('title', '確認メール送信完了 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <div style="width:36px;"></div>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">📧 確認メール送信完了</h1>
        <div style="width:36px;"></div>
    </div>
</header>
@endsection

@section('styles')
<style>
    .section {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        text-align: center;
    }
    .sent-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .sent-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 16px;
    }
    .sent-text {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.8;
        margin-bottom: 24px;
    }
    .sent-email {
        font-weight: 700;
        color: var(--gray-800);
    }
    .info-box {
        text-align: left;
        padding: 14px 16px;
        background: var(--blue-light);
        border-radius: var(--radius);
        font-size: 13px;
        color: var(--gray-700);
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .info-box strong {
        color: var(--gray-800);
    }
    .info-box ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }
    .info-box li {
        margin-bottom: 4px;
    }
    .btn-back {
        display: inline-block;
        padding: 12px 32px;
        background: var(--beige);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-back:hover { background: var(--gray-200); }
</style>
@endsection

@section('content')
<section class="section">
    <div class="sent-icon">📨</div>
    <h2 class="sent-title">確認メールを送信しました</h2>
    <p class="sent-text">
        <span class="sent-email">{{ $maskedEmail }}</span> 宛に<br>
        確認メールを送信しました。<br>
        メール内のリンクをクリックして登録を完了してください。
    </p>

    <div class="info-box">
        <strong>💡 メールが届かない場合</strong>
        <ul>
            <li>迷惑メールフォルダをご確認ください</li>
            <li>メールアドレスが正しいかご確認ください</li>
            <li>リンクの有効期限は <strong>24時間</strong> です</li>
        </ul>
    </div>

    <a href="/settings" class="btn-back">設定に戻る</a>
</section>
@endsection


