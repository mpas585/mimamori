@extends('layouts.app')

@section('title', '検知ログ - みまもりデバイス')

@section('styles')
<style>
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-label {
        font-size: 11px;
        color: #999;
    }
    .filter-input {
        padding: 8px 10px;
        border: 1px solid #d8d0c4;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        color: #4a4a4a;
    }
    .filter-input:focus {
        outline: none;
        border-color: #8b7e6a;
    }
    .filter-btn {
        padding: 8px 16px;
        background: #8b7e6a;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
    }
    .filter-btn:hover {
        background: #7a6e5b;
    }
    .filter-reset {
        padding: 8px 12px;
        background: none;
        color: #8b7e6a;
        border: 1px solid #d8d0c4;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        text-decoration: none;
    }
    .log-table {
        width: 100%;
        font-size: 13px;
        border-collapse: collapse;
    }
    .log-table th {
        padding: 10px 8px;
        text-align: left;
        color: #999;
        font-weight: 400;
        border-bottom: 2px solid #e8e2d8;
        white-space: nowrap;
    }
    .log-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #f0ece4;
    }
    .log-table tr:hover {
        background: #faf8f4;
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
    .pagination-wrap {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .pagination-wrap a,
    .pagination-wrap span {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        color: #8b7e6a;
        border: 1px solid #d8d0c4;
    }
    .pagination-wrap span.current {
        background: #8b7e6a;
        color: #fff;
        border-color: #8b7e6a;
    }
    .pagination-wrap span.disabled {
        color: #ccc;
        border-color: #e8e2d8;
    }
    .log-summary {
        font-size: 12px;
        color: #999;
        text-align: right;
        margin-top: 8px;
    }
    .error-badge {
        display: inline-block;
        background: #fbe9e7;
        color: #c62828;
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')

<div class="card">
    <div style="font-size:14px;font-weight:500;color:#8b7e6a;margin-bottom:16px;">検知ログ</div>

    {{-- フィルタ --}}
    <form method="GET" action="/logs">
        <div class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">開始日</span>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <span class="filter-label">終了日</span>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="filter-btn">絞り込み</button>
            @if(request('date_from') || request('date_to'))
                <a href="/logs" class="filter-reset">リセット</a>
            @endif
        </div>
    </form>

    {{-- ログ一覧 --}}
    @if($logs->isEmpty())
        <p style="color:#aaa;font-size:13px;text-align:center;padding:40px 0;">検知データがありません</p>
    @else
        <table class="log-table">
            <thead>
                <tr>
                    <th>期間</th>
                    <th class="text-center">人</th>
                    <th class="text-center">ペット</th>
                    <th class="text-center">距離</th>
                    <th class="text-center">電池</th>
                    <th class="text-center">電波</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->period_start->format('m/d H:i') }} 〜 {{ $log->period_end->format('H:i') }}</td>
                    <td class="text-center">{{ $log->human_count }}</td>
                    <td class="text-center">{{ $log->pet_count }}</td>
                    <td class="text-center">{{ $log->last_distance_cm !== null ? $log->last_distance_cm . 'cm' : '-' }}</td>
                    <td class="text-center">{{ $log->battery_pct !== null ? $log->battery_pct . '%' : '-' }}</td>
                    <td class="text-center">{{ $log->rssi !== null ? $log->rssi . 'dBm' : '-' }}</td>
                    <td class="text-center">
                        @if($log->error_code)
                            <span class="error-badge">{{ $log->error_code }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ページネーション --}}
        @if($logs->hasPages())
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
        @endif

        <div class="log-summary">{{ $logs->total() }}件中 {{ $logs->firstItem() }}〜{{ $logs->lastItem() }}件</div>
    @endif
</div>

@endsection
