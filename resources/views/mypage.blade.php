@extends('layouts.app')

@section('title', 'マイページ - みまもりデバイス')

@section('styles')
<style>
    /* アラートバナー */
    .alert-banner {
        border-radius: var(--radius-lg);
        padding: 14px 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        animation: fadeIn 0.4s ease;
    }
    .alert-banner.warning {
        background: var(--red-light);
        border: 1px solid #fca5a5;
    }
    .alert-banner.offline {
        background: var(--gray-100);
        border: 1px solid var(--gray-300);
    }
    .alert-banner span {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-700);
        flex: 1;
    }
    .alert-banner strong {
        font-weight: 700;
    }
    .alert-dismiss-btn {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        color: var(--gray-600);
        background: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .alert-dismiss-btn:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }

    /* 通知先未登録バナー */
    .notify-banner {
        background: var(--yellow-light);
        border: 1px solid #fde68a;
        border-radius: var(--radius-lg);
        padding: 12px 16px;
        margin-bottom: 12px;
        animation: fadeIn 0.4s ease;
    }
    .notify-banner-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .notify-banner-text {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    .notify-banner-text span { font-size: 18px; }
    .notify-banner-text p { font-size: 14px; font-weight: 600; color: var(--gray-700); }
    .notify-banner-btn {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-800);
        background: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        text-decoration: none;
    }
    .notify-banner-btn:hover { background: var(--gray-100); }

    /* ステータスカード */
    .status-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
        animation: fadeIn 0.4s ease;
    }
    .status-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .status-main {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .status-indicator {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
    }
    .status-indicator.ok { background: var(--green-light); }
    .status-indicator.warning { background: var(--yellow-light); }
    .status-indicator.error { background: var(--red-light); }
    .status-indicator.offline { background: var(--gray-200); }
    .status-indicator.paused { background: var(--gray-100); }
    .status-dot-inner {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        position: relative;
        z-index: 2;
    }
    .status-indicator.ok .status-dot-inner { background: var(--green); }
    .status-indicator.warning .status-dot-inner { background: var(--yellow); }
    .status-indicator.error .status-dot-inner { background: var(--red); }
    .status-indicator.offline .status-dot-inner { background: var(--gray-400); }
    .status-indicator.paused .status-dot-inner { background: var(--gray-300); }
    .status-indicator.ok::before,
    .status-indicator.ok::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        border: 2px solid var(--green);
        opacity: 0;
        animation: radar-pulse 3.5s ease-out infinite;
    }
    .status-indicator.ok::after { animation-delay: 1.75s; }
    @keyframes radar-pulse {
        0% { width: 20px; height: 20px; opacity: 0.6; }
        100% { width: 80px; height: 80px; opacity: 0; }
    }
    .status-indicator.paused::before,
    .status-indicator.paused::after { display: none; }
    .status-card.paused { opacity: 0.7; }
    .status-card.paused .status-text h2 { color: var(--gray-500); }
    .status-card.paused .status-text p { color: var(--gray-400); }
    .status-text h2 { font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--gray-800); }
    .status-text p { font-size: 14px; color: var(--gray-500); font-weight: 500; }
    .status-id {
        font-size: 13px;
        font-weight: 600;
        font-family: monospace;
        color: var(--gray-600);
        background: var(--beige);
        padding: 6px 12px;
        border-radius: 20px;
    }
    .status-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        padding-top: 20px;
        border-top: 1px solid var(--gray-100);
    }
    .detail-item {
        text-align: center;
        padding: 0 8px;
        border-right: 1px solid var(--gray-200);
    }
    .detail-item:last-child { border-right: none; }
    .detail-label {
        font-size: 11px;
        font-weight: 500;
        color: var(--gray-500);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .detail-value.good { color: var(--green-dark); }
    .detail-value.warn { color: var(--yellow); }
    .detail-value.bad { color: var(--red); }

    /* セクション */
    .section {
        margin-bottom: 28px;
        animation: fadeIn 0.4s ease both;
    }
    .section:nth-child(2) { animation-delay: 0.05s; }
    .section:nth-child(3) { animation-delay: 0.1s; }
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--gray-300);
    }
    .section-title-new {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-action {
        font-size: 13px;
        font-weight: 500;
        color: var(--gray-600);
        text-decoration: none;
        padding: 6px 12px;
        border-radius: var(--radius);
        background: var(--beige);
        transition: all 0.2s;
    }
    .section-action:hover { background: var(--gray-200); color: var(--gray-800); }
    .card-body { padding: 16px 20px; }

    /* 検知ログ */
    .log-list { list-style: none; }
    .log-item {
        display: flex;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid var(--gray-100);
        font-size: 14px;
    }
    .log-item:last-child { border-bottom: none; }
    .log-time { width: 100px; color: var(--gray-600); font-size: 13px; font-weight: 500; }
    .log-distance { width: 56px; font-weight: 600; color: var(--gray-800); }
    .log-type { flex: 1; font-size: 13px; font-weight: 500; }
    .log-type.human { color: var(--green-dark); }
    .log-type.pet { color: var(--gray-400); }
    .log-battery { color: var(--gray-400); font-size: 12px; }
    .log-empty { padding: 32px 20px; text-align: center; color: var(--gray-400); font-size: 13px; }

    /* フッター */
    .footer {
        text-align: center;
        padding: 32px 20px;
        margin-top: 20px;
        border-top: 1px solid var(--gray-200);
        color: var(--gray-400);
        font-size: 12px;
    }
    .footer-links { margin-top: 8px; }
    .footer-links a { color: var(--gray-400); text-decoration: none; }
    .footer-links a:hover { color: var(--gray-600); text-decoration: underline; }
    .footer-sep { margin: 0 4px; }

    /* 下部固定バー */
    .watch-status-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--white);
        border-top: 1px solid var(--gray-200);
        padding: 12px 20px;
        z-index: 100;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    .watch-status-bar-inner {
        max-width: 640px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .watch-status-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .watch-status-label { font-size: 16px; color: var(--gray-600); font-weight: 500; }
    .watch-status-text { font-size: 18px; font-weight: 700; color: var(--gray-800); }
    .watch-status-text.paused { color: var(--gray-400); }
    .watch-toggle {
        position: relative;
        width: 52px;
        height: 30px;
        background: var(--gray-300);
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        flex-shrink: 0;
    }
    .watch-toggle.active { background: var(--green); }
    .watch-toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background: var(--white);
        border-radius: 50%;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .watch-toggle.active::after { left: 25px; }
    .watch-status-btn {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        background: var(--beige);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 12px;
        font-family: 'Noto Sans JP', sans-serif;
    }
    .watch-status-btn:hover { background: var(--gray-200); }

    /* モーダル */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1000;
        padding: 20px;
    }
    .modal-overlay.show { display: block; }
    .modal {
        background: var(--white);
        max-width: 480px;
        margin: 60px auto;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        max-height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
    }
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .modal-header h3 { font-size: 16px; font-weight: 600; }
    .modal-close {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--beige);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        color: var(--gray-600);
    }
    .modal-close:hover { background: var(--gray-200); }
    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }
    .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--gray-100);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    /* 解除確認モーダル内 */
    .dismiss-warning {
        background: var(--red-light);
        border-radius: var(--radius);
        padding: 12px 14px;
        margin-bottom: 16px;
        font-size: 13px;
        color: var(--red);
        font-weight: 600;
    }
    .dismiss-details {
        font-size: 13px;
        color: var(--gray-600);
        line-height: 1.8;
        margin-bottom: 16px;
    }

    /* タイマータブ */
    .timer-tabs {
        display: flex;
        gap: 4px;
        background: var(--beige);
        padding: 4px;
        border-radius: var(--radius);
        margin-bottom: 20px;
    }
    .timer-tab {
        flex: 1;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        color: var(--gray-500);
        background: transparent;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Noto Sans JP', sans-serif;
    }
    .timer-tab.active {
        background: var(--white);
        color: var(--gray-800);
        box-shadow: var(--shadow-sm);
    }
    .timer-tab:not(.active):hover { color: var(--gray-700); }
    .timer-content { display: none; }
    .timer-content.active { display: block; }

    /* スケジュール一覧 */
    .schedule-list { margin-bottom: 20px; }
    .schedule-list-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .schedule-items {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .schedule-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        background: var(--white);
        border-bottom: 1px solid var(--gray-100);
    }
    .schedule-item:last-child { border-bottom: none; }
    .schedule-item:nth-child(even) { background: var(--cream); }
    .schedule-item-info { flex: 1; }
    .schedule-item-main { font-size: 14px; font-weight: 600; color: var(--gray-800); margin-bottom: 2px; }
    .schedule-item-sub { font-size: 12px; color: var(--gray-500); }
    .schedule-item-delete {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: var(--gray-400);
        font-size: 16px;
        transition: all 0.2s;
    }
    .schedule-item-delete:hover { background: var(--red-light); color: var(--red); }
    .schedule-empty {
        padding: 20px;
        text-align: center;
        color: var(--gray-400);
        font-size: 13px;
        background: var(--cream);
    }

    /* 追加フォーム */
    .add-form-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-600);
        background: var(--beige);
        border: 1px dashed var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 16px;
        font-family: 'Noto Sans JP', sans-serif;
    }
    .add-form-toggle:hover { background: var(--gray-100); color: var(--gray-800); }
    .add-form {
        display: none;
        padding: 16px;
        background: var(--beige);
        border-radius: var(--radius);
    }
    .add-form.show { display: block; }
    .add-form-title { font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 12px; }
    .add-form-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 16px;
    }
    .btn-sm { padding: 8px 16px; font-size: 13px; }

    /* フォーム */
    .form-group { margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; }
    .form-input {
        width: 100%;
        padding: 12px 14px;
        font-size: 15px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
    }
    .form-input:focus { outline: none; border-color: var(--gray-500); background: var(--white); }
    .form-hint { font-size: 12px; color: var(--gray-500); margin-top: 6px; }

    /* 曜日選択 */
    .weekday-selector { display: flex; gap: 6px; margin-bottom: 16px; }
    .weekday-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-500);
        background: var(--cream);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Noto Sans JP', sans-serif;
    }
    .weekday-btn:hover { border-color: var(--gray-400); color: var(--gray-700); }
    .weekday-btn.selected { background: var(--gray-800); color: var(--white); border-color: var(--gray-800); }
    .weekday-btn.sun { color: var(--red); }
    .weekday-btn.sun.selected { background: var(--red); border-color: var(--red); color: var(--white); }
    .weekday-btn.sat { color: var(--blue); }
    .weekday-btn.sat.selected { background: var(--blue); border-color: var(--blue); color: var(--white); }

    /* 時間入力 */
    .time-row { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .time-row input[type="time"] {
        flex: 1;
        padding: 10px 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
    }
    .time-row input[type="time"]:focus { outline: none; border-color: var(--gray-500); background: var(--white); }
    .time-row span { color: var(--gray-500); font-size: 14px; }
    .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
    .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--gray-800); cursor: pointer; }
    .checkbox-row label { font-size: 13px; color: var(--gray-700); cursor: pointer; }

    @media (max-width: 480px) {
        .status-card { padding: 20px; }
        .detail-item { padding: 8px 6px; }
        .alert-banner { flex-direction: column; align-items: flex-start; gap: 8px; }
        .alert-dismiss-btn { align-self: flex-end; }
    }
