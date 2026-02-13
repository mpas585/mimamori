@extends('layouts.app')

@section('title', '検知ログ - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner">
        <a href="/mypage" class="log-back-btn">←</a>
        <h1 class="log-header-title">検知ログ</h1>
        <span class="log-header-id">{{ $device->device_id }}</span>
    </div>
</header>
@endsection

@section('styles')
<style>
    .log-back-btn {
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
    }
    .log-back-btn:hover {
        background: var(--gray-200);
    }
    .log-header-title {
        font-size: 16px;
        font-weight: 600;
        flex: 1;
        text-align: center;
    }
    .log-header-id {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-500);
        font-family: monospace;
        letter-spacing: 0.05em;
    }

    /* フィルタ */
    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .filter-input {
        flex: 1;
        min-width: 140px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
    }
    .filter-input:focus {
        outline: none;
        border-color: var(--gray-500);
    }
    .filter-select {
        padding: 12px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
        min-width: 120px;
    }

    /* サマリー */
    .summary-bar {
        display: flex;
        gap: 16px;
        padding: 16px 20px;
        background: var(--white);
        border-radius: var(--radius-lg);
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .summary-item {
        text-align: center;
        flex: 1;
    }
    .summary-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--gray-800);
    }
    .summary-value.human { color: var(--green-dark); }
    .summary-value.pet { color: var(--yellow); }
    .summary-label {
        font-size: 11px;
        color: var(--gray-500);
        margin-top: 2px;
    }

    /* ログセクション */
    .log-section {
        background: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .log-date-header {
        padding: 12px 20px;
        background: var(--beige);
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
        position: sticky;
        top: 61px;
        z-index: 10;
    }
    .log-item {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid var(--gray-100);
        transition: background 0.2s;
    }
    .log-item:last-child { border-bottom: none; }
    .log-item:hover { background: var(--gray-100); }
    .log-time {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        min-width: 60px;
        font-family: monospace;
    }
    .log-type {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 80px;
    }
    .log-type-icon { font-size: 16px; }
    .log-type-text {
        font-size: 13px;
        font-weight: 500;
    }
    .log-type-text.human { color: var(--green-dark); }
    .log-type-text.pet { color: var(--yellow); }
    .log-details {
        flex: 1;
        display: flex;
        gap: 16px;
        justify-content: flex-end;
    }
    .log-detail {
        font-size: 12px;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .log-detail svg {
        width: 14px;
        height: 14px;
    }
    .log-error {
        display: inline-block;
        background: var(--red-light);
        color: var(--red);
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 4px;
        margin-left: 8px;
    }

    /* ページネーション */
    .pagination-area {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .pagination-wrap {
        display: flex;
        justify-content: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .pagination-wrap a,
    .pagination-wrap span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: var(--radius);
        font-size: 13px;
        text-decoration: none;
        color: var(--gray-600);
        background: var(--white);
        border: 1px solid var(--gray-200);
        transition: all 0.2s;
    }
    .pagination-wrap a:hover { background: var(--beige); }
    .pagination-wrap span.current {
        background: var(--gray-800);
        color: var(--white);
        border-color: var(--gray-800);
    }
    .pagination-wrap span.disabled {
        color: var(--gray-300);
        border-color: var(--gray-200);
    }
    .log-summary {
        font-size: 12px;
        color: var(--gray-500);
    }

    /* 空状態 */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    .empty-state-text { font-size: 14px; }

    @media (max-width: 480px) {
        .filter-bar { flex-direction: column; }
        .filter-input, .filter-select { width: 100%; }
        .log-details { gap: 12px; }
        .summary-bar { gap: 8px; padding: 12px 16px; }
        .summary-value { font-size: 20px; }
    }
</style>
@endsection

@section('content')

{{-- フィルタ --}}
<form method="GET" action="/logs" id="filterForm">
    <div class="filter-bar">
        <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}" onchange="this.form.submit()">
        <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}" onchange="this.form.submit()">
        <select name="type" class="filter-select" onchange="this.form.submit()">
            <option value="">すべて</option>
            <option value="human" {{ request('type') === 'human' ? 'selected' : '' }}>人間のみ</option>
            <option value="pet" {{ request('type') === 'pet' ? 'selected' : '' }}>ペットのみ</option>
        </select>
    </div>
</form>

{{-- サマリー --}}
<div class="summary-bar">
    <div class="summary-item">
        <p class="summary-value">{{ $summary['total'] }}</p>
        <p class="summary-label">総検知数</p>
    </div>
    <div class="summary-item">
        <p class="summary-value human">{{ $summary['human'] }}</p>
        <p class="summary-label">人間</p>
    </div>
    <div class="summary-item">
        <p class="summary-value pet">{{ $summary['pet'] }}</p>
        <p class="summary-label">ペット</p>
    </div>
</div>

{{-- ログ一覧 --}}
@if($logs->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">📋</div>
        <p class="empty-state-text">検知データがありません</p>
    </div>
@else
    <div class="log-section">
        @php $currentDate = null; @endphp

        @foreach($logs as $log)
            @php
                $logDate = $log->period_start->format('Y年n月j日') . '（' . ['日','月','火','水','木','金','土'][$log->period_start->dayOfWeek] . '）';
            @endphp

            @if($currentDate !== $logDate)
                @php $currentDate = $logDate; @endphp
                <div class="log-date-header">{{ $logDate }}</div>
            @endif

            @php
                $isHuman = $log->human_count > 0;
                $isPet = $log->pet_count > 0 && !$isHuman;
            @endphp

            <div class="log-item">
                <span class="log-time">{{ $log->period_start->format('H:i') }}</span>
                <div class="log-type">
                    @if($isHuman)
                        <span class="log-type-icon">👤</span>
                        <span class="log-type-text human">人間</span>
                    @elseif($isPet)
                        <span class="log-type-icon">🐕</span>
                        <span class="log-type-text pet">ペット</span>
                    @else
                        <span class="log-type-icon">📡</span>
                        <span class="log-type-text" style="color:var(--gray-500);">送信のみ</span>
                    @endif
                </div>
                <div class="log-details">
                    @if($log->last_distance_cm !== null)
                        <span class="log-detail">{{ $log->last_distance_cm }}cm</span>
                    @endif
                    @if($log->battery_voltage !== null)
                        <span class="log-detail">
                            <svg viewBox="0 0 24 12"><rect x="0" y="0" width="20" height="12" rx="2" fill="none" stroke="{{ $log->battery_pct > 20 ? '#16a34a' : '#ef4444' }}" stroke-width="1.5"/><rect x="2" y="2" width="{{ max(1, ($log->battery_pct ?? 50) / 100 * 16) }}" height="8" rx="1" fill="{{ $log->battery_pct > 20 ? '#22c55e' : '#ef4444' }}"/><rect x="20" y="3" width="3" height="6" rx="1" fill="{{ $log->battery_pct > 20 ? '#16a34a' : '#ef4444' }}"/></svg>
                            {{ $log->battery_voltage }}V
                        </span>
                    @endif
                    @if($log->error_code)
                        <span class="log-error">{{ $log->error_code }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ページネーション --}}
    @if($logs->hasPages())
        <div class="pagination-area">
            <div class="pagination-wrap">
                @if($logs->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}">‹</a>
                @endif

                @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
            <div class="log-summary">{{ $logs->total() }}件中 {{ $logs->firstItem() }}〜{{ $logs->lastItem() }}件</div>
        </div>
    @endif
@endif

@endsection
