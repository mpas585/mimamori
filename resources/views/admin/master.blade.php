@extends('layouts.admin')

@section('title', 'ダッシュボード - 管理画面')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #5a5245;
    }
    .stat-label {
        font-size: 11px;
        color: #999;
        margin-top: 4px;
    }
    .stat-card.alert .stat-value { color: #c62828; }
    .stat-card.offline .stat-value { color: #666; }

    /* 発番セクション */
    .issue-section {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .issue-form {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }
    .issue-input {
        width: 80px;
        padding: 8px 10px;
        border: 1px solid #d8d0c4;
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        text-align: center;
    }
    .issue-input:focus {
        outline: none;
        border-color: #5a5245;
    }
    .issue-label {
        font-size: 12px;
        color: #888;
        margin-bottom: 4px;
    }

    /* 発番結果 */
    .issued-result {
        background: #e8f5e9;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .issued-title {
        font-size: 14px;
        font-weight: 500;
        color: #2e7d32;
        margin-bottom: 12px;
    }
    .issued-item {
        display: flex;
        gap: 24px;
        padding: 8px 0;
        border-bottom: 1px solid #c8e6c9;
        font-size: 13px;
    }
    .issued-item:last-child { border-bottom: none; }
    .issued-item .label {
        color: #666;
        min-width: 80px;
    }
    .issued-item .value {
        font-family: monospace;
        font-size: 15px;
        font-weight: 700;
        color: #2e7d32;
        letter-spacing: 2px;
    }
    .issued-copy-btn {
        background: #2e7d32;
        color: #fff;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        margin-left: 8px;
    }
    .issued-copy-btn:hover { opacity: 0.85; }

    /* 検索・フィルタ */
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .filter-input {
        flex: 1;
        min-width: 180px;
        padding: 8px 12px;
        border: 1px solid #d8d0c4;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
    }
    .filter-input:focus {
        outline: none;
        border-color: #5a5245;
    }
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #d8d0c4;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
    }

    /* デバイステーブル */
    .device-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .device-table th {
        text-align: left;
        padding: 10px 12px;
        border-bottom: 2px solid #e0d8cc;
        font-weight: 500;
        color: #8b7e6a;
        font-size: 12px;
        white-space: nowrap;
    }
    .device-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f0ebe1;
        vertical-align: middle;
    }
    .device-table tr:hover td {
        background: #faf8f4;
    }
    .device-id-cell {
        font-family: monospace;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .status-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
    }
    .status-normal { background: #e8f5e9; color: #2e7d32; }
    .status-warning { background: #fff3e0; color: #e65100; }
    .status-alert { background: #fbe9e7; color: #c62828; }
    .status-offline { background: #eeeeee; color: #616161; }
    .status-inactive { background: #f5f5f5; color: #9e9e9e; }
    .battery-cell {
        font-size: 12px;
    }
    .battery-low { color: #c62828; font-weight: 500; }
    .empty-row {
        text-align: center;
        color: #aaa;
        padding: 40px 12px;
    }

    /* ページネーション */
    .pagination-wrap {
        margin-top: 16px;
        display: flex;
        justify-content: center;
        gap: 4px;
    }
    .pagination-wrap a,
    .pagination-wrap span {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
        color: #5a5245;
    }
    .pagination-wrap a:hover {
        background: #e0d8cc;
    }
    .pagination-wrap .active {
        background: #5a5245;
        color: #fff;
    }
    .pagination-wrap .disabled {
        color: #ccc;
    }
</style>
@endsection

@section('content')

{{-- 統計カード --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">総デバイス</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['active'] }}</div>
        <div class="stat-label">稼働中</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['normal'] }}</div>
        <div class="stat-label">正常</div>
    </div>
    <div class="stat-card alert">
        <div class="stat-value">{{ $stats['alert'] }}</div>
        <div class="stat-label">未検知</div>
    </div>
    <div class="stat-card offline">
        <div class="stat-value">{{ $stats['offline'] }}</div>
        <div class="stat-label">通信途絶</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['inactive'] }}</div>
        <div class="stat-label">未稼働</div>
    </div>
</div>

{{-- デバイス発番 --}}
<div class="card">
    <div class="card-title">デバイス発番</div>
    <div class="issue-section">
        <form method="POST" action="/admin/issue" class="issue-form">
            @csrf
            <button type="submit" class="btn btn-primary">1台発番</button>
        </form>

        <form method="POST" action="/admin/issue-bulk" class="issue-form">
            @csrf
            <div>
                <div class="issue-label">台数</div>
                <input type="number" name="count" class="issue-input" value="5" min="1" max="100">
            </div>
            <button type="submit" class="btn btn-secondary">一括発番</button>
        </form>
    </div>
    @error('count')
        <div style="color:#c62828;font-size:12px;margin-top:8px;">{{ $message }}</div>
    @enderror
</div>

{{-- 発番結果（1台） --}}
@if(session('issued'))
    @php $issued = session('issued'); @endphp
    <div class="issued-result">
        <div class="issued-title">✅ デバイスを発番しました</div>
        <div class="issued-item">
            <span class="label">品番</span>
            <span class="value" id="issued-id">{{ $issued['device_id'] }}</span>
            <button class="issued-copy-btn" onclick="copyText('issued-id')">コピー</button>
        </div>
        <div class="issued-item">
            <span class="label">初期PIN</span>
            <span class="value" id="issued-pin">{{ $issued['pin'] }}</span>
            <button class="issued-copy-btn" onclick="copyText('issued-pin')">コピー</button>
        </div>
    </div>
@endif

{{-- 発番結果（一括） --}}
@if(session('issued_bulk'))
    @php $bulkList = session('issued_bulk'); @endphp
    <div class="issued-result">
        <div class="issued-title">✅ {{ count($bulkList) }}台のデバイスを発番しました</div>
        @foreach($bulkList as $i => $item)
            <div class="issued-item">
                <span class="label">{{ $i + 1 }}.</span>
                <span class="value">{{ $item['device_id'] }}</span>
                <span style="color:#666;margin:0 8px;">/</span>
                <span class="value">{{ $item['pin'] }}</span>
            </div>
        @endforeach
    </div>
@endif

{{-- デバイス一覧 --}}
<div class="card">
    <div class="card-title">デバイス一覧</div>

    <form method="GET" action="/admin" class="filter-bar">
        <input
            type="text"
            name="search"
            class="filter-input"
            placeholder="品番・ニックネームで検索"
            value="{{ request('search') }}"
        >
        <select name="status" class="filter-select">
            <option value="">すべて</option>
            <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>正常</option>
            <option value="alert" {{ request('status') === 'alert' ? 'selected' : '' }}>未検知</option>
            <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>通信途絶</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>未稼働</option>
        </select>
        <button type="submit" class="btn btn-sm btn-secondary">絞り込み</button>
    </form>

    <table class="device-table">
        <thead>
            <tr>
                <th>品番</th>
                <th>表示名</th>
                <th>状態</th>
                <th>電池</th>
                <th>電波</th>
                <th>最終受信</th>
                <th>最終検知</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devices as $device)
                <tr>
                    <td class="device-id-cell">{{ $device->device_id }}</td>
                    <td>{{ $device->nickname ?: '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ $device->status }}">
                            @switch($device->status)
                                @case('normal') 正常 @break
                                @case('warning') 注意 @break
                                @case('alert') 未検知 @break
                                @case('offline') 通信途絶 @break
                                @case('inactive') 未稼働 @break
                            @endswitch
                        </span>
                    </td>
                    <td class="battery-cell {{ $device->battery_pct && $device->battery_pct < 20 ? 'battery-low' : '' }}">
                        {{ $device->battery_pct ? $device->battery_pct . '%' : '-' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $device->rssi ? $device->rssi . 'dBm' : '-' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $device->last_received_at ? $device->last_received_at->format('m/d H:i') : '-' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $device->last_human_detected_at ? $device->last_human_detected_at->format('m/d H:i') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">デバイスがありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ページネーション --}}
    @if($devices->hasPages())
        <div class="pagination-wrap">
            @if($devices->onFirstPage())
                <span class="disabled">←</span>
            @else
                <a href="{{ $devices->previousPageUrl() }}">←</a>
            @endif

            @foreach($devices->getUrlRange(1, $devices->lastPage()) as $page => $url)
                @if($page == $devices->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($devices->hasMorePages())
                <a href="{{ $devices->nextPageUrl() }}">→</a>
            @else
                <span class="disabled">→</span>
            @endif
        </div>
    @endif
</div>

<script>
function copyText(id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        btn.textContent = 'コピー済';
        setTimeout(() => { btn.textContent = 'コピー'; }, 1500);
    });
}
</script>

@endsection