</style>
@endsection

@section('content')
{{-- 通知先未登録バナー --}}
@if($showNotifyBanner)
<div class="notify-banner">
    <div class="notify-banner-inner">
        <div class="notify-banner-text">
            <span>⚠️</span>
            <p>通知先を登録してください</p>
        </div>
        <a href="/settings" class="notify-banner-btn">登録する</a>
    </div>
</div>
@endif

{{-- アラートバナー --}}
@if($device->status === 'alert' && !$device->away_mode)
<div class="alert-banner warning" id="alertBanner">
    <span>🔴 <strong>{{ $device->alert_threshold_hours }}時間以上</strong>検知がありません（要確認）</span>
    <button class="alert-dismiss-btn" onclick="showDismissModal()">✕ 解除</button>
</div>
@endif
@if($device->status === 'offline' && !$device->away_mode)
<div class="alert-banner offline" id="offlineBanner">
    <span>⚫ デバイスとの<strong>通信が途絶</strong>えています（電波障害または電池切れの可能性）</span>
    <button class="alert-dismiss-btn" onclick="showDismissModal()">✕ 解除</button>
</div>
@endif

{{-- ステータスカード --}}
<div class="status-card{{ $device->away_mode ? ' paused' : '' }}" id="statusCard">
    <div class="status-header">
        <div class="status-main">
            @php
                $indicatorClass = match($device->status) {
                    'normal' => 'ok',
                    'warning' => 'warning',
                    'alert' => 'error',
                    'offline' => 'offline',
                    default => 'offline',
                };
                if ($device->away_mode) $indicatorClass = 'paused';
            @endphp
            <div class="status-indicator {{ $indicatorClass }}" id="statusIndicator">
                <div class="status-dot-inner"></div>
            </div>
            <div class="status-text">
                <h2 id="statusTitle">
                    @if($device->away_mode)
                        見守り停止中
                    @else
                        @switch($device->status)
                            @case('normal') 正常稼働中 @break
                            @case('warning') 注意 @break
                            @case('alert') 未検知アラート @break
                            @case('offline') 通信途絶 @break
                            @default 未稼働
                        @endswitch
                    @endif
                </h2>
                <p id="statusSubtitle">
                    @if($device->away_mode)
                        タイマーまたは手動でONに戻せます
                    @elseif($device->last_human_detected_at)
                        最終検知: {{ $device->last_human_detected_at->diffForHumans() }}
                    @else
                        検知データなし
                    @endif
                </p>
            </div>
        </div>
        <span class="status-id">{{ $device->device_id }}</span>
    </div>

    <div class="status-details">
        <div class="detail-item">
            <p class="detail-label">電池残量</p>
            <p class="detail-value {{ $device->battery_pct !== null ? ($device->battery_pct > 50 ? 'good' : ($device->battery_pct > 20 ? 'warn' : 'bad')) : '' }}">
                @if($device->battery_pct !== null)
                    <svg width="22" height="11" viewBox="0 0 24 12">
                        <rect x="0" y="0" width="20" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="2" y="2" width="{{ max(1, $device->battery_pct / 100 * 16) }}" height="8" rx="1" fill="currentColor"/>
                        <rect x="20" y="3" width="3" height="6" rx="1" fill="currentColor"/>
                    </svg>
                    {{ $device->battery_pct }}%
                @else
                    ---
                @endif
            </p>
        </div>
        <div class="detail-item">
            <p class="detail-label">電波状況</p>
            <p class="detail-value {{ $device->rssi !== null ? ($device->rssi > -80 ? 'good' : ($device->rssi > -100 ? 'warn' : 'bad')) : '' }}">
                @if($device->rssi !== null)
                    <svg width="18" height="14" viewBox="0 0 20 16">
                        <rect x="0" y="12" width="4" height="4" rx="1" fill="currentColor"/>
                        <rect x="5" y="8" width="4" height="8" rx="1" fill="{{ $device->rssi > -100 ? 'currentColor' : 'var(--gray-200)' }}"/>
                        <rect x="10" y="4" width="4" height="12" rx="1" fill="{{ $device->rssi > -80 ? 'currentColor' : 'var(--gray-200)' }}"/>
                        <rect x="15" y="0" width="4" height="16" rx="1" fill="{{ $device->rssi > -60 ? 'currentColor' : 'var(--gray-200)' }}"/>
                    </svg>
                    {{ $device->rssi > -60 ? '良好' : ($device->rssi > -80 ? '普通' : '弱い') }}
                @else
                    ---
                @endif
            </p>
        </div>
        <div class="detail-item">
            <p class="detail-label">最終受信</p>
            <p class="detail-value">
                {{ $device->last_received_at ? $device->last_received_at->format('m/d H:i') : '---' }}
            </p>
        </div>
    </div>
