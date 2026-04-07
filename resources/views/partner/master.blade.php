@extends('layouts.partner')

@section('title', 'ダッシュボード - 管理画面')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 16px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .stat-value { font-size: 28px; font-weight: 700; color: #5a5245; }
    .stat-label { font-size: 11px; color: #999; margin-top: 4px; }
    .stat-card.alert .stat-value { color: #c62828; }
    .stat-card.offline .stat-value { color: #666; }
    .tab-bar { display: flex; gap: 4px; background: var(--white); padding: 4px; border-radius: var(--radius-lg); margin-bottom: 20px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); }
    .tab { flex: 1; padding: 10px 16px; font-size: 13px; font-weight: 600; text-align: center; color: var(--gray-500); background: transparent; border: none; border-radius: var(--radius); cursor: pointer; transition: all 0.2s; font-family: inherit; }
    .tab.active { background: var(--gray-800); color: var(--white); }
    .tab:not(.active):hover { background: var(--beige); color: var(--gray-700); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .issue-section { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .issue-form { display: flex; gap: 8px; align-items: flex-end; }
    .issue-input { width: 80px; padding: 8px 10px; border: 1px solid #d8d0c4; border-radius: 6px; font-size: 14px; font-family: 'Noto Sans JP', sans-serif; background: #faf8f4; text-align: center; }
    .issue-input:focus { outline: none; border-color: #5a5245; }
    .issue-label { font-size: 12px; color: #888; margin-bottom: 4px; }
    .issued-result { background: #e8f5e9; border-radius: 10px; padding: 20px; margin-bottom: 16px; }
    .issued-title { font-size: 14px; font-weight: 500; color: #2e7d32; margin-bottom: 12px; }
    .issued-item { display: flex; gap: 24px; padding: 8px 0; border-bottom: 1px solid #c8e6c9; font-size: 13px; }
    .issued-item:last-child { border-bottom: none; }
    .issued-item .label { color: #666; min-width: 80px; }
    .issued-item .value { font-family: monospace; font-size: 15px; font-weight: 700; color: #2e7d32; letter-spacing: 2px; }
    .issued-copy-btn { background: #2e7d32; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; margin-left: 8px; }
    .issued-copy-btn:hover { opacity: 0.85; }
    .filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
    .filter-input { flex: 1; min-width: 180px; padding: 8px 12px; border: 1px solid #d8d0c4; border-radius: 6px; font-size: 13px; font-family: 'Noto Sans JP', sans-serif; background: #faf8f4; }
    .filter-input:focus { outline: none; border-color: #5a5245; }
    .filter-select { padding: 8px 12px; border: 1px solid #d8d0c4; border-radius: 6px; font-size: 13px; font-family: 'Noto Sans JP', sans-serif; background: #faf8f4; }
    .device-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .device-table th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; white-space: nowrap; }
    .device-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; vertical-align: middle; }
    .device-table tr:hover td { background: #faf8f4; }
    .device-id-cell { font-family: monospace; font-weight: 700; letter-spacing: 1px; }
    .status-badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 500; }
    .status-normal { background: #e8f5e9; color: #2e7d32; }
    .status-warning { background: #fff3e0; color: #e65100; }
    .status-alert { background: #fbe9e7; color: #c62828; }
    .status-offline { background: #eeeeee; color: #616161; }
    .status-inactive { background: #f5f5f5; color: #9e9e9e; }
    .battery-cell { font-size: 12px; }
    .battery-low { color: #c62828; font-weight: 500; }
    .empty-row { text-align: center; color: #aaa; padding: 40px 12px; }
    .pagination-wrap { margin-top: 16px; display: flex; justify-content: center; gap: 4px; }
    .pagination-wrap a, .pagination-wrap span { padding: 6px 12px; border-radius: 4px; font-size: 12px; text-decoration: none; color: #5a5245; }
    .pagination-wrap a:hover { background: #e0d8cc; }
    .pagination-wrap .active { background: #5a5245; color: #fff; }
    .pagination-wrap .disabled { color: #ccc; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .admin-table th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; white-space: nowrap; }
    .admin-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; vertical-align: middle; }
    .admin-table tr:hover td { background: #faf8f4; }
    .role-badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .role-master { background: var(--purple-light, #f3e8ff); color: #7c3aed; }
    .role-operator { background: var(--blue-light, #dbeafe); color: #2563eb; }
    .action-btn { padding: 6px 12px; font-size: 12px; font-weight: 600; font-family: inherit; border: 1px solid var(--gray-300); border-radius: 4px; background: var(--white); color: var(--gray-700); cursor: pointer; transition: all 0.2s; margin-right: 4px; }
    .action-btn:hover { background: var(--beige); }
    .action-btn.danger { color: var(--red); border-color: #fecaca; }
    .action-btn.danger:hover { background: var(--red-light); }
    .toolbar-right { display: flex; gap: 8px; }
    .password-field { display: flex; gap: 8px; align-items: center; }
    .password-field .form-input { flex: 1; }
    .password-generate-btn { padding: 10px 14px; font-size: 13px; font-weight: 600; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--beige); color: var(--gray-700); cursor: pointer; white-space: nowrap; }
    .password-generate-btn:hover { background: var(--gray-200); }
    .watch-toggle { position: relative; width: 44px; height: 24px; display: inline-block; }
    .watch-toggle input { opacity: 0; width: 0; height: 0; }
    .watch-slider { position: absolute; cursor: pointer; inset: 0; background: var(--gray-300, #d1d5db); border-radius: 12px; transition: 0.3s; }
    .watch-slider::before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    .watch-toggle input:checked + .watch-slider { background: #22c55e; }
    .watch-toggle input:checked + .watch-slider::before { transform: translateX(20px); }
    /* 組織テーブル */
    .org-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .org-table th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; white-space: nowrap; }
    .org-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; vertical-align: middle; }
    .org-table tr:hover td { background: #faf8f4; }
    .expires-warn { color: #c62828; font-weight: 600; }
    .expires-ok { color: #2e7d32; }
    .org-notify-icons { display: flex; gap: 6px; align-items: center; font-size: 11px; }
    /* モーダル共通 */
    .modal-section { margin-bottom: 20px; }
    .modal-section-title { font-size: 13px; font-weight: 700; color: var(--gray-600); margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid var(--gray-200); }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    /* デバイス詳細モーダル */
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .detail-item { padding: 10px 12px; background: var(--beige, #faf8f4); border-radius: var(--radius, 6px); }
    .detail-item-label { font-size: 11px; color: var(--gray-500, #888); margin-bottom: 4px; }
    .detail-item-value { font-size: 14px; font-weight: 600; color: var(--gray-800, #333); }
    .detail-form-input { width: 100%; padding: 6px 8px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-800); box-sizing: border-box; }
    .detail-form-input:focus { outline: none; border-color: var(--gray-500); box-shadow: 0 0 0 2px rgba(168,162,158,0.15); }
    .detail-status-row { display: flex; align-items: center; margin-bottom: 16px; }
    .detail-status-badge { display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; }
    .detail-status-badge.normal { background: #e8f5e9; color: #2e7d32; }
    .detail-status-badge.warning { background: #fff3e0; color: #e65100; }
    .detail-status-badge.alert { background: #fbe9e7; color: #c62828; }
    .detail-status-badge.offline { background: #eeeeee; color: #616161; }
    .detail-status-badge.inactive { background: #f5f5f5; color: #9e9e9e; }
    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; }
    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
    .detail-schedule-list { border: 1px solid var(--gray-200); border-radius: var(--radius); overflow: hidden; margin-bottom: 10px; }
    .detail-schedule-item { display: flex; align-items: center; padding: 8px 12px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
    .detail-schedule-item:last-child { border-bottom: none; }
    .detail-schedule-item:nth-child(even) { background: var(--cream); }
    .detail-schedule-icon { width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 13px; margin-right: 8px; flex-shrink: 0; }
    .detail-schedule-icon.oneshot { background: #eff6ff; }
    .detail-schedule-icon.recurring { background: #f0fdf4; }
    .detail-schedule-info { flex: 1; min-width: 0; }
    .detail-schedule-main { font-size: 12px; font-weight: 600; color: var(--gray-800); }
    .detail-schedule-sub { font-size: 11px; color: var(--gray-500); }
    .detail-schedule-del { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--gray-400); background: transparent; border: none; border-radius: 4px; cursor: pointer; flex-shrink: 0; transition: all 0.2s; }
    .detail-schedule-del:hover { color: var(--red); background: var(--red-light); }
    .detail-schedule-empty { padding: 16px; text-align: center; font-size: 12px; color: var(--gray-400); }
    .detail-schedule-add { display: flex; align-items: center; justify-content: center; gap: 4px; width: 100%; padding: 8px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--gray-500); background: var(--cream); border: 1px dashed var(--gray-300); border-radius: var(--radius); cursor: pointer; transition: all 0.2s; }
    .detail-schedule-add:hover { background: var(--beige); color: var(--gray-700); }
    .schedule-type-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
    .schedule-type-tab { flex: 1; padding: 10px; text-align: center; font-size: 13px; font-weight: 600; font-family: inherit; border: 2px solid var(--gray-200); border-radius: var(--radius); background: var(--white); cursor: pointer; transition: all 0.2s; color: var(--gray-600); }
    .schedule-type-tab.active { border-color: var(--gray-800); background: var(--beige); color: var(--gray-800); }
    .schedule-form-group { margin-bottom: 14px; }
    .schedule-form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px; }
    .schedule-form-group input, .schedule-form-group select { width: 100%; padding: 8px 10px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); }
    .schedule-days { display: flex; gap: 6px; flex-wrap: wrap; }
    .schedule-day-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; font-family: inherit; border: 2px solid var(--gray-200); border-radius: 50%; background: var(--white); cursor: pointer; transition: all 0.2s; color: var(--gray-600); }
    .schedule-day-btn.active { border-color: var(--gray-800); background: var(--gray-800); color: var(--white); }
    .schedule-time-row { display: flex; align-items: center; gap: 8px; }
    .schedule-time-row input { width: auto; flex: 1; }
    .schedule-time-row span { font-size: 13px; color: var(--gray-500); white-space: nowrap; }
    .schedule-nextday-check { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 12px; color: var(--gray-600); }
    .mono { font-family: monospace; font-weight: 700; letter-spacing: 1px; }
    .flash-success { background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
    .flash-error { background: #fbe9e7; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
    .toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: var(--radius); font-size: 13px; font-weight: 600; color: var(--white); z-index: 9999; transform: translateY(100px); opacity: 0; transition: all 0.3s; }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.success { background: #2e7d32; }
    .toast.error { background: var(--red); }
    .premium-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: var(--beige); border-radius: var(--radius); margin-bottom: 14px; border: 1px solid var(--gray-200); }
    .premium-toggle-info p:first-child { font-size: 13px; font-weight: 600; color: var(--gray-700); }
    .premium-toggle-info p:last-child { font-size: 11px; color: var(--gray-500); margin-top: 2px; }
    .premium-toggle-control { display: flex; align-items: center; gap: 8px; }
</style>
@endsection

@section('content')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">総デバイス</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['active'] }}</div><div class="stat-label">稼働中</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['normal'] }}</div><div class="stat-label">正常</div></div>
    <div class="stat-card alert"><div class="stat-value">{{ $stats['alert'] }}</div><div class="stat-label">未検知</div></div>
    <div class="stat-card offline"><div class="stat-value">{{ $stats['offline'] }}</div><div class="stat-label">通信途絶</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['inactive'] }}</div><div class="stat-label">未稼働</div></div>
</div>

@if(session('success')) <div class="flash-success">✅ {{ session('success') }}</div> @endif
@if(session('error')) <div class="flash-error">⚠️ {{ session('error') }}</div> @endif

<div class="tab-bar">
    <button class="tab active" onclick="switchTab('devices', this)">デバイス管理</button>
    <button class="tab" onclick="switchTab('admins', this)">管理者アカウント</button>
    <button class="tab" onclick="switchTab('orgs', this)">組織管理</button>
</div>

{{-- ===== デバイス管理タブ ===== --}}
<div id="tab-devices" class="tab-content active">
    <div class="card" id="issueSectionCard">
        <div class="card-title" style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">デバイス発番</div>
        <div class="issue-section">
            <form method="POST" action="/partner/issue" class="issue-form">
                @csrf
                <button type="submit" class="btn btn-primary">1台発番</button>
            </form>
            <form method="POST" action="/partner/issue-bulk" class="issue-form">
                @csrf
                <div>
                    <div class="issue-label">台数</div>
                    <input type="number" name="count" class="issue-input" value="5" min="1" max="100">
                </div>
                <button type="submit" class="btn btn-secondary">一括発番</button>
            </form>
        </div>
        @error('count') <div style="color:#c62828;font-size:12px;margin-top:8px;">{{ $message }}</div> @enderror
    </div>

    @if(session('issued'))
        @php $issued = session('issued'); @endphp
        <div class="issued-result">
            <div class="issued-title">✅ デバイスを発番しました</div>
            <div class="issued-item"><span class="label">品番</span><span class="value" id="issued-id">{{ $issued['device_id'] }}</span><button class="issued-copy-btn" onclick="copyText('issued-id')">コピー</button></div>
            <div class="issued-item"><span class="label">初期PIN</span><span class="value" id="issued-pin">{{ $issued['pin'] }}</span><button class="issued-copy-btn" onclick="copyText('issued-pin')">コピー</button></div>
        </div>
    @endif

    @if(session('issued_bulk'))
        @php $bulkList = session('issued_bulk'); @endphp
        <div class="issued-result">
            <div class="issued-title">✅ {{ count($bulkList) }}台のデバイスを発番しました</div>
            @foreach($bulkList as $i => $item)
                <div class="issued-item"><span class="label">{{ $i + 1 }}.</span><span class="value">{{ $item['device_id'] }}</span><span style="color:#666;margin:0 8px;">/</span><span class="value">{{ $item['pin'] }}</span></div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">デバイス一覧</div>
        <form method="GET" action="/partner" class="filter-bar">
            <input type="hidden" name="tab" value="devices">
            <input type="text" name="search" class="filter-input" placeholder="品番・ニックネームで検索" value="{{ request('search') }}">
            <select name="status" class="filter-select">
                <option value="">すべて</option>
                <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>正常</option>
                <option value="alert" {{ request('status') === 'alert' ? 'selected' : '' }}>未検知</option>
                <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>通信途絶</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>未稼働</option>
            </select>
            <select name="org" class="filter-select">
                <option value="">すべての組織</option>
                <option value="none" {{ request('org') === 'none' ? 'selected' : '' }}>組織未割当</option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ request('org') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-secondary">絞り込み</button>
        </form>
        <table class="device-table">
            <thead>
                <tr><th>品番</th><th>表示名</th><th>状態</th><th>組織</th><th>電池</th><th>電波</th><th>最終受信</th><th>最終検知</th><th>操作</th></tr>
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
                        <td style="font-size:12px;color:var(--gray-600);">{{ $device->organization ? $device->organization->name : '-' }}</td>
                        <td class="battery-cell {{ $device->battery_pct && $device->battery_pct < 20 ? 'battery-low' : '' }}">{{ $device->battery_pct ? $device->battery_pct . '%' : '-' }}</td>
                        <td style="font-size:12px;">{{ $device->rssi ? $device->rssi . 'dBm' : '-' }}</td>
                        <td style="font-size:12px;">{{ $device->last_received_at ? $device->last_received_at->format('m/d H:i') : '-' }}</td>
                        <td style="font-size:12px;">{{ $device->last_human_detected_at ? $device->last_human_detected_at->format('m/d H:i') : '-' }}</td>
                        <td><button class="action-btn" onclick="showDeviceDetail('{{ $device->device_id }}')">詳細</button><button class="action-btn danger" onclick="confirmDeleteDevice('{{ $device->device_id }}')">削除</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">
                            デバイスがありません。デバイス追加を行ってください。<br>
                            <button class="btn btn-sm btn-primary" style="margin-top:10px;" onclick="scrollToIssueSection()">＋ デバイスを発番する</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($devices->hasPages())
            <div class="pagination-wrap">
                @if($devices->onFirstPage()) <span class="disabled">←</span> @else <a href="{{ $devices->previousPageUrl() }}">←</a> @endif
                @foreach($devices->getUrlRange(1, $devices->lastPage()) as $page => $url)
                    @if($page == $devices->currentPage()) <span class="active">{{ $page }}</span> @else <a href="{{ $url }}">{{ $page }}</a> @endif
                @endforeach
                @if($devices->hasMorePages()) <a href="{{ $devices->nextPageUrl() }}">→</a> @else <span class="disabled">→</span> @endif
            </div>
        @endif
    </div>
</div>

{{-- ===== 管理者アカウントタブ ===== --}}
<div id="tab-admins" class="tab-content">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:600;color:#5a5245;">管理者アカウント一覧</div>
            <div class="toolbar-right"><button class="btn btn-sm btn-primary" onclick="showAddAdminModal()">＋ アカウント追加</button></div>
        </div>
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>名前</th><th>メールアドレス</th><th>権限</th><th>所属組織</th><th>最終ログイン</th><th>作成日</th><th>操作</th></tr>
            </thead>
            <tbody>
                @forelse($adminUsers as $admin)
                    <tr>
                        <td style="font-size:12px;color:#999;">{{ $admin->id }}</td>
                        <td style="font-weight:500;">{{ $admin->name }}</td>
                        <td style="font-size:13px;">{{ $admin->email }}</td>
                        <td><span class="role-badge {{ $admin->role === 'master' ? 'role-master' : 'role-operator' }}">{{ $admin->role === 'master' ? 'マスター' : 'オペレーター' }}</span></td>
                        <td style="font-size:12px;color:var(--gray-600);">{{ $admin->organization ? $admin->organization->name : '-' }}</td>
                        <td style="font-size:12px;color:#888;">{{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->format('Y/m/d H:i') : '未ログイン' }}</td>
                        <td style="font-size:12px;color:#888;">{{ $admin->created_at->format('Y/m/d') }}</td>
                        <td>
                            <button class="action-btn" onclick="showEditAdminModal({{ json_encode(['id' => $admin->id, 'name' => $admin->name, 'email' => $admin->email, 'role' => $admin->role, 'organization_id' => $admin->organization_id]) }})">編集</button>
                            @if($admin->id !== Auth::guard('partner')->id())
                                <button class="action-btn danger" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ $admin->name }}')">削除</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-row">管理者アカウントがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== 組織管理タブ ===== --}}
<div id="tab-orgs" class="tab-content">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:600;color:#5a5245;">組織一覧</div>
            <button class="btn btn-sm btn-primary" onclick="showAddOrgModal()">＋ 組織追加</button>
        </div>
        <table class="org-table">
            <thead>
                <tr><th>組織名</th><th>担当者</th><th>連絡先</th><th>デバイス数</th><th>配送先住所</th><th>通知</th><th>操作</th></tr>
            </thead>
            <tbody>
                @forelse($organizations as $org)
                    @php
                        $hasEmail = $org->notification_email_1 || $org->notification_email_2 || $org->notification_email_3;
                        $hasSms = $org->notification_sms_1 || $org->notification_sms_2;
                    @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $org->name }}</td>
                        <td style="font-size:12px;">{{ $org->contact_name ?: '-' }}</td>
                        <td style="font-size:12px;">{{ $org->contact_email }}</td>
                        <td style="font-size:13px;">{{ $org->devices_count }}台</td>
                        <td style="font-size:12px;color:var(--gray-600);">{{ $org->delivery_address ?: '-' }}</td>

                        <td>
                            <div class="org-notify-icons">
                                @if($hasEmail) <span title="メール" style="{{ $org->notification_enabled ? '' : 'opacity:0.4;' }}">📧</span> @endif
                                @if($hasSms) <span title="SMS" style="{{ $org->notification_sms_enabled ? '' : 'opacity:0.4;' }}">💬</span> @endif
                                @if(!$hasEmail && !$hasSms) <span style="color:var(--gray-300);font-size:11px;">未設定</span> @endif
                            </div>
                        </td>
                        <td>
                            <button class="action-btn" onclick="showEditOrgModal({{ json_encode(['id'=>$org->id,'name'=>$org->name,'contact_name'=>$org->contact_name,'contact_email'=>$org->contact_email,'contact_phone'=>$org->contact_phone,'address'=>$org->address,'notes'=>$org->notes,'device_limit'=>$org->device_limit??100,'expires_at'=>$org->expires_at?$org->expires_at->format('Y-m-d'):'','notification_email_1'=>$org->notification_email_1,'notification_email_2'=>$org->notification_email_2,'notification_email_3'=>$org->notification_email_3,'notification_sms_1'=>$org->notification_sms_1,'notification_sms_2'=>$org->notification_sms_2]) }})">編集</button>
                            @if($org->devices_count === 0) <button class="action-btn danger" onclick="confirmDeleteOrg({{ $org->id }}, '{{ $org->name }}')">削除</button> @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">組織がありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== デバイス詳細モーダル（編集可能） ===== --}}
<div id="deviceDetailModal" class="modal-overlay" onclick="if(event.target===this)hideModal('deviceDetailModal')">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>📋 デバイス詳細</h3><button class="modal-close" onclick="hideModal('deviceDetailModal')">×</button></div>
        <div class="modal-body">
            <div class="detail-status-row">
                <div class="detail-status-badge normal" id="masterDetailStatusBadge">-</div>
                <button class="detail-clear-alert-btn" id="masterDetailClearAlertBtn" style="display:none;" onclick="masterClearAlert()">✕ 警告解除</button>
            </div>
            <div class="modal-section">
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">デバイスID</p><p class="detail-item-value mono" id="masterDetailDeviceId">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">最終検知</p><p class="detail-item-value" id="masterDetailLastDetected">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">部屋番号</p><input type="text" class="detail-form-input" id="masterDetailRoom" placeholder="101"></div>
                    <div class="detail-item"><p class="detail-item-label">入居者名</p><input type="text" class="detail-form-input" id="masterDetailTenant" placeholder="山田 太郎"></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">📊 センサー状態</div>
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">電池残量</p><p class="detail-item-value" id="masterDetailBattery">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">電波強度</p><p class="detail-item-value" id="masterDetailSignal">-</p></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">⚙️ 設定</div>
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">アラート閾値</p>
                        <select class="detail-form-input" id="masterDetailAlertHours">
                            <option value="12">12時間</option><option value="24">24時間</option><option value="36">36時間</option><option value="48">48時間</option><option value="72">72時間</option>
                        </select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">設置高さ</p>
                        <div style="display:flex;align-items:center;gap:4px;"><input type="number" class="detail-form-input" id="masterDetailHeight" min="100" max="300" style="width:70px;"><span style="font-size:12px;color:var(--gray-500);">cm</span></div>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">ペット除外</p>
                        <select class="detail-form-input" id="masterDetailPetExclusion"><option value="0">OFF</option><option value="1">ON</option></select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">外出モード</p>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <label class="watch-toggle"><input type="checkbox" id="masterDetailAwayMode" onchange="masterToggleAwayMode(this.checked)"><span class="watch-slider"></span></label>
                            <span id="masterDetailAwayLabel" style="font-size:12px;color:var(--gray-600);">OFF</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">📝 登録情報</div>
                <div class="detail-grid">
                    <div class="detail-item" style="grid-column: span 2;">
                        <p class="detail-item-label">📡 SIM ID（ICCID）</p>
                        <input type="text" class="detail-form-input" id="masterDetailSimId" placeholder="例：89882806660000123456" maxlength="22" style="font-family:monospace;letter-spacing:1px;" inputmode="numeric">
                        <p style="font-size:11px;color:var(--gray-500);margin-top:4px;">SIMカード裏面または1NCE管理画面のICCID（数字のみ・最大22桁）。デバイスからのJSONに含まれるSIM IDと品番を紐づけます。</p>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">登録日</p><p class="detail-item-value" id="masterDetailRegistered">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">メモ</p><input type="text" class="detail-form-input" id="masterDetailMemo" placeholder="メモを追加..." maxlength="200"></div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <p class="detail-item-label">💳 決済開始日</p>
                        <input type="date" class="detail-form-input" id="masterDetailBillingStartDate" style="max-width:180px;">
                        <p style="font-size:11px;color:var(--gray-500);margin-top:4px;">※ デフォルトは翌月1日。この日付から pay.jp の定期課金が開始されます。カード登録後にマスターが設定してください。</p>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">🔔 通知設定</div>
                {{-- プレミアムトグル（マスター専用） --}}
                <div class="premium-toggle-row" id="masterPremiumToggleRow">
                    <div class="premium-toggle-info">
                        <p>⭐ プレミアム（SMS/電話通知）</p>
                        <p id="masterPremiumOrgLabel">組織全体に適用されます</p>
                    </div>
                    <div class="premium-toggle-control">
                        <label class="watch-toggle"><input type="checkbox" id="masterDetailPremiumToggle" onchange="masterTogglePremium(this.checked)"><span class="watch-slider"></span></label>
                        <span id="masterDetailPremiumLabel" style="font-size:12px;color:var(--gray-500);">無効</span>
                    </div>
                </div>
                <div id="masterDetailPremiumNote" style="display:none;padding:10px 12px;background:var(--yellow-light);border-radius:var(--radius);margin-bottom:12px;font-size:12px;color:#a16207;">
                    ⚠️ SMS・電話通知はプレミアム契約が必要です。上のトグルで有効にしてください。
                </div>
                <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">💬 SMS通知</p>
                        <label class="watch-toggle"><input type="checkbox" id="masterDetailSmsEnabled" onchange="masterSaveNotification()"><span class="watch-slider"></span></label>
                    </div>
                    <input type="tel" class="detail-form-input" id="masterDetailSmsPhone1" placeholder="09012345678" style="margin-bottom:6px;" onblur="masterSaveNotification()">
                    <input type="tel" class="detail-form-input" id="masterDetailSmsPhone2" placeholder="09012345678（任意）" onblur="masterSaveNotification()">
                </div>
                <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">📞 電話通知（AIコール）</p>
                        <label class="watch-toggle"><input type="checkbox" id="masterDetailVoiceEnabled" onchange="masterSaveNotification()"><span class="watch-slider"></span></label>
                    </div>
                    <input type="tel" class="detail-form-input" id="masterDetailVoicePhone1" placeholder="09012345678" style="margin-bottom:6px;" onblur="masterSaveNotification()">
                    <input type="tel" class="detail-form-input" id="masterDetailVoicePhone2" placeholder="09012345678（任意）" onblur="masterSaveNotification()">
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">🚶 外出スケジュール</div>
                <div id="masterDetailScheduleList"></div>
                <button class="detail-schedule-add" onclick="masterOpenScheduleAdd()">＋ 外出スケジュール追加</button>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('deviceDetailModal')">閉じる</button>
            <button class="btn btn-primary" onclick="masterSaveAssignment()">保存</button>
        </div>
    </div>
</div>

{{-- ===== スケジュール追加モーダル ===== --}}
<div id="masterScheduleAddModal" class="modal-overlay" onclick="if(event.target===this)hideModal('masterScheduleAddModal')">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header"><h3>🚶 外出スケジュール追加</h3><button class="modal-close" onclick="hideModal('masterScheduleAddModal')">×</button></div>
        <div class="modal-body">
            <div class="schedule-type-tabs">
                <button class="schedule-type-tab active" id="masterTabOneshot" onclick="masterSwitchScheduleType('oneshot')">📅 単発</button>
                <button class="schedule-type-tab" id="masterTabRecurring" onclick="masterSwitchScheduleType('recurring')">🔁 定期</button>
            </div>
            <div id="masterOneshotForm">
                <div class="schedule-form-group"><label>開始日時</label><input type="datetime-local" id="masterSchedStartAt"></div>
                <div class="schedule-form-group"><label>終了日時（空欄＝手動復帰）</label><input type="datetime-local" id="masterSchedEndAt"></div>
            </div>
            <div id="masterRecurringForm" style="display:none;">
                <div class="schedule-form-group"><label>曜日</label>
                    <div class="schedule-days" id="masterScheduleDays">
                        <button type="button" class="schedule-day-btn" data-day="0" onclick="toggleDay(this)">日</button>
                        <button type="button" class="schedule-day-btn" data-day="1" onclick="toggleDay(this)">月</button>
                        <button type="button" class="schedule-day-btn" data-day="2" onclick="toggleDay(this)">火</button>
                        <button type="button" class="schedule-day-btn" data-day="3" onclick="toggleDay(this)">水</button>
                        <button type="button" class="schedule-day-btn" data-day="4" onclick="toggleDay(this)">木</button>
                        <button type="button" class="schedule-day-btn" data-day="5" onclick="toggleDay(this)">金</button>
                        <button type="button" class="schedule-day-btn" data-day="6" onclick="toggleDay(this)">土</button>
                    </div>
                </div>
                <div class="schedule-form-group"><label>時間帯</label>
                    <div class="schedule-time-row"><input type="time" id="masterSchedStartTime"><span>〜</span><input type="time" id="masterSchedEndTime"></div>
                    <label class="schedule-nextday-check"><input type="checkbox" id="masterSchedNextDay"> 翌日にまたがる</label>
                </div>
            </div>
            <div class="schedule-form-group"><label>メモ（任意）</label><input type="text" id="masterSchedMemo" placeholder="例：デイサービス" maxlength="200"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('masterScheduleAddModal')">キャンセル</button>
            <button class="btn btn-primary" onclick="masterSubmitSchedule()">追加</button>
        </div>
    </div>
</div>

{{-- ===== スケジュール削除確認モーダル ===== --}}
<div id="masterScheduleDeleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('masterScheduleDeleteModal')">
    <div class="modal"><div class="modal-header"><h3>⚠️ 外出スケジュール削除</h3><button class="modal-close" onclick="hideModal('masterScheduleDeleteModal')">×</button></div>
        <div class="modal-body"><p>このスケジュールを削除しますか？</p></div>
        <div class="modal-footer"><button class="btn btn-secondary" onclick="hideModal('masterScheduleDeleteModal')">キャンセル</button><button class="btn btn-danger" onclick="masterExecuteDeleteSchedule()">削除する</button></div>
    </div>
</div>

{{-- ===== 管理者追加モーダル ===== --}}
<div id="addAdminModal" class="modal-overlay" onclick="if(event.target===this)hideAddAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>管理者アカウント追加</h3><button class="modal-close" onclick="hideAddAdminModal()">×</button></div>
        <form method="POST" action="{{ route('partner.admin-users.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label class="form-label">名前 *</label><input type="text" name="name" class="form-input" placeholder="例：山田太郎" required></div>
                <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" name="email" class="form-input" placeholder="admin@example.com" required><p class="form-hint">このアドレスでログインします</p></div>
                <div class="form-group">
                    <label class="form-label">権限 *</label>
                    <select name="role" class="form-input" id="addAdminRole" onchange="toggleOrgSelect('addAdminOrgRow', this.value)">
                        <option value="operator">オペレーター（組織管理者）</option>
                        <option value="master">マスター（全権限）</option>
                    </select>
                    <p class="form-hint">オペレーター：担当組織のみ / マスター：全機能</p>
                </div>
                <div class="form-group" id="addAdminOrgRow">
                    <label class="form-label">所属組織</label>
                    <select name="organization_id" class="form-input">
                        <option value="">未割当</option>
                        @foreach($organizations as $org) <option value="{{ $org->id }}">{{ $org->name }}</option> @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">初期パスワード *</label>
                    <div class="password-field">
                        <input type="text" name="password" id="addAdminPassword" class="form-input" required>
                        <button type="button" class="password-generate-btn" onclick="generatePassword('addAdminPassword')">生成</button>
                    </div>
                    <p class="form-hint">初回ログイン後にパスワード変更を推奨</p>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideAddAdminModal()">キャンセル</button><button type="submit" class="btn btn-primary">作成</button></div>
        </form>
    </div>
</div>

{{-- ===== 管理者編集モーダル ===== --}}
<div id="editAdminModal" class="modal-overlay" onclick="if(event.target===this)hideEditAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>管理者アカウント編集</h3><button class="modal-close" onclick="hideEditAdminModal()">×</button></div>
        <form method="POST" id="editAdminForm" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label class="form-label">名前 *</label><input type="text" name="name" id="editAdminName" class="form-input" required></div>
                <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" name="email" id="editAdminEmail" class="form-input" required></div>
                <div class="form-group">
                    <label class="form-label">権限 *</label>
                    <select name="role" id="editAdminRole" class="form-input" onchange="toggleOrgSelect('editAdminOrgRow', this.value)">
                        <option value="operator">オペレーター</option>
                        <option value="master">マスター</option>
                    </select>
                </div>
                <div class="form-group" id="editAdminOrgRow">
                    <label class="form-label">所属組織</label>
                    <select name="organization_id" id="editAdminOrg" class="form-input">
                        <option value="">未割当</option>
                        @foreach($organizations as $org) <option value="{{ $org->id }}">{{ $org->name }}</option> @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">新しいパスワード</label>
                    <div class="password-field">
                        <input type="text" name="password" id="editAdminPassword" class="form-input" placeholder="変更しない場合は空欄">
                        <button type="button" class="password-generate-btn" onclick="generatePassword('editAdminPassword')">生成</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditAdminModal()">キャンセル</button><button type="submit" class="btn btn-primary">保存</button></div>
        </form>
    </div>
</div>

<form id="deleteAdminForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>

{{-- ===== 組織追加モーダル ===== --}}
<div id="addOrgModal" class="modal-overlay" onclick="if(event.target===this)hideAddOrgModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>組織追加</h3><button class="modal-close" onclick="hideAddOrgModal()">×</button></div>
        <form method="POST" action="{{ route('partner.orgs.store') }}">
            @csrf
            <div class="modal-body">
                <div class="modal-section"><div class="modal-section-title">基本情報</div>
                    <div class="form-group"><label class="form-label">組織名 *</label><input type="text" name="name" class="form-input" required></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">担当者名</label><input type="text" name="contact_name" class="form-input"></div>
                        <div class="form-group"><label class="form-label">連絡先メール *</label><input type="email" name="contact_email" class="form-input" required></div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">電話番号</label><input type="text" name="contact_phone" class="form-input"></div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-group"><label class="form-label">住所</label><input type="text" name="address" class="form-input"></div>
                    <div class="form-group"><label class="form-label">メモ</label><textarea name="notes" class="form-input" rows="2" style="resize:vertical;"></textarea></div>
                </div>
                <div class="modal-section"><div class="modal-section-title">契約情報</div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">台数上限</label><input type="number" name="device_limit" class="form-input" value="100" min="1" max="9999"><p class="form-hint">最大登録台数</p></div>
                        <div class="form-group"><label class="form-label">契約期限</label><input type="date" name="expires_at" class="form-input"><p class="form-hint">空欄＝無期限</p></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideAddOrgModal()">キャンセル</button><button type="submit" class="btn btn-primary">作成</button></div>
        </form>
    </div>
</div>

{{-- ===== 組織編集モーダル ===== --}}
<div id="editOrgModal" class="modal-overlay" onclick="if(event.target===this)hideEditOrgModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>組織編集</h3><button class="modal-close" onclick="hideEditOrgModal()">×</button></div>
        <form method="POST" id="editOrgForm" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="modal-section"><div class="modal-section-title">基本情報</div>
                    <div class="form-group"><label class="form-label">組織名 *</label><input type="text" name="name" id="editOrgName" class="form-input" required></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">担当者名</label><input type="text" name="contact_name" id="editOrgContactName" class="form-input"></div>
                        <div class="form-group"><label class="form-label">連絡先メール *</label><input type="email" name="contact_email" id="editOrgContactEmail" class="form-input" required></div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">電話番号</label><input type="text" name="contact_phone" id="editOrgContactPhone" class="form-input"></div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-group"><label class="form-label">住所</label><input type="text" name="address" id="editOrgAddress" class="form-input"></div>
                    <div class="form-group"><label class="form-label">メモ</label><textarea name="notes" id="editOrgNotes" class="form-input" rows="2" style="resize:vertical;"></textarea></div>
                </div>
                <div class="modal-section"><div class="modal-section-title">契約情報</div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">台数上限</label><input type="number" name="device_limit" id="editOrgDeviceLimit" class="form-input" min="1" max="9999"></div>
                        <div class="form-group"><label class="form-label">契約期限</label><input type="date" name="expires_at" id="editOrgExpiresAt" class="form-input"><p class="form-hint">空欄＝無期限</p></div>
                    </div>
                </div>
                <div class="modal-section"><div class="modal-section-title">通知設定（確認・修正用）</div>
                    <p style="font-size:12px;color:var(--gray-500);margin-bottom:12px;">通常はパートナー側で設定します。</p>
                    <div class="form-group"><label class="form-label">通知メール 1</label><input type="email" name="notification_email_1" id="editOrgEmail1" class="form-input"></div>
                    <div class="form-group"><label class="form-label">通知メール 2</label><input type="email" name="notification_email_2" id="editOrgEmail2" class="form-input"></div>
                    <div class="form-group"><label class="form-label">通知メール 3</label><input type="email" name="notification_email_3" id="editOrgEmail3" class="form-input"></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">SMS通知先 1</label><input type="text" name="notification_sms_1" id="editOrgSms1" class="form-input"></div>
                        <div class="form-group"><label class="form-label">SMS通知先 2</label><input type="text" name="notification_sms_2" id="editOrgSms2" class="form-input"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditOrgModal()">キャンセル</button><button type="submit" class="btn btn-primary">保存</button></div>
        </form>
    </div>
</div>

<form id="deleteOrgForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>

<div id="toast" class="toast"></div>

@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let masterCurrentDeviceId = null;
let masterCurrentOrgId = null;
let masterScheduleType = 'oneshot';
let masterDeleteScheduleId = null;

function showModal(id) { document.getElementById(id).classList.add('show'); }
function hideModal(id) { document.getElementById(id).classList.remove('show'); }
function showToast(msg, type) { const t = document.getElementById('toast'); t.textContent = msg; t.className = 'toast ' + type + ' show'; setTimeout(() => t.classList.remove('show'), 3000); }
function escapeHtml(s) { if (!s) return '-'; const d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

function scrollToIssueSection() {
    const el = document.getElementById('issueSectionCard');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function switchTab(tabName, btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'admins') switchTab('admins', document.querySelectorAll('.tab')[1]);
    else if (tab === 'orgs') switchTab('orgs', document.querySelectorAll('.tab')[2]);
});

function copyText(id) {
    navigator.clipboard.writeText(document.getElementById(id).textContent).then(() => {
        const btn = event.target; btn.textContent = 'コピー済'; setTimeout(() => { btn.textContent = 'コピー'; }, 1500);
    });
}

function generatePassword(inputId) {
    const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let pw = ''; for (let i = 0; i < 12; i++) pw += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById(inputId).value = pw;
}

// ===== デバイス詳細 =====
async function showDeviceDetail(deviceId) {
    masterCurrentDeviceId = deviceId;
    masterCurrentOrgId = null;
    showModal('deviceDetailModal');

    try {
        const res = await fetch('/partner/devices/' + deviceId + '/detail', { headers: { 'Accept': 'application/json' } });
        const d = await res.json();

        masterCurrentOrgId = d.organization_id || null;

        const statusLabels = { normal: '正常稼働中', warning: '注意', alert: '未検知警告', offline: '通信途絶', inactive: '未稼働' };
        const badge = document.getElementById('masterDetailStatusBadge');
        badge.textContent = statusLabels[d.status] || d.status;
        badge.className = 'detail-status-badge ' + (d.status || 'inactive');
        document.getElementById('masterDetailClearAlertBtn').style.display = d.status === 'alert' ? 'inline-flex' : 'none';

        document.getElementById('masterDetailDeviceId').textContent = d.device_id;
        document.getElementById('masterDetailLastDetected').textContent = d.last_human_detected_at || '-';
        document.getElementById('masterDetailRoom').value = d.room_number || '';
        document.getElementById('masterDetailTenant').value = d.tenant_name || '';

        const battEl = document.getElementById('masterDetailBattery');
        battEl.textContent = (d.battery_pct !== null ? d.battery_pct + '%' : '-') + (d.battery_voltage ? ' / ' + d.battery_voltage + 'V' : '');
        battEl.style.color = (d.battery_pct !== null && d.battery_pct < 20) ? '#c62828' : '';
        document.getElementById('masterDetailSignal').textContent = d.rssi_label || '-';

        document.getElementById('masterDetailAlertHours').value = d.alert_threshold_hours || 24;
        document.getElementById('masterDetailHeight').value = d.install_height_cm || 200;
        document.getElementById('masterDetailPetExclusion').value = d.pet_exclusion_enabled ? '1' : '0';
        document.getElementById('masterDetailAwayMode').checked = d.away_mode;
        document.getElementById('masterDetailAwayLabel').textContent = d.away_mode ? ('ON' + (d.away_until ? '（' + d.away_until + 'まで）' : '')) : 'OFF';

        document.getElementById('masterDetailSimId').value = d.sim_id || '';
        document.getElementById('masterDetailRegistered').textContent = d.registered_at || '-';
        document.getElementById('masterDetailMemo').value = d.memo || '';

        // 決済開始日（デフォルト：翌月1日）
        const now = new Date();
        const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        const defaultBillingDate = nextMonth.toISOString().split('T')[0];
        document.getElementById('masterDetailBillingStartDate').value = d.billing_start_date || defaultBillingDate;

        // プレミアムトグル
        const isPremium = d.premium_enabled || false;
        document.getElementById('masterDetailPremiumToggle').checked = isPremium;
        document.getElementById('masterDetailPremiumLabel').textContent = isPremium ? '有効' : '無効';
        // 組織未割当の場合はトグルを無効化
        const premiumToggle = document.getElementById('masterDetailPremiumToggle');
        premiumToggle.disabled = !masterCurrentOrgId;
        premiumToggle.style.opacity = masterCurrentOrgId ? '' : '0.4';
        premiumToggle.style.cursor = masterCurrentOrgId ? '' : 'not-allowed';
        const orgLabel = d.organization_name ? d.organization_name + ' 全体に適用' : '組織未割当（変更不可）';
        document.getElementById('masterPremiumOrgLabel').textContent = orgLabel;

        // SMS/電話のinput活性制御
        masterApplyPremiumState(isPremium);

        document.getElementById('masterDetailSmsEnabled').checked = d.sms_enabled || false;
        document.getElementById('masterDetailSmsPhone1').value = d.sms_phone_1 || '';
        document.getElementById('masterDetailSmsPhone2').value = d.sms_phone_2 || '';
        document.getElementById('masterDetailVoiceEnabled').checked = d.voice_enabled || false;
        document.getElementById('masterDetailVoicePhone1').value = d.voice_phone_1 || '';
        document.getElementById('masterDetailVoicePhone2').value = d.voice_phone_2 || '';

        masterRenderSchedules(d.schedules || []);
    } catch(e) { showToast('詳細の取得に失敗しました', 'error'); }
}

function masterApplyPremiumState(isPremium) {
    document.getElementById('masterDetailPremiumNote').style.display = isPremium ? 'none' : '';
    ['masterDetailSmsEnabled','masterDetailSmsPhone1','masterDetailSmsPhone2','masterDetailVoiceEnabled','masterDetailVoicePhone1','masterDetailVoicePhone2'].forEach(id => {
        const el = document.getElementById(id);
        el.disabled = !isPremium;
        el.style.opacity = isPremium ? '' : '0.4';
        el.style.cursor = isPremium ? '' : 'not-allowed';
    });
}

async function masterTogglePremium(enabled) {
    if (!masterCurrentOrgId) {
        showToast('組織に割り当てられていないデバイスです', 'error');
        document.getElementById('masterDetailPremiumToggle').checked = !enabled;
        return;
    }
    try {
        const res = await fetch('/partner/orgs/' + masterCurrentOrgId + '/toggle-premium', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ premium_enabled: enabled ? 1 : 0 })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('masterDetailPremiumLabel').textContent = enabled ? '有効' : '無効';
            masterApplyPremiumState(enabled);
            // 組織タブのトグルも同期
            const orgLabel = document.querySelector('.org-premium-label-' + masterCurrentOrgId);
            if (orgLabel) orgLabel.textContent = enabled ? '有効' : '無効';
            showToast(enabled ? 'プレミアムを有効にしました' : 'プレミアムを無効にしました', 'success');
        } else {
            document.getElementById('masterDetailPremiumToggle').checked = !enabled;
            showToast('エラーが発生しました', 'error');
        }
    } catch(e) {
        document.getElementById('masterDetailPremiumToggle').checked = !enabled;
        showToast('通信エラーが発生しました', 'error');
    }
}

async function masterSaveAssignment() {
    if (!masterCurrentDeviceId) return;
    const payload = {
        room_number: document.getElementById('masterDetailRoom').value || null,
        tenant_name: document.getElementById('masterDetailTenant').value || null,
        memo: document.getElementById('masterDetailMemo').value || null,
        alert_threshold_hours: parseInt(document.getElementById('masterDetailAlertHours').value) || 24,
        install_height_cm: parseInt(document.getElementById('masterDetailHeight').value) || 200,
        pet_exclusion_enabled: document.getElementById('masterDetailPetExclusion').value === '1' ? 1 : 0,
        billing_start_date: document.getElementById('masterDetailBillingStartDate').value || null,
        sim_id: document.getElementById('masterDetailSimId').value || null,
    };
    try {
        const res = await fetch('/partner/devices/' + masterCurrentDeviceId + '/assignment', {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.success) { showToast('保存しました', 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || '保存に失敗しました', 'error');
    } catch(e) { showToast('通信エラーが発生しました', 'error'); }
}

async function masterSaveNotification() {
    if (!masterCurrentDeviceId) return;
    const payload = {
        sms_enabled: document.getElementById('masterDetailSmsEnabled').checked ? 1 : 0,
        sms_phone_1: document.getElementById('masterDetailSmsPhone1').value || null,
        sms_phone_2: document.getElementById('masterDetailSmsPhone2').value || null,
        voice_enabled: document.getElementById('masterDetailVoiceEnabled').checked ? 1 : 0,
        voice_phone_1: document.getElementById('masterDetailVoicePhone1').value || null,
        voice_phone_2: document.getElementById('masterDetailVoicePhone2').value || null,
    };
    fetch('/partner/devices/' + masterCurrentDeviceId + '/notification', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
    }).then(r => r.json()).then(d => { if (d.success) showToast('通知設定を保存しました', 'success'); })
    .catch(() => showToast('保存に失敗しました', 'error'));
}

async function masterToggleAwayMode(checked) {
    if (!masterCurrentDeviceId) return;
    fetch('/partner/devices/' + masterCurrentDeviceId + '/toggle-watch', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ away_mode: checked })
    }).then(r => r.json()).then(d => {
        if (d.success) { document.getElementById('masterDetailAwayLabel').textContent = checked ? 'ON' : 'OFF'; showToast(d.message, 'success'); }
        else { document.getElementById('masterDetailAwayMode').checked = !checked; showToast('エラー', 'error'); }
    }).catch(() => showToast('通信エラー', 'error'));
}

async function masterClearAlert() {
    if (!masterCurrentDeviceId) return;
    if (!confirm('デバイス ' + masterCurrentDeviceId + ' の警告を解除しますか？\nステータスが初期状態に戻り、検知ログがクリアされます。')) return;
    fetch('/partner/devices/' + masterCurrentDeviceId + '/clear-alert', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); hideModal('deviceDetailModal'); setTimeout(() => location.reload(), 500); }
        else showToast(d.message || 'エラー', 'error');
    }).catch(() => showToast('通信エラー', 'error'));
}

// ===== スケジュール =====
function masterRenderSchedules(schedules) {
    const c = document.getElementById('masterDetailScheduleList');
    if (!schedules.length) { c.innerHTML = '<div class="detail-schedule-empty">外出スケジュールなし</div>'; return; }
    let html = '<div class="detail-schedule-list">';
    schedules.forEach(s => {
        html += '<div class="detail-schedule-item">';
        if (s.type === 'oneshot') {
            html += '<div class="detail-schedule-icon oneshot">📅</div><div class="detail-schedule-info"><p class="detail-schedule-main">' + formatDt(s.start_at) + ' 〜 ' + (s.end_at ? formatDt(s.end_at) : '手動復帰') + '</p><p class="detail-schedule-sub">' + escapeHtml(s.memo || '単発') + '</p></div>';
        } else {
            html += '<div class="detail-schedule-icon recurring">🔁</div><div class="detail-schedule-info"><p class="detail-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + s.start_time + '〜' + (s.next_day ? '翌' : '') + s.end_time + '</p><p class="detail-schedule-sub">' + escapeHtml(s.memo || '定期') + '</p></div>';
        }
        html += '<button class="detail-schedule-del" onclick="masterConfirmDeleteSchedule(' + s.id + ')">×</button></div>';
    });
    c.innerHTML = html + '</div>';
}

function formatDt(dtStr) {
    if (!dtStr) return '-';
    const p = dtStr.split(' ');
    if (p.length === 2) { const d = p[0].split('-'); if (d.length === 3) return parseInt(d[1]) + '/' + parseInt(d[2]) + ' ' + p[1]; }
    return dtStr;
}

function masterOpenScheduleAdd() {
    masterScheduleType = 'oneshot';
    ['masterSchedStartAt','masterSchedEndAt','masterSchedStartTime','masterSchedEndTime','masterSchedMemo'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('masterSchedNextDay').checked = false;
    document.querySelectorAll('#masterScheduleDays .schedule-day-btn').forEach(b => b.classList.remove('active'));
    masterSwitchScheduleType('oneshot');
    showModal('masterScheduleAddModal');
}

function masterSwitchScheduleType(type) {
    masterScheduleType = type;
    document.getElementById('masterTabOneshot').classList.toggle('active', type === 'oneshot');
    document.getElementById('masterTabRecurring').classList.toggle('active', type === 'recurring');
    document.getElementById('masterOneshotForm').style.display = type === 'oneshot' ? 'block' : 'none';
    document.getElementById('masterRecurringForm').style.display = type === 'recurring' ? 'block' : 'none';
}

function toggleDay(btn) { btn.classList.toggle('active'); }

async function masterSubmitSchedule() {
    if (!masterCurrentDeviceId) return;
    const payload = { type: masterScheduleType, memo: document.getElementById('masterSchedMemo').value || null };
    if (masterScheduleType === 'oneshot') {
        const startAt = document.getElementById('masterSchedStartAt').value;
        if (!startAt) { showToast('開始日時を入力してください', 'error'); return; }
        payload.start_at = startAt;
        const endAt = document.getElementById('masterSchedEndAt').value;
        if (endAt) payload.end_at = endAt;
    } else {
        const days = []; document.querySelectorAll('#masterScheduleDays .schedule-day-btn.active').forEach(b => days.push(parseInt(b.dataset.day)));
        if (!days.length) { showToast('曜日を1つ以上選択してください', 'error'); return; }
        const st = document.getElementById('masterSchedStartTime').value, et = document.getElementById('masterSchedEndTime').value;
        if (!st || !et) { showToast('時間帯を入力してください', 'error'); return; }
        payload.days_of_week = days; payload.start_time = st; payload.end_time = et; payload.next_day = document.getElementById('masterSchedNextDay').checked;
    }
    try {
        const res = await fetch('/partner/devices/' + masterCurrentDeviceId + '/schedules', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.success) { showToast('スケジュールを追加しました', 'success'); hideModal('masterScheduleAddModal'); showDeviceDetail(masterCurrentDeviceId); }
        else showToast(data.message || '追加に失敗しました', 'error');
    } catch(e) { showToast('通信エラーが発生しました', 'error'); }
}

function masterConfirmDeleteSchedule(scheduleId) {
    masterDeleteScheduleId = scheduleId;
    showModal('masterScheduleDeleteModal');
}

async function masterExecuteDeleteSchedule() {
    if (!masterCurrentDeviceId || !masterDeleteScheduleId) return;
    try {
        const res = await fetch('/partner/devices/' + masterCurrentDeviceId + '/schedules/' + masterDeleteScheduleId, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (res.ok && data.success) { showToast('スケジュールを削除しました', 'success'); hideModal('masterScheduleDeleteModal'); showDeviceDetail(masterCurrentDeviceId); }
        else showToast(data.message || '削除に失敗しました', 'error');
    } catch(e) { showToast('通信エラーが発生しました', 'error'); }
}

// ===== デバイス削除 =====
function confirmDeleteDevice(deviceId) {
    if (!confirm('デバイス ' + deviceId + ' を削除しますか？\nこの操作は取り消せません。')) return;
    fetch('/partner/devices/' + deviceId, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(d.message || '削除に失敗しました', 'error');
    }).catch(() => showToast('通信エラー', 'error'));
}

// ===== 管理者アカウント =====
function toggleOrgSelect(rowId, role) {
    const row = document.getElementById(rowId);
    if (row) row.style.display = role === 'operator' ? '' : 'none';
}
function showAddAdminModal() { generatePassword('addAdminPassword'); toggleOrgSelect('addAdminOrgRow', 'operator'); document.getElementById('addAdminModal').classList.add('show'); }
function hideAddAdminModal() { document.getElementById('addAdminModal').classList.remove('show'); }
function showEditAdminModal(data) {
    document.getElementById('editAdminForm').action = '/partner/admin-users/' + data.id;
    document.getElementById('editAdminName').value = data.name;
    document.getElementById('editAdminEmail').value = data.email;
    document.getElementById('editAdminRole').value = data.role;
    document.getElementById('editAdminOrg').value = data.organization_id || '';
    document.getElementById('editAdminPassword').value = '';
    toggleOrgSelect('editAdminOrgRow', data.role);
    document.getElementById('editAdminModal').classList.add('show');
}
function hideEditAdminModal() { document.getElementById('editAdminModal').classList.remove('show'); }
function confirmDeleteAdmin(id, name) {
    if (confirm('「' + name + '」のアカウントを削除しますか？\nこの操作は取り消せません。')) {
        const form = document.getElementById('deleteAdminForm'); form.action = '/partner/admin-users/' + id; form.submit();
    }
}

// ===== 組織管理 =====
function showAddOrgModal() { document.getElementById('addOrgModal').classList.add('show'); }
function hideAddOrgModal() { document.getElementById('addOrgModal').classList.remove('show'); }
function showEditOrgModal(data) {
    document.getElementById('editOrgForm').action = '/partner/orgs/' + data.id;
    document.getElementById('editOrgName').value = data.name || '';
    document.getElementById('editOrgContactName').value = data.contact_name || '';
    document.getElementById('editOrgContactEmail').value = data.contact_email || '';
    document.getElementById('editOrgContactPhone').value = data.contact_phone || '';
    document.getElementById('editOrgAddress').value = data.address || '';
    document.getElementById('editOrgNotes').value = data.notes || '';
    document.getElementById('editOrgDeviceLimit').value = data.device_limit || 100;
    document.getElementById('editOrgExpiresAt').value = data.expires_at || '';
    document.getElementById('editOrgEmail1').value = data.notification_email_1 || '';
    document.getElementById('editOrgEmail2').value = data.notification_email_2 || '';
    document.getElementById('editOrgEmail3').value = data.notification_email_3 || '';
    document.getElementById('editOrgSms1').value = data.notification_sms_1 || '';
    document.getElementById('editOrgSms2').value = data.notification_sms_2 || '';
    document.getElementById('editOrgModal').classList.add('show');
}
function hideEditOrgModal() { document.getElementById('editOrgModal').classList.remove('show'); }
function confirmDeleteOrg(id, name) {
    if (confirm('「' + name + '」を削除しますか？\nこの操作は取り消せません。')) {
        const form = document.getElementById('deleteOrgForm'); form.action = '/partner/orgs/' + id; form.submit();
    }
}

async function toggleOrgPremium(orgId, enabled, checkbox) {
    try {
        const res = await fetch('/partner/orgs/' + orgId + '/toggle-premium', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ premium_enabled: enabled ? 1 : 0 })
        });
        const data = await res.json();
        if (data.success) { const label = document.querySelector('.org-premium-label-' + orgId); if (label) label.textContent = enabled ? '有効' : '無効'; }
        else checkbox.checked = !enabled;
    } catch(e) { checkbox.checked = !enabled; }
}
</script>
@endsection