</div>

{{-- 検知ログ --}}
<section class="section">
    <div class="section-header">
        <h3 class="section-title-new">📋 検知ログ</h3>
        <a href="/logs" class="section-action">全て見る</a>
    </div>
    <div class="card" style="padding:0;">
        <div class="card-body">
            @if($logs->isEmpty())
                <div class="log-empty">まだ検知データがありません</div>
            @else
                <ul class="log-list">
                    @foreach($logs as $log)
                    <li class="log-item">
                        <span class="log-time">{{ $log->period_start->format('m/d H:i') }}</span>
                        @if($log->last_distance_cm !== null)
                            <span class="log-distance">{{ $log->last_distance_cm }}cm</span>
                        @else
                            <span class="log-distance">---</span>
                        @endif
                        @if($log->human_count > 0)
                            <span class="log-type human">人間 ×{{ $log->human_count }}</span>
                        @elseif($log->pet_count > 0)
                            <span class="log-type pet">ペット ×{{ $log->pet_count }}</span>
                        @else
                            <span class="log-type">検知なし</span>
                        @endif
                        <span class="log-battery">{{ $log->battery_voltage ? $log->battery_voltage . 'V' : '' }}</span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>

{{-- フッター --}}
<footer class="footer">
    <div class="footer-links">
        <a href="/settings">設定</a><span class="footer-sep">|</span>
        <a href="/logs">検知ログ</a><span class="footer-sep">|</span>
        <a href="/pin-reset">PIN変更</a>
    </div>
    <div class="footer-links" style="margin-top: 8px;">
        <a href="/guide">使い方</a><span class="footer-sep">|</span>
        <a href="/terms">利用規約</a><span class="footer-sep">|</span>
        <a href="/contact">お問い合わせ</a>
    </div>
</footer>

{{-- 下部固定 見守り設定バー --}}
<div class="watch-status-bar">
    <div class="watch-status-bar-inner">
        <div class="watch-status-left">
            <span class="watch-status-label">見守り</span>
            <span class="watch-status-text{{ $device->away_mode ? ' paused' : '' }}" id="watchText">{{ $device->away_mode ? 'OFF' : 'ON' }}</span>
        </div>
        <div style="display: flex; align-items: center;">
            <button class="watch-toggle{{ $device->away_mode ? '' : ' active' }}" id="watchToggle" onclick="toggleWatch()"></button>
            <button class="watch-status-btn" onclick="showScheduleModal()">タイマー</button>
        </div>
    </div>
</div>

{{-- ===== モーダル: 警告解除確認 ===== --}}
<div id="dismissModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>⚠️ 警告を解除しますか？</h3>
            <button class="modal-close" onclick="hideDismissModal()">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:var(--gray-700);margin-bottom:16px;">ステータスを<strong>「未稼働」</strong>に変更します。</p>
            <p style="font-size:13px;color:var(--gray-500);">デバイスからの次回通信があれば自動的に再開されます。</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideDismissModal()">キャンセル</button>
            <button class="btn btn-danger" id="dismissBtn" onclick="executeDismiss()">解除する</button>
        </div>
    </div>
</div>

{{-- タイマー設定モーダル --}}
<div id="scheduleModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>タイマー設定</h3>
            <button class="modal-close" onclick="hideScheduleModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="timer-tabs">
                <button class="timer-tab active" onclick="switchTimerTab('oneshot')">単発の予定</button>
                <button class="timer-tab" onclick="switchTimerTab('recurring')">定期スケジュール</button>
            </div>

            {{-- 単発の予定タブ --}}
            <div id="oneshotTab" class="timer-content active">
                <div class="schedule-list">
                    <p class="schedule-list-title">設定済みの予定</p>
                    <div class="schedule-items" id="oneshotList">
                        <div class="schedule-empty">設定済みの予定はありません</div>
                    </div>
                </div>
                <button class="add-form-toggle" onclick="toggleOneshotForm()">＋ 新しい予定を追加</button>
                <div id="oneshotForm" class="add-form">
                    <p class="add-form-title">新しい停止予定</p>
                    <div class="form-group">
                        <label class="form-label">停止開始</label>
                        <input type="datetime-local" class="form-input" id="scheduleStart">
                    </div>
                    <div class="form-group">
                        <label class="form-label">自動再開</label>
                        <input type="datetime-local" class="form-input" id="scheduleEnd">
                        <p class="form-hint">空欄の場合は手動でONに戻す必要があります</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">メモ（任意）</label>
                        <input type="text" class="form-input" id="scheduleMemo" placeholder="旅行、入院など">
                    </div>
                    <div class="add-form-actions">
                        <button class="btn btn-secondary btn-sm" onclick="toggleOneshotForm()">キャンセル</button>
                        <button class="btn btn-primary btn-sm" onclick="saveOneshot()">追加する</button>
                    </div>
                </div>
            </div>

            {{-- 定期スケジュールタブ --}}
            <div id="recurringTab" class="timer-content">
                <div class="schedule-list">
                    <p class="schedule-list-title">設定済みの定期スケジュール</p>
                    <div class="schedule-items" id="recurringList">
                        <div class="schedule-empty">設定済みの定期スケジュールはありません</div>
                    </div>
                </div>
                <button class="add-form-toggle" onclick="toggleRecurringForm()">＋ 定期スケジュールを追加</button>
                <div id="recurringForm" class="add-form">
                    <p class="add-form-title">新しい定期スケジュール</p>
                    <div class="form-group">
                        <label class="form-label">曜日を選択</label>
                        <div class="weekday-selector">
                            <button type="button" class="weekday-btn sun" data-day="0">日</button>
                            <button type="button" class="weekday-btn" data-day="1">月</button>
                            <button type="button" class="weekday-btn" data-day="2">火</button>
                            <button type="button" class="weekday-btn" data-day="3">水</button>
                            <button type="button" class="weekday-btn" data-day="4">木</button>
                            <button type="button" class="weekday-btn" data-day="5">金</button>
                            <button type="button" class="weekday-btn sat" data-day="6">土</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">時間帯</label>
                        <div class="time-row">
                            <input type="time" id="recurringStart" value="10:00">
                            <span>〜</span>
                            <input type="time" id="recurringEnd" value="16:00">
                        </div>
                        <div class="checkbox-row">
                            <input type="checkbox" id="recurringNextDay">
                            <label for="recurringNextDay">翌日までまたぐ</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">メモ（任意）</label>
                        <input type="text" class="form-input" id="recurringMemo" placeholder="デイサービス、通院など">
                    </div>
                    <div class="add-form-actions">
                        <button class="btn btn-secondary btn-sm" onclick="toggleRecurringForm()">キャンセル</button>
                        <button class="btn btn-primary btn-sm" onclick="saveRecurring()">追加する</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideScheduleModal()">閉じる</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    // ==== 警告解除 ====
    function showDismissModal() {
        document.getElementById('dismissModal').classList.add('show');
    }
    function hideDismissModal() {
        document.getElementById('dismissModal').classList.remove('show');
    }

    async function executeDismiss() {
        var btn = document.getElementById('dismissBtn');
        btn.disabled = true;
        btn.textContent = '処理中...';

        try {
            var res = await fetch('/mypage/dismiss-alert', {
                method: 'POST',
                headers: headers,
            });
            var data = await res.json();

            if (res.ok && data.success) {
                showToast(data.message);
                hideDismissModal();
                // ページをリロードして状態を反映
                setTimeout(function() { location.reload(); }, 500);
            } else {
                showToast(data.message || 'エラーが発生しました');
                btn.disabled = false;
                btn.textContent = '解除する';
            }
        } catch (e) {
            console.error('警告解除エラー:', e);
            showToast('通信エラーが発生しました');
            btn.disabled = false;
            btn.textContent = '解除する';
        }
    }

    // モーダル外クリックで閉じる
    document.getElementById('dismissModal').addEventListener('click', function(e) {
        if (e.target === this) hideDismissModal();
    });

    // ==== 見守りON/OFF ====
    let watchEnabled = {{ $device->away_mode ? 'false' : 'true' }};

    function toggleWatch() {
        const toggle = document.getElementById('watchToggle');
        const text = document.getElementById('watchText');
        const statusCard = document.getElementById('statusCard');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusTitle = document.getElementById('statusTitle');
        const statusSubtitle = document.getElementById('statusSubtitle');

        watchEnabled = !watchEnabled;

        fetch('/mypage/toggle-watch', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ away_mode: !watchEnabled })
        })
        .then(res => res.json())
        .then(data => {
            if (watchEnabled) {
                toggle.classList.add('active');
                text.textContent = 'ON';
                text.classList.remove('paused');
                statusCard.classList.remove('paused');
                statusIndicator.className = 'status-indicator ' + data.indicator_class;
                statusTitle.textContent = data.status_label;
                statusSubtitle.textContent = data.status_subtitle;
                showToast('見守りをONにしました');
            } else {
                toggle.classList.remove('active');
                text.textContent = 'OFF';
                text.classList.add('paused');
                statusCard.classList.add('paused');
                statusIndicator.className = 'status-indicator paused';
                statusTitle.textContent = '見守り停止中';
                statusSubtitle.textContent = 'タイマーまたは手動でONに戻せます';
                showToast('見守りをOFFにしました');
            }
        })
        .catch(() => {
            if (watchEnabled) {
                toggle.classList.add('active');
                text.textContent = 'ON';
                text.classList.remove('paused');
                statusCard.classList.remove('paused');
                showToast('見守りをONにしました');
            } else {
                toggle.classList.remove('active');
                text.textContent = 'OFF';
                text.classList.add('paused');
                statusCard.classList.add('paused');
                statusIndicator.className = 'status-indicator paused';
                statusTitle.textContent = '見守り停止中';
                statusSubtitle.textContent = 'タイマーまたは手動でONに戻せます';
                showToast('見守りをOFFにしました');
            }
        });
    }

    // ==== タイマーモーダル ====
    function showScheduleModal() {
        document.getElementById('scheduleModal').classList.add('show');
        loadSchedules();
    }
    function hideScheduleModal() {
        document.getElementById('scheduleModal').classList.remove('show');
        document.getElementById('oneshotForm').classList.remove('show');
        document.getElementById('recurringForm').classList.remove('show');
    }
    function switchTimerTab(tab) {
        document.querySelectorAll('.timer-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.timer-content').forEach(c => c.classList.remove('active'));
        if (tab === 'oneshot') {
            document.querySelector('.timer-tab:nth-child(1)').classList.add('active');
            document.getElementById('oneshotTab').classList.add('active');
        } else {
            document.querySelector('.timer-tab:nth-child(2)').classList.add('active');
            document.getElementById('recurringTab').classList.add('active');
        }
    }

    // ==== サーバーからスケジュール読み込み ====
    let oneshotSchedules = [];
    let recurringSchedules = [];
    const dayNames = ['日', '月', '火', '水', '木', '金', '土'];

    async function loadSchedules() {
        try {
            const res = await fetch('/schedules', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            oneshotSchedules = data.filter(s => s.type === 'oneshot');
            recurringSchedules = data.filter(s => s.type === 'recurring');
            renderOneshotList();
            renderRecurringList();
        } catch (e) {
            console.error('スケジュール読み込みエラー:', e);
        }
    }

    // ==== 単発予定 ====
    function toggleOneshotForm() {
        const form = document.getElementById('oneshotForm');
        form.classList.toggle('show');
        if (form.classList.contains('show')) {
            const now = new Date();
            const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            document.getElementById('scheduleStart').value = formatDatetimeLocal(now);
            document.getElementById('scheduleEnd').value = formatDatetimeLocal(tomorrow);
            setTimeout(() => form.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
        }
    }

    function formatDatetimeLocal(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        const h = String(date.getHours()).padStart(2, '0');
        const mi = String(date.getMinutes()).padStart(2, '0');
        return `${y}-${m}-${d}T${h}:${mi}`;
    }

    function formatDateTime(dtStr) {
        if (!dtStr) return '未定（手動復帰）';
        const d = new Date(dtStr);
        return `${d.getMonth()+1}/${d.getDate()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    }

    function renderOneshotList() {
        const container = document.getElementById('oneshotList');
        if (oneshotSchedules.length === 0) {
            container.innerHTML = '<div class="schedule-empty">設定済みの予定はありません</div>';
            return;
        }
        container.innerHTML = oneshotSchedules.map(s => `
            <div class="schedule-item">
                <div class="schedule-item-info">
                    <p class="schedule-item-main">${formatDateTime(s.start_at)} 〜 ${formatDateTime(s.end_at)}</p>
                    <p class="schedule-item-sub">${s.memo || '（メモなし）'}</p>
                </div>
                <button class="schedule-item-delete" onclick="deleteSchedule(${s.id}, 'oneshot')" title="削除">×</button>
            </div>`).join('');
    }

    async function saveOneshot() {
        const start = document.getElementById('scheduleStart').value;
        const end = document.getElementById('scheduleEnd').value;
        const memo = document.getElementById('scheduleMemo').value;
        if (!start) { alert('開始日時を入力してください'); return; }
        try {
            const res = await fetch('/schedules', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    type: 'oneshot',
                    start_at: start,
                    end_at: end || null,
                    memo: memo || null,
                })
            });
            if (!res.ok) { alert('保存に失敗しました'); return; }
            const saved = await res.json();
            oneshotSchedules.push(saved);
            renderOneshotList();
            toggleOneshotForm();
            document.getElementById('scheduleMemo').value = '';
            showToast('予定を追加しました');
        } catch (e) { alert('通信エラーが発生しました'); }
    }

    // ==== 定期スケジュール ====
    function toggleRecurringForm() {
        const form = document.getElementById('recurringForm');
        form.classList.toggle('show');
        if (form.classList.contains('show')) {
            setTimeout(() => form.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
        } else {
            document.querySelectorAll('.weekday-btn').forEach(btn => btn.classList.remove('selected'));
            document.getElementById('recurringStart').value = '10:00';
            document.getElementById('recurringEnd').value = '16:00';
            document.getElementById('recurringNextDay').checked = false;
            document.getElementById('recurringMemo').value = '';
        }
    }

    document.querySelectorAll('.weekday-btn').forEach(btn => {
        btn.addEventListener('click', function() { this.classList.toggle('selected'); });
    });

    function renderRecurringList() {
        const container = document.getElementById('recurringList');
        if (recurringSchedules.length === 0) {
            container.innerHTML = '<div class="schedule-empty">設定済みの定期スケジュールはありません</div>';
            return;
        }
        container.innerHTML = recurringSchedules.map(s => {
            const days = s.days_of_week || [];
            const daysStr = days.map(d => dayNames[d]).join('・');
            const timeStr = s.next_day ? `${s.start_time}〜翌${s.end_time}` : `${s.start_time}〜${s.end_time}`;
            return `
            <div class="schedule-item">
                <div class="schedule-item-info">
                    <p class="schedule-item-main">毎週 ${daysStr} ${timeStr}</p>
                    <p class="schedule-item-sub">${s.memo || '（メモなし）'}</p>
                </div>
                <button class="schedule-item-delete" onclick="deleteSchedule(${s.id}, 'recurring')" title="削除">×</button>
            </div>`;
        }).join('');
    }

    async function saveRecurring() {
        const selectedDays = [];
        document.querySelectorAll('.weekday-btn.selected').forEach(btn => {
            selectedDays.push(parseInt(btn.dataset.day));
        });
        if (selectedDays.length === 0) { alert('曜日を選択してください'); return; }
        const start = document.getElementById('recurringStart').value;
        const end = document.getElementById('recurringEnd').value;
        const nextDay = document.getElementById('recurringNextDay').checked;
        const memo = document.getElementById('recurringMemo').value;
        if (!start || !end) { alert('時間帯を入力してください'); return; }
        try {
            const res = await fetch('/schedules', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    type: 'recurring',
                    days_of_week: selectedDays.sort(),
                    start_time: start,
                    end_time: end,
                    next_day: nextDay,
                    memo: memo || null,
                })
            });
            if (!res.ok) { alert('保存に失敗しました'); return; }
            const saved = await res.json();
            recurringSchedules.push(saved);
            renderRecurringList();
            toggleRecurringForm();
            showToast('定期スケジュールを追加しました');
        } catch (e) { alert('通信エラーが発生しました'); }
    }

    // ==== 共通削除 ====
    async function deleteSchedule(id, type) {
        if (!confirm('このスケジュールを削除しますか？')) return;
        try {
            const res = await fetch(`/schedules/${id}`, {
                method: 'DELETE',
                headers: headers,
            });
            if (!res.ok) { alert('削除に失敗しました'); return; }
            if (type === 'oneshot') {
                oneshotSchedules = oneshotSchedules.filter(s => s.id !== id);
                renderOneshotList();
            } else {
                recurringSchedules = recurringSchedules.filter(s => s.id !== id);
                renderRecurringList();
            }
            showToast('スケジュールを削除しました');
        } catch (e) { alert('通信エラーが発生しました'); }
    }

    // モーダル外クリックで閉じる
    document.getElementById('scheduleModal').addEventListener('click', function(e) {
        if (e.target === this) hideScheduleModal();
    });
</script>
@endsection
