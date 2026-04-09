@extends('layouts.partner')

@section('title', 'みまもりト�EチE- マスター管琁E)

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
    .battery-low { color: #c62828 !important; font-weight: 600 !important; }
    .signal-weak { color: #e65100 !important; font-weight: 600 !important; }
    tr.row-inactive td.signal-weak { color: #e65100 !important; font-weight: 600 !important; }
    tr.row-inactive td.battery-low { color: #c62828 !important; font-weight: 600 !important; }
    .row-inactive td { color: var(--gray-400) !important; }
    .row-inactive .mono { color: var(--gray-400) !important; }
    .row-inactive .status-badge { opacity: 0.45; }
    .row-inactive .action-btn { opacity: 1; }
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
    /* 絁E��管琁E��ーブル */
    .org-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .org-table th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; white-space: nowrap; }
    .org-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; vertical-align: middle; }
    .org-table tr:hover td { background: #faf8f4; }
    .expires-warn { color: #c62828; font-weight: 600; }
    .expires-ok { color: #2e7d32; }
    .org-notify-icons { display: flex; gap: 6px; align-items: center; font-size: 11px; }
    /* パ�EトナーアカウンチE*/
    .partner-account-cell { font-size: 12px; }
    .partner-account-name { font-weight: 500; color: var(--gray-700); }
    .partner-account-email { color: var(--gray-500); font-size: 11px; }
    .partner-account-count { display: inline-block; padding: 1px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; background: var(--blue-light, #dbeafe); color: #2563eb; }
    /* チE��イス詳細モーダル */
    .modal-section { margin-bottom: 20px; }
    .modal-section-title { font-size: 13px; font-weight: 700; color: var(--gray-600); margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid var(--gray-200); }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    /* チE��イス詳細グリチE�� */
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
    .detail-notify-note { font-size: 11px; color: var(--gray-500); margin-top: 6px; line-height: 1.5; }
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
    /* パ�Eトナーアカウント管琁E��ーダル冁E��ーブル */
    .partner-user-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .partner-user-table th { text-align: left; padding: 8px 10px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 11px; white-space: nowrap; }
    .partner-user-table td { padding: 8px 10px; border-bottom: 1px solid #f0ebe1; vertical-align: middle; }
    .partner-user-table tr:hover td { background: #faf8f4; }
    /* 売上集訁E*/
    .sales-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .sales-card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .sales-card-label { font-size: 11px; color: #999; margin-bottom: 6px; }
    .sales-card-value { font-size: 26px; font-weight: 700; color: #5a5245; }
    .sales-card-sub { font-size: 11px; color: #aaa; margin-top: 4px; }
    .sales-card-diff { font-size: 12px; font-weight: 600; margin-top: 4px; }
    .sales-card-diff.up { color: #2e7d32; }
    .sales-card-diff.down { color: #c62828; }
    .sales-card-diff.flat { color: #888; }
    .sales-trend-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .sales-trend-table th { text-align: left; padding: 8px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; }
    .sales-trend-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; }
    .sales-trend-table tr:hover td { background: #faf8f4; }
    .sales-bar { display: inline-block; height: 10px; background: #5a5245; border-radius: 3px; vertical-align: middle; margin-right: 8px; min-width: 2px; }
    .sales-org-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .sales-org-table th { text-align: left; padding: 8px 12px; border-bottom: 2px solid #e0d8cc; font-weight: 500; color: #8b7e6a; font-size: 12px; }
    .sales-org-table td { padding: 10px 12px; border-bottom: 1px solid #f0ebe1; }
    .sales-org-table tr:hover td { background: #faf8f4; }
</style>
@endsection

@section('content')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">総デバイス数</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['active'] }}</div><div class="stat-label">稼働中</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['normal'] }}</div><div class="stat-label">正常</div></div>
    <div class="stat-card alert"><div class="stat-value">{{ $stats['alert'] }}</div><div class="stat-label">未検知アラーチE/div></div>
    <div class="stat-card offline"><div class="stat-value">{{ $stats['offline'] }}</div><div class="stat-label">通信途絶</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['inactive'] }}</div><div class="stat-label">未稼僁E/div></div>
</div>

@if(session('success')) <div class="flash-success">✁E{{ session('success') }}</div> @endif
@if(session('error')) <div class="flash-error">⚠ {{ session('error') }}</div> @endif

<div class="tab-bar">
    <button class="tab active" onclick="switchTab('devices', this)">チE��イス管琁E/button>
    <button class="tab" onclick="switchTab('admins', this)">管琁E��E��カウンチE/button>
    <button class="tab" onclick="switchTab('orgs', this)">パ�Eトナー管琁E/button>
    <button class="tab" onclick="switchTab('sales', this)">売上集訁E/button>
</div>

{{-- ===== チE��イス管琁E��チE===== --}}
<div id="tab-devices" class="tab-content active">
    <div class="card" id="issueSectionCard">
        <div class="card-title" style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">チE��イス発番</div>
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
                <button type="submit" class="btn btn-secondary">まとめて発番</button>
            </form>
        </div>
        @error('count') <div style="color:#c62828;font-size:12px;margin-top:8px;">{{ $message }}</div> @enderror
    </div>

    @if(session('issued'))
        @php $issued = session('issued'); @endphp
        <div class="issued-result">
            <div class="issued-title">✁EチE��イスを発番しました</div>
            <div class="issued-item"><span class="label">品番</span><span class="value" id="issued-id">{{ $issued['device_id'] }}</span><button class="issued-copy-btn" onclick="copyText('issued-id')">コピ�E</button></div>
            <div class="issued-item"><span class="label">初期PIN</span><span class="value" id="issued-pin">{{ $issued['pin'] }}</span><button class="issued-copy-btn" onclick="copyText('issued-pin')">コピ�E</button></div>
        </div>
    @endif

    @if(session('issued_bulk'))
        @php $bulkList = session('issued_bulk'); @endphp
        <div class="issued-result">
            <div class="issued-title">✁E{{ count($bulkList) }}台のチE��イスを発番しました</div>
            @foreach($bulkList as $i => $item)
                <div class="issued-item"><span class="label">{{ $i + 1 }}.</span><span class="value">{{ $item['device_id'] }}</span><span style="color:#666;margin:0 8px;">/</span><span class="value">{{ $item['pin'] }}</span></div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">チE��イス一覧</div>
        <form method="GET" action="/partner" class="filter-bar">
            <input type="hidden" name="tab" value="devices">
            <input type="text" name="search" class="filter-input" placeholder="品番・表示名で検索" value="{{ request('search') }}">
            <select name="status" class="filter-select">
                <option value="">全スチE�Eタス</option>
                <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>正常</option>
                <option value="alert" {{ request('status') === 'alert' ? 'selected' : '' }}>未検知アラーチE/option>
                <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>通信途絶</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>未稼僁E/option>
            </select>
            <select name="org" class="filter-select">
                <option value="">全絁E��E/option>
                <option value="none" {{ request('org') === 'none' ? 'selected' : '' }}>絁E��未所屁E/option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ request('org') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-secondary">絞り込む</button>
        </form>
        <table class="device-table">
            <thead>
                <tr><th>品番</th><th>表示吁E/th><th>スチE�Eタス</th><th>絁E��E/th><th>電池残量</th><th>電波強度</th><th>最終受信</th><th>最終検知</th><th>操佁E/th></tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr class="{{ !$device->notification_service_enabled ? 'row-inactive' : '' }}">
                        <td class="device-id-cell">{{ $device->device_id }}</td>
                        <td>{{ $device->nickname ?: '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ $device->status }}">
                                @switch($device->status)
                                    @case('normal') 正常 @break
                                    @case('warning') 注愁E@break
                                    @case('alert') 未検知アラーチE@break
                                    @case('offline') 通信途絶 @break
                                    @case('inactive') 未稼僁E@break
                                @endswitch
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--gray-600);">{{ $device->organization ? $device->organization->name : '-' }}</td>
                        <td class="battery-cell {{ $device->battery_pct && $device->battery_pct < 20 ? 'battery-low' : '' }}" style="font-size:12px;">{{ $device->battery_pct ? $device->battery_pct . '%' : '-' }}</td>
                        <td class="{{ $device->rssi !== null && $device->rssi <= -85 ? 'signal-weak' : '' }}" style="font-size:12px;">
                            @if($device->rssi !== null)
                                @if($device->rssi > -70) 良好
                                @elseif($device->rssi > -85) 普送E                                @else 弱ぁE                                @endif
                                ({{ $device->rssi }}dBm)
                            @else
                                -
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $device->last_received_at ? $device->last_received_at->format('m/d H:i') : '-' }}</td>
                        <td style="font-size:12px;">{{ $device->last_human_detected_at ? $device->last_human_detected_at->format('m/d H:i') : '-' }}</td>
                        <td><button class="action-btn" onclick="showDeviceDetail('{{ $device->device_id }}')">詳細</button><a class="action-btn" href="/partner/devices/{{ $device->device_id }}/logs">ログ</a><button class="action-btn danger" onclick="confirmDeleteDevice('{{ $device->device_id }}')">削除</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">
                            チE��イスがありません、Ebr>
                            <button class="btn btn-sm btn-primary" style="margin-top:10px;" onclick="scrollToIssueSection()">チE��イスを発番する</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($devices->hasPages())
            <div class="pagination-wrap">
                @if($devices->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $devices->previousPageUrl() }}">‹</a> @endif
                @foreach($devices->getUrlRange(1, $devices->lastPage()) as $page => $url)
                    @if($page == $devices->currentPage()) <span class="active">{{ $page }}</span> @else <a href="{{ $url }}">{{ $page }}</a> @endif
                @endforeach
                @if($devices->hasMorePages()) <a href="{{ $devices->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
            </div>
        @endif
    </div>
</div>

{{-- ===== 管琁E��E��カウントタブ！Easterのみ�E�E===== --}}
<div id="tab-admins" class="tab-content">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:600;color:#5a5245;">管琁E��E��カウント一覧</div>
            <div class="toolbar-right"><button class="btn btn-sm btn-primary" onclick="showAddAdminModal()">�E�E管琁E��E��加</button></div>
        </div>
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>名前</th><th>メールアドレス</th><th>最終ログイン</th><th>作�E日</th><th>操佁E/th></tr>
            </thead>
            <tbody>
                @forelse($adminUsers as $admin)
                    <tr>
                        <td style="font-size:12px;color:#999;">{{ $admin->id }}</td>
                        <td style="font-weight:500;">{{ $admin->name }}</td>
                        <td style="font-size:13px;">{{ $admin->email }}</td>
                        <td style="font-size:12px;color:#888;">{{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->format('Y/m/d H:i') : '未ログイン' }}</td>
                        <td style="font-size:12px;color:#888;">{{ $admin->created_at->format('Y/m/d') }}</td>
                        <td>
                            <button class="action-btn" onclick="showEditAdminModal({{ json_encode(['id' => $admin->id, 'name' => $admin->name, 'email' => $admin->email]) }})">編雁E/button>
                            @if($admin->id !== Auth::guard('partner')->id())
                                <button class="action-btn danger" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ $admin->name }}')">削除</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">管琁E��E��カウントがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== 絁E��管琁E��チE===== --}}
<div id="tab-orgs" class="tab-content">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:600;color:#5a5245;">パ�Eトナー一覧</div>
            <button class="btn btn-sm btn-primary" onclick="showAddOrgModal()">�E�Eパ�Eトナー登録</button>
        </div>
        <table class="org-table">
            <thead>
                <tr><th>絁E��名</th><th>拁E��老E/th><th>連絡允E/th><th>チE��イス数</th><th>パ�EトナーアカウンチE/th><th>通知</th><th>操佁E/th></tr>
            </thead>
            <tbody>
                @forelse($organizations as $org)
                    @php
                        $hasEmail = $org->notification_email_1 || $org->notification_email_2 || $org->notification_email_3;
                        $hasSms = $org->notification_sms_1 || $org->notification_sms_2;
                        $partnerUsers = $org->partnerUsers ?? collect();
                    @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $org->name }}</td>
                        <td style="font-size:12px;">{{ $org->contact_name ?: '-' }}</td>
                        <td style="font-size:12px;">{{ $org->contact_email }}</td>
                        <td style="font-size:13px;"><a href="/partner?tab=devices&search=&status=&org={{ $org->id }}" style="color:#2563eb;text-decoration:underline;font-weight:600;">{{ $org->devices_count }}台</a></td>
                        <td class="partner-account-cell">
                            @if($partnerUsers->count() > 0)
                                @foreach($partnerUsers as $pu)
                                    <div><span class="partner-account-name">{{ $pu->name }}</span><br><span class="partner-account-email">{{ $pu->email }}</span></div>
                                @endforeach
                            @else
                                <span style="color:var(--gray-400);font-size:11px;">未設宁E/span>
                            @endif
                        </td>
                        <td>
                            <div class="org-notify-icons">
                                @if($hasEmail) <span title="メール" style="{{ $org->notification_enabled ? '' : 'opacity:0.4;' }}">✁E/span> @endif
                                @if($hasSms) <span title="SMS" style="{{ $org->notification_sms_enabled ? '' : 'opacity:0.4;' }}">📱</span> @endif
                                @if(!$hasEmail && !$hasSms) <span style="color:var(--gray-300);font-size:11px;">未設宁E/span> @endif
                            </div>
                        </td>
                        <td>
                            <button class="action-btn" onclick="showEditOrgModal({{ json_encode(['id'=>$org->id,'name'=>$org->name,'contact_name'=>$org->contact_name,'contact_email'=>$org->contact_email,'contact_phone'=>$org->contact_phone,'address'=>$org->address,'notes'=>$org->notes,'notification_email_1'=>$org->notification_email_1,'notification_email_2'=>$org->notification_email_2,'notification_email_3'=>$org->notification_email_3,'notification_sms_1'=>$org->notification_sms_1,'notification_sms_2'=>$org->notification_sms_2]) }})">編雁E/button>
                            <button class="action-btn" onclick="showOrgAccountsModal({{ $org->id }}, '{{ addslashes($org->name) }}')">アカウンチE/button>
                            @if($org->devices_count === 0) <button class="action-btn danger" onclick="confirmDeleteOrg({{ $org->id }}, '{{ $org->name }}')">削除</button> @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">絁E��がありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== 売上集計タチE===== --}}
<div id="tab-sales" class="tab-content">
    @php
        $diff = $salesData['this_month'] - $salesData['last_month'];
        $diffSign = $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'flat');
        $diffLabel = $diff > 0 ? '▲ ¥' . number_format($diff) : ($diff < 0 ? '▼ ¥' . number_format(abs($diff)) : '±0');
        $maxMonthly = $salesData['monthly']->max('total') ?: 1;
    @endphp
    <div class="sales-grid">
        <div class="sales-card">
            <div class="sales-card-label">累計売上（�E期間�E�E/div>
            <div class="sales-card-value">¥{{ number_format($salesData['total_all']) }}</div>
            <div class="sales-card-sub">{{ $salesData['count_all'] }}件</div>
        </div>
        <div class="sales-card">
            <div class="sales-card-label">今月の売丁E/div>
            <div class="sales-card-value">¥{{ number_format($salesData['this_month']) }}</div>
            <div class="sales-card-sub">{{ now()->format('Y年n朁E) }} / {{ $salesData['count_this'] }}件</div>
            <div class="sales-card-diff {{ $diffSign }}">{{ $diffLabel }} 先月毁E/div>
        </div>
        <div class="sales-card">
            <div class="sales-card-label">先月の売丁E/div>
            <div class="sales-card-value">¥{{ number_format($salesData['last_month']) }}</div>
            <div class="sales-card-sub">{{ now()->subMonth()->format('Y年n朁E) }}</div>
        </div>
        <div class="sales-card">
            <div class="sales-card-label">稼働デバイス数</div>
            <div class="sales-card-value">{{ $stats['active'] }}台</div>
            <div class="sales-card-sub">全{{ $stats['total'] }}台中</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:16px;">月別推移�E�直迁Eヶ月！E/div>
        @if($salesData['monthly']->isEmpty())
            <p style="text-align:center;color:#aaa;padding:24px 0;">課金データがありません</p>
        @else
            <table class="sales-trend-table">
                <thead>
                    <tr><th>朁E/th><th>売丁E/th><th>件数</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($salesData['monthly'] as $row)
                        @php $barWidth = $maxMonthly > 0 ? round($row->total / $maxMonthly * 160) : 0; @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('Y年n朁E) }}</td>
                            <td style="font-weight:600;">¥{{ number_format($row->total) }}</td>
                            <td style="color:#888;">{{ $row->count }}件</td>
                            <td><span class="sales-bar" style="width:{{ $barWidth }}px;"></span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <div style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:16px;">パ�Eトナー別冁E���E�今月�E�E/div>
        @if($salesData['by_org']->isEmpty())
            <p style="text-align:center;color:#aaa;padding:24px 0;">今月の課金データがありません</p>
        @else
            <table class="sales-org-table">
                <thead><tr><th>パ�Eトナー吁E/th><th>売丁E/th><th>件数</th></tr></thead>
                <tbody>
                    @foreach($salesData['by_org'] as $row)
                        <tr>
                            <td style="font-weight:500;">{{ $row->org_name }}</td>
                            <td style="font-weight:600;">¥{{ number_format($row->total) }}</td>
                            <td style="color:#888;">{{ $row->count }}件</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ===== チE��イス詳細モーダル ===== --}}
<div id="deviceDetailModal" class="modal-overlay" onclick="if(event.target===this)hideModal('deviceDetailModal')">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>🔍 チE��イス詳細</h3><button class="modal-close" onclick="hideModal('deviceDetailModal')">✁E/button></div>
        <div class="modal-body">
            <div class="detail-status-row">
                <div class="detail-status-badge normal" id="masterDetailStatusBadge">-</div>
                <button class="detail-clear-alert-btn" id="masterDetailClearAlertBtn" style="display:none;" onclick="masterClearAlert()">✁E警告を解除して退去処琁E/button>
            </div>
            <div class="modal-section" style="margin-bottom:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <label class="watch-toggle"><input type="checkbox" id="masterDetailNotifyEnabled" checked onchange="masterToggleNotifyService(this.checked)"><span class="watch-slider"></span></label>
                        <span style="font-size:13px;font-weight:600;color:var(--gray-700);">📢 通知サービス有効</span>
                        <span id="masterDetailNotifyLabel" style="font-size:12px;color:var(--gray-500);">有効</span>
                    </div>
                    <button class="btn btn-sm btn-secondary" onclick="masterShowSubscriptionModal()">🔍 通知設宁E/button>
                </div>
                <p class="detail-notify-note">OFFにすると未検知チェチE��が停止し、E��知が送られなくなります、E/p>
            </div>
            <div class="modal-section">
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">チE��イスID</p><p class="detail-item-value mono" id="masterDetailDeviceId">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">最終検知</p><p class="detail-item-value" id="masterDetailLastDetected">-</p></div>
                    {{-- 絁E��割当セレクチE--}}
                    <div class="detail-item" style="grid-column: span 2;">
                        <p class="detail-item-label">🏢 絁E��割彁E/p>
                        <select class="detail-form-input" id="masterDetailOrgId">
                            <option value="">未割彁E/option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">部屋番号</p><input type="text" class="detail-form-input" id="masterDetailRoom" placeholder="101"></div>
                    <div class="detail-item"><p class="detail-item-label">入屁E��E��</p><input type="text" class="detail-form-input" id="masterDetailTenant" placeholder="山田 太郁E></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">📡 センサー状慁E/div>
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">電池残量(%)</p><p class="detail-item-value" id="masterDetailBattery">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">電波強度</p><p class="detail-item-value" id="masterDetailSignal">-</p></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">⚁E設宁E/div>
                <div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">アラート閾値</p>
                        <select class="detail-form-input" id="masterDetailAlertHours">
                            <option value="12">12時間</option><option value="24">24時間</option><option value="36">36時間</option><option value="48">48時間</option><option value="72">72時間</option>
                        </select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">設置高さ</p>
                        <div style="display:flex;align-items:center;gap:4px;"><input type="number" class="detail-form-input" id="masterDetailHeight" min="100" max="300" style="width:70px;"><span style="font-size:12px;color:var(--gray-500);">cm</span></div>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">ペット除夁E/p>
                        <select class="detail-form-input" id="masterDetailPetExclusion"><option value="0">OFF</option><option value="1">ON</option></select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">外�EモーチE/p>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <label class="watch-toggle"><input type="checkbox" id="masterDetailAwayMode" onchange="masterToggleAwayMode(this.checked)"><span class="watch-slider"></span></label>
                            <span id="masterDetailAwayLabel" style="font-size:12px;color:var(--gray-600);">OFF</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">🔧 端末惁E��</div>
                <div class="detail-grid">
                    <div class="detail-item" style="grid-column: span 2;">
                        <p class="detail-item-label">📶 SIM ID�E�ECCID�E�E/p>
                        <input type="text" class="detail-form-input" id="masterDetailSimId" placeholder="侁E 09882806660000123456" maxlength="22" style="font-family:monospace;letter-spacing:1px;" inputmode="numeric">
                        <p style="font-size:11px;color:var(--gray-500);margin-top:4px;">1NCE管琁E��面のICCIDを�E力、E2桁以冁E�E数字。デバイスからのJSONにSIM IDが含まれる場合�E自動設定されます、E/p>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">登録日</p><p class="detail-item-value" id="masterDetailRegistered">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">メモ</p><input type="text" class="detail-form-input" id="masterDetailMemo" placeholder="メモを�E劁E.." maxlength="200"></div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <p class="detail-item-label">💳 課金開始日</p>
                        <input type="date" class="detail-form-input" id="masterDetailBillingStartDate" style="max-width:180px;">
                        <p style="font-size:11px;color:var(--gray-500);margin-top:4px;">月締め請求�E開始日を指定。pay.jp の定期課金に使用予定、E/p>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title">📅 外�Eスケジュール</div>
                <div id="masterDetailScheduleList"></div>
                <button class="detail-schedule-add" onclick="masterOpenScheduleAdd()">�E�Eスケジュール追加</button>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('deviceDetailModal')">閉じめE/button>
            <button class="btn btn-primary" onclick="masterSaveAssignment()">保孁E/button>
        </div>
    </div>
</div>

{{-- ===== 通知設定モーダル ===== --}}
<div id="masterSubscriptionModal" class="modal-overlay" onclick="if(event.target===this)hideModal('masterSubscriptionModal')">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>🔍 通知設宁E/h3><button class="modal-close" onclick="hideModal('masterSubscriptionModal')">✁E/button></div>
        <div class="modal-body">
            <div style="font-size:12px;color:var(--gray-500);margin-bottom:16px;">対象チE��イス: <span id="masterSubModalDeviceId" class="mono" style="font-size:12px;"></span></div>
            <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;margin-bottom:10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <p style="font-size:13px;font-weight:600;color:var(--gray-700);">📱 SMS通知 <span style="font-size:11px;font-weight:400;color:var(--gray-500);">+税込100冁E台/朁E/span></p>
                    <label class="watch-toggle"><input type="checkbox" id="masterDetailSmsEnabled"><span class="watch-slider"></span></label>
                </div>
                <input type="tel" class="detail-form-input" id="masterDetailSmsPhone1" placeholder="09012345678" style="margin-bottom:6px;">
                <input type="tel" class="detail-form-input" id="masterDetailSmsPhone2" placeholder="09012345678�E�任意！E>
            </div>
            <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <p style="font-size:13px;font-weight:600;color:var(--gray-700);">📞 自動音声電話 <span style="font-size:11px;font-weight:400;color:var(--gray-500);">+税込300冁E台/朁E/span></p>
                    <label class="watch-toggle"><input type="checkbox" id="masterDetailVoiceEnabled"><span class="watch-slider"></span></label>
                </div>
                <input type="tel" class="detail-form-input" id="masterDetailVoicePhone1" placeholder="09012345678" style="margin-bottom:6px;">
                <input type="tel" class="detail-form-input" id="masterDetailVoicePhone2" placeholder="09012345678�E�任意！E>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('masterSubscriptionModal'); showDeviceDetail(masterCurrentDeviceId)">戻めE/button>
            <button class="btn btn-primary" onclick="masterSaveNotification(); hideModal('masterSubscriptionModal'); showDeviceDetail(masterCurrentDeviceId)">保孁E/button>
        </div>
    </div>
</div>

{{-- ===== スケジュール追加モーダル ===== --}}
<div id="masterScheduleAddModal" class="modal-overlay" onclick="if(event.target===this)hideModal('masterScheduleAddModal')">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header"><h3>📅 スケジュール追加</h3><button class="modal-close" onclick="hideModal('masterScheduleAddModal')">✁E/button></div>
        <div class="modal-body">
            <div class="schedule-type-tabs">
                <button class="schedule-type-tab active" id="masterTabOneshot" onclick="masterSwitchScheduleType('oneshot')">📅 単発</button>
                <button class="schedule-type-tab" id="masterTabRecurring" onclick="masterSwitchScheduleType('recurring')">🔄 定期</button>
            </div>
            <div id="masterOneshotForm">
                <div class="schedule-form-group"><label>開始日晁E/label><input type="datetime-local" id="masterSchedStartAt"></div>
                <div class="schedule-form-group"><label>終亁E��時（省略可・未入力�E無期限�E�E/label><input type="datetime-local" id="masterSchedEndAt"></div>
            </div>
            <div id="masterRecurringForm" style="display:none;">
                <div class="schedule-form-group"><label>曜日</label>
                    <div class="schedule-days" id="masterScheduleDays">
                        <button type="button" class="schedule-day-btn" data-day="0" onclick="toggleDay(this)">日</button>
                        <button type="button" class="schedule-day-btn" data-day="1" onclick="toggleDay(this)">朁E/button>
                        <button type="button" class="schedule-day-btn" data-day="2" onclick="toggleDay(this)">火</button>
                        <button type="button" class="schedule-day-btn" data-day="3" onclick="toggleDay(this)">水</button>
                        <button type="button" class="schedule-day-btn" data-day="4" onclick="toggleDay(this)">木</button>
                        <button type="button" class="schedule-day-btn" data-day="5" onclick="toggleDay(this)">釁E/button>
                        <button type="button" class="schedule-day-btn" data-day="6" onclick="toggleDay(this)">圁E/button>
                    </div>
                </div>
                <div class="schedule-form-group"><label>時間帯</label>
                    <div class="schedule-time-row"><input type="time" id="masterSchedStartTime"><span>、E/span><input type="time" id="masterSchedEndTime"></div>
                    <label class="schedule-nextday-check"><input type="checkbox" id="masterSchedNextDay"> 翌日にまたがめE/label>
                </div>
            </div>
            <div class="schedule-form-group"><label>メモ�E�任意！E/label><input type="text" id="masterSchedMemo" placeholder="侁E 外�E・旁E��E maxlength="200"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('masterScheduleAddModal')">キャンセル</button>
            <button class="btn btn-primary" onclick="masterSubmitSchedule()">追加</button>
        </div>
    </div>
</div>

{{-- ===== スケジュール削除確認モーダル ===== --}}
<div id="masterScheduleDeleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('masterScheduleDeleteModal')">
    <div class="modal"><div class="modal-header"><h3>⚠ スケジュール削除</h3><button class="modal-close" onclick="hideModal('masterScheduleDeleteModal')">✁E/button></div>
        <div class="modal-body"><p>こ�Eスケジュールを削除しますか�E�E/p></div>
        <div class="modal-footer"><button class="btn btn-secondary" onclick="hideModal('masterScheduleDeleteModal')">キャンセル</button><button class="btn btn-danger" onclick="masterExecuteDeleteSchedule()">削除する</button></div>
    </div>
</div>

{{-- ===== 管琁E��E��加モーダル�E�Easterのみ�E�E===== --}}
<div id="addAdminModal" class="modal-overlay" onclick="if(event.target===this)hideAddAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>管琁E��E��カウント追加</h3><button class="modal-close" onclick="hideAddAdminModal()">✁E/button></div>
        <form method="POST" action="{{ route('partner.admin-users.store') }}">
            @csrf
            <input type="hidden" name="role" value="master">
            <div class="modal-body">
                <div class="form-group"><label class="form-label">名前 *</label><input type="text" name="name" class="form-input" placeholder="侁E 山田 太郁E required></div>
                <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" name="email" class="form-input" placeholder="admin@example.com" required><p class="form-hint">こ�EメールアドレスでログインしまぁE/p></div>
                <div class="form-group">
                    <label class="form-label">初期パスワーチE*</label>
                    <div class="password-field">
                        <input type="text" name="password" id="addAdminPassword" class="form-input" required>
                        <button type="button" class="password-generate-btn" onclick="generatePassword('addAdminPassword')">生�E</button>
                    </div>
                    <p class="form-hint">ログイン後に変更するよう案�Eしてください</p>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideAddAdminModal()">キャンセル</button><button type="submit" class="btn btn-primary">作�E</button></div>
        </form>
    </div>
</div>

{{-- ===== 管琁E��E��雁E��ーダル�E�Easterのみ�E�E===== --}}
<div id="editAdminModal" class="modal-overlay" onclick="if(event.target===this)hideEditAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>管琁E��E��カウント編雁E/h3><button class="modal-close" onclick="hideEditAdminModal()">✁E/button></div>
        <form method="POST" id="editAdminForm" action="">
            @csrf @method('PUT')
            <input type="hidden" name="role" value="master">
            <div class="modal-body">
                <div class="form-group"><label class="form-label">名前 *</label><input type="text" name="name" id="editAdminName" class="form-input" required></div>
                <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" name="email" id="editAdminEmail" class="form-input" required></div>
                <div class="form-group">
                    <label class="form-label">新しいパスワーチE/label>
                    <div class="password-field">
                        <input type="text" name="password" id="editAdminPassword" class="form-input" placeholder="変更しなぁE��合�E空欁E>
                        <button type="button" class="password-generate-btn" onclick="generatePassword('editAdminPassword')">生�E</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditAdminModal()">キャンセル</button><button type="submit" class="btn btn-primary">保孁E/button></div>
        </form>
    </div>
</div>

<form id="deleteAdminForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>

{{-- ===== 絁E��追加モーダル ===== --}}
<div id="addOrgModal" class="modal-overlay" onclick="if(event.target===this)hideAddOrgModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>パ�Eトナー登録</h3><button class="modal-close" onclick="hideAddOrgModal()">✁E/button></div>
        <form method="POST" action="{{ route('partner.orgs.store') }}">
            @csrf
            <div class="modal-body">
                @if($errors->hasAny(['partner_email', 'partner_password', 'name']))
                    <div class="flash-error" style="margin-bottom:12px;">
                        @foreach(['partner_email', 'partner_password', 'name'] as $field)
                            @if($errors->has($field))
                                <div>⚠ {{ $errors->first($field) }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
                <div class="modal-section">
                    <div class="modal-section-title">パ�EトナーアカウンチE/div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">メールアドレス</label><input type="email" name="partner_email" class="form-input" placeholder="partner@example.com"></div>
                        <div class="form-group">
                            <label class="form-label">パスワーチE/label>
                            <div class="password-field">
                                <input type="text" name="partner_password" id="addOrgPartnerPassword" class="form-input">
                                <button type="button" class="password-generate-btn" onclick="generatePassword('addOrgPartnerPassword')">生�E</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-section"><div class="modal-section-title">基本惁E��</div>
                    <div class="form-group"><label class="form-label">絁E��名 *</label><input type="text" name="name" class="form-input" required></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">拁E��老E��</label><input type="text" name="contact_name" class="form-input"></div>
                        <div class="form-group"><label class="form-label">連絡先メール</label><input type="email" name="contact_email" class="form-input"></div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">連絡先電話番号</label><input type="text" name="contact_phone" class="form-input"></div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-group"><label class="form-label">住所</label><input type="text" name="address" class="form-input"></div>
                    <div class="form-group"><label class="form-label">メモ</label><textarea name="notes" class="form-input" rows="2" style="resize:vertical;"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideAddOrgModal()">キャンセル</button><button type="submit" class="btn btn-primary">作�E</button></div>
        </form>
    </div>
</div>

{{-- ===== 絁E��編雁E��ーダル ===== --}}
<div id="editOrgModal" class="modal-overlay" onclick="if(event.target===this)hideEditOrgModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>パ�Eトナー編雁E/h3><button class="modal-close" onclick="hideEditOrgModal()">✁E/button></div>
        <form method="POST" id="editOrgForm" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="modal-section"><div class="modal-section-title">基本惁E��</div>
                    <div class="form-group"><label class="form-label">絁E��名 *</label><input type="text" name="name" id="editOrgName" class="form-input" required></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">拁E��老E��</label><input type="text" name="contact_name" id="editOrgContactName" class="form-input"></div>
                        <div class="form-group"><label class="form-label">連絡先メール *</label><input type="email" name="contact_email" id="editOrgContactEmail" class="form-input" required></div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">連絡先電話番号</label><input type="text" name="contact_phone" id="editOrgContactPhone" class="form-input"></div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-group"><label class="form-label">住所</label><input type="text" name="address" id="editOrgAddress" class="form-input"></div>
                    <div class="form-group"><label class="form-label">メモ</label><textarea name="notes" id="editOrgNotes" class="form-input" rows="2" style="resize:vertical;"></textarea></div>
                </div>
                <div class="modal-section"><div class="modal-section-title">通知設定（アラート発生時に絁E��宛に送信�E�E/div>
                    <p style="font-size:12px;color:var(--gray-500);margin-bottom:12px;">設定したメール・SMSにアラートを転送します、E/p>
                    <div class="form-group"><label class="form-label">通知メール 1</label><input type="email" name="notification_email_1" id="editOrgEmail1" class="form-input"></div>
                    <div class="form-group"><label class="form-label">通知メール 2</label><input type="email" name="notification_email_2" id="editOrgEmail2" class="form-input"></div>
                    <div class="form-group"><label class="form-label">通知メール 3</label><input type="email" name="notification_email_3" id="editOrgEmail3" class="form-input"></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">SMS通知允E1</label><input type="text" name="notification_sms_1" id="editOrgSms1" class="form-input"></div>
                        <div class="form-group"><label class="form-label">SMS通知允E2</label><input type="text" name="notification_sms_2" id="editOrgSms2" class="form-input"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditOrgModal()">キャンセル</button><button type="submit" class="btn btn-primary">保孁E/button></div>
        </form>
    </div>
</div>

<form id="deleteOrgForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>

{{-- ===== パ�Eトナーアカウント管琁E��ーダル�E�一覧のみ�E�E===== --}}
<div id="orgAccountsModal" class="modal-overlay" onclick="if(event.target===this)hideModal('orgAccountsModal')">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>👤 パ�Eトナーアカウント管琁E/h3><button class="modal-close" onclick="hideModal('orgAccountsModal')">✁E/button></div>
        <div class="modal-body">
            <div style="font-size:13px;color:var(--gray-600);margin-bottom:16px;">絁E��E <strong id="orgAccountsOrgName"></strong></div>
            <div id="orgAccountsTable"><p style="text-align:center;color:var(--gray-400);padding:20px;">読み込み中...</p></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('orgAccountsModal')">閉じめE/button>
        </div>
    </div>
</div>

{{-- ===== パ�Eトナーアカウント編雁E��ーダル ===== --}}
<div id="orgEditUserModal" class="modal-overlay" onclick="if(event.target===this)hideModal('orgEditUserModal')">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header"><h3>パ�Eトナーアカウント編雁E/h3><button class="modal-close" onclick="hideModal('orgEditUserModal')">✁E/button></div>
        <div class="modal-body">
            <input type="hidden" id="orgEditUserId">
            <div class="form-group"><label class="form-label">名前 *</label><input type="text" id="orgEditUserName" class="form-input"></div>
            <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" id="orgEditUserEmail" class="form-input"></div>
            <div class="form-group">
                <label class="form-label">新しいパスワーチE/label>
                <div class="password-field">
                    <input type="text" id="orgEditUserPassword" class="form-input" placeholder="変更しなぁE��合�E空欁E>
                    <button type="button" class="password-generate-btn" onclick="generatePassword('orgEditUserPassword')">生�E</button>
                </div>
            </div>
            <div id="orgEditUserError" style="display:none;color:#c62828;font-size:12px;margin-top:4px;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('orgEditUserModal')">キャンセル</button>
            <button class="btn btn-primary" onclick="submitOrgEditUser()">保孁E/button>
        </div>
    </div>
</div>

{{-- ===== パスワードリセチE��モーダル ===== --}}
<div id="orgResetPasswordModal" class="modal-overlay" onclick="if(event.target===this)hideModal('orgResetPasswordModal')">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header"><h3>🔑 パスワードリセチE��</h3><button class="modal-close" onclick="hideModal('orgResetPasswordModal')">✁E/button></div>
        <div class="modal-body">
            <div style="font-size:13px;color:var(--gray-600);margin-bottom:16px;">対象: <strong id="orgResetPasswordName"></strong></div>
            <input type="hidden" id="orgResetPasswordUserId">
            <div class="form-group">
                <label class="form-label">新しいパスワーチE*</label>
                <div class="password-field">
                    <input type="text" id="orgResetPasswordValue" class="form-input">
                    <button type="button" class="password-generate-btn" onclick="generatePassword('orgResetPasswordValue')">生�E</button>
                </div>
            </div>
            <div id="orgResetPasswordError" style="display:none;color:#c62828;font-size:12px;margin-top:4px;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('orgResetPasswordModal')">キャンセル</button>
            <button class="btn btn-primary" onclick="submitOrgResetPassword()">リセチE��</button>
        </div>
    </div>
</div>

<div id="toast" class="toast"></div>

@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let masterCurrentDeviceId = null;
let masterScheduleType = 'oneshot';
let masterDeleteScheduleId = null;
let orgAccountsCurrentOrgId = null;

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
    else if (tab === 'sales') switchTab('sales', document.querySelectorAll('.tab')[3]);

    @if($errors->hasAny(['partner_email', 'partner_password', 'name']))
        switchTab('orgs', document.querySelectorAll('.tab')[2]);
        showAddOrgModal();
    @endif
});

function copyText(id) {
    navigator.clipboard.writeText(document.getElementById(id).textContent).then(() => {
        const btn = event.target; btn.textContent = 'コピ�E渁E; setTimeout(() => { btn.textContent = 'コピ�E'; }, 1500);
    });
}

function generatePassword(inputId) {
    const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let pw = ''; for (let i = 0; i < 12; i++) pw += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById(inputId).value = pw;
}

// ===== チE��イス詳細 =====
async function showDeviceDetail(deviceId) {
    masterCurrentDeviceId = deviceId;
    showModal('deviceDetailModal');
    try {
        const res = await fetch('/partner/devices/' + deviceId + '/detail', { headers: { 'Accept': 'application/json' } });
        const d = await res.json();
        const statusLabels = { normal: '正常・稼働中', warning: '注愁E, alert: '未検知アラーチE⚠', offline: '通信途絶', inactive: '未稼僁E };
        const badge = document.getElementById('masterDetailStatusBadge');
        badge.textContent = statusLabels[d.status] || d.status;
        badge.className = 'detail-status-badge ' + (d.status || 'inactive');
        document.getElementById('masterDetailClearAlertBtn').style.display = d.status === 'alert' ? 'inline-flex' : 'none';
        const notifyEnabled = d.notification_service_enabled !== false;
        document.getElementById('masterDetailNotifyEnabled').checked = notifyEnabled;
        document.getElementById('masterDetailNotifyLabel').textContent = notifyEnabled ? '有効' : '停止中';
        document.getElementById('masterDetailDeviceId').textContent = d.device_id;
        document.getElementById('masterDetailLastDetected').textContent = d.last_human_detected_at || '-';
        // 絁E��割当セレクトをセチE��
        document.getElementById('masterDetailOrgId').value = d.organization_id || '';
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
        document.getElementById('masterDetailAwayLabel').textContent = d.away_mode ? ('ON' + (d.away_until ? ' (' + d.away_until + 'まで)' : '')) : 'OFF';
        document.getElementById('masterDetailSimId').value = d.sim_id || '';
        document.getElementById('masterDetailRegistered').textContent = d.registered_at || '-';
        document.getElementById('masterDetailMemo').value = d.memo || '';
        const now = new Date();
        const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        document.getElementById('masterDetailBillingStartDate').value = d.billing_start_date || nextMonth.toISOString().split('T')[0];
        document.getElementById('masterDetailSmsEnabled').checked = d.sms_enabled || false;
        document.getElementById('masterDetailSmsPhone1').value = d.sms_phone_1 || '';
        document.getElementById('masterDetailSmsPhone2').value = d.sms_phone_2 || '';
        document.getElementById('masterDetailVoiceEnabled').checked = d.voice_enabled || false;
        document.getElementById('masterDetailVoicePhone1').value = d.voice_phone_1 || '';
        document.getElementById('masterDetailVoicePhone2').value = d.voice_phone_2 || '';
        masterRenderSchedules(d.schedules || []);
    } catch(e) { showToast('詳細の取得に失敗しました', 'error'); }
}

function masterShowSubscriptionModal() {
    if (!masterCurrentDeviceId) return;
    hideModal('deviceDetailModal');
    document.getElementById('masterSubModalDeviceId').textContent = masterCurrentDeviceId;
    showModal('masterSubscriptionModal');
}

function masterToggleNotifyService(enabled) {
    document.getElementById('masterDetailNotifyLabel').textContent = enabled ? '有効' : '停止中';
    fetch('/partner/devices/' + masterCurrentDeviceId + '/toggle-notify', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ enabled: enabled ? 1 : 0 })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            document.querySelectorAll('.device-table tbody tr').forEach(row => {
                const idCell = row.querySelector('.device-id-cell');
                if (idCell && idCell.textContent.trim() === masterCurrentDeviceId) {
                    enabled ? row.classList.remove('row-inactive') : row.classList.add('row-inactive');
                }
            });
        }
        else { showToast(d.message || 'エラー', 'error'); document.getElementById('masterDetailNotifyEnabled').checked = !enabled; document.getElementById('masterDetailNotifyLabel').textContent = !enabled ? '有効' : '停止中'; }
    }).catch(() => showToast('通信エラー', 'error'));
}

async function masterSaveAssignment() {
    if (!masterCurrentDeviceId) return;
    const orgIdVal = document.getElementById('masterDetailOrgId').value;
    const payload = {
        organization_id: orgIdVal ? parseInt(orgIdVal) : null,
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
    try {
        const res = await fetch('/partner/devices/' + masterCurrentDeviceId + '/notification', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) showToast('通知設定を保存しました', 'success');
        else showToast(data.message || 'エラーが発生しました', 'error');
    } catch(e) { showToast('通信エラー', 'error'); }
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
    if (!confirm('チE��イス ' + masterCurrentDeviceId + ' の警告を解除して退去処琁E��行いますか�E�\n検知ログはすべて削除されます、E)) return;
    fetch('/partner/devices/' + masterCurrentDeviceId + '/clear-alert', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); hideModal('deviceDetailModal'); setTimeout(() => location.reload(), 500); }
        else showToast(d.message || 'エラー', 'error');
    }).catch(() => showToast('通信エラー', 'error'));
}

function masterRenderSchedules(schedules) {
    const c = document.getElementById('masterDetailScheduleList');
    if (!schedules.length) { c.innerHTML = '<div class="detail-schedule-empty">スケジュールなぁE/div>'; return; }
    let html = '<div class="detail-schedule-list">';
    schedules.forEach(s => {
        html += '<div class="detail-schedule-item">';
        if (s.type === 'oneshot') {
            html += '<div class="detail-schedule-icon oneshot">📅</div><div class="detail-schedule-info"><p class="detail-schedule-main">' + formatDt(s.start_at) + ' 、E' + (s.end_at ? formatDt(s.end_at) : '無期限') + '</p><p class="detail-schedule-sub">' + escapeHtml(s.memo || '単発') + '</p></div>';
        } else {
            html += '<div class="detail-schedule-icon recurring">🔄</div><div class="detail-schedule-info"><p class="detail-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + s.start_time + '、E + (s.next_day ? '翁E : '') + s.end_time + '</p><p class="detail-schedule-sub">' + escapeHtml(s.memo || '定期') + '</p></div>';
        }
        html += '<button class="detail-schedule-del" onclick="masterConfirmDeleteSchedule(' + s.id + ')">✁E/button></div>';
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
        if (!days.length) { showToast('曜日を選択してください', 'error'); return; }
        const st = document.getElementById('masterSchedStartTime').value, et = document.getElementById('masterSchedEndTime').value;
        if (!st || !et) { showToast('時間帯を�E力してください', 'error'); return; }
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

function confirmDeleteDevice(deviceId) {
    if (!confirm('チE��イス ' + deviceId + ' を削除しますか�E�\nこ�E操作�E取り消せません、E)) return;
    fetch('/partner/devices/' + deviceId, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(d.message || '削除に失敗しました', 'error');
    }).catch(() => showToast('通信エラー', 'error'));
}

// ===== 管琁E��E��カウンチE=====
function showAddAdminModal() { generatePassword('addAdminPassword'); document.getElementById('addAdminModal').classList.add('show'); }
function hideAddAdminModal() { document.getElementById('addAdminModal').classList.remove('show'); }
function showEditAdminModal(data) {
    document.getElementById('editAdminForm').action = '/partner/admin-users/' + data.id;
    document.getElementById('editAdminName').value = data.name;
    document.getElementById('editAdminEmail').value = data.email;
    document.getElementById('editAdminPassword').value = '';
    document.getElementById('editAdminModal').classList.add('show');
}
function hideEditAdminModal() { document.getElementById('editAdminModal').classList.remove('show'); }
function confirmDeleteAdmin(id, name) {
    if (confirm(name + ' を削除しますか�E�\nこ�E操作�E取り消せません、E)) {
        const form = document.getElementById('deleteAdminForm'); form.action = '/partner/admin-users/' + id; form.submit();
    }
}

// ===== 絁E��管琁E=====
function showAddOrgModal() { generatePassword('addOrgPartnerPassword'); document.getElementById('addOrgModal').classList.add('show'); }
function hideAddOrgModal() { document.getElementById('addOrgModal').classList.remove('show'); }
function showEditOrgModal(data) {
    document.getElementById('editOrgForm').action = '/partner/orgs/' + data.id;
    document.getElementById('editOrgName').value = data.name || '';
    document.getElementById('editOrgContactName').value = data.contact_name || '';
    document.getElementById('editOrgContactEmail').value = data.contact_email || '';
    document.getElementById('editOrgContactPhone').value = data.contact_phone || '';
    document.getElementById('editOrgAddress').value = data.address || '';
    document.getElementById('editOrgNotes').value = data.notes || '';
    document.getElementById('editOrgEmail1').value = data.notification_email_1 || '';
    document.getElementById('editOrgEmail2').value = data.notification_email_2 || '';
    document.getElementById('editOrgEmail3').value = data.notification_email_3 || '';
    document.getElementById('editOrgSms1').value = data.notification_sms_1 || '';
    document.getElementById('editOrgSms2').value = data.notification_sms_2 || '';
    document.getElementById('editOrgModal').classList.add('show');
}
function hideEditOrgModal() { document.getElementById('editOrgModal').classList.remove('show'); }
function confirmDeleteOrg(id, name) {
    if (confirm(name + ' を削除しますか�E�\nこ�E操作�E取り消せません、E)) {
        const form = document.getElementById('deleteOrgForm'); form.action = '/partner/orgs/' + id; form.submit();
    }
}

// ===== パ�Eトナーアカウント管琁E=====
async function showOrgAccountsModal(orgId, orgName) {
    orgAccountsCurrentOrgId = orgId;
    document.getElementById('orgAccountsOrgName').textContent = orgName;
    showModal('orgAccountsModal');
    await loadOrgUsers();
}

async function loadOrgUsers() {
    const container = document.getElementById('orgAccountsTable');
    container.innerHTML = '<p style="text-align:center;color:var(--gray-400);padding:20px;">読み込み中...</p>';
    try {
        const res = await fetch('/partner/orgs/' + orgAccountsCurrentOrgId + '/users', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.users || data.users.length === 0) {
            container.innerHTML = '<p style="text-align:center;color:var(--gray-400);padding:16px;font-size:13px;">アカウントがありません</p>';
            return;
        }
        let html = '<table class="partner-user-table"><thead><tr><th>名前</th><th>メールアドレス</th><th>最終ログイン</th><th>操佁E/th></tr></thead><tbody>';
        data.users.forEach(u => {
            const lastLogin = u.last_login_at ? u.last_login_at.replace('T', ' ').substring(0, 16) : '未ログイン';
            html += '<tr>'
                + '<td style="font-weight:500;">' + escapeHtml(u.name) + '</td>'
                + '<td style="font-size:12px;">' + escapeHtml(u.email) + '</td>'
                + '<td style="font-size:11px;color:var(--gray-500);">' + escapeHtml(lastLogin) + '</td>'
                + '<td>'
                + '<button class="action-btn" onclick="showOrgEditUserModal(' + u.id + ', \'' + (u.name||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'") + '\', \'' + (u.email||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'") + '\')">編雁E/button>'
                + '<button class="action-btn" onclick="showOrgResetPasswordModal(' + u.id + ', \'' + (u.name||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'") + '\')">PW</button>'
                + '<button class="action-btn danger" onclick="confirmDeleteOrgUser(' + u.id + ', \'' + (u.name||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'") + '\')">削除</button>'
                + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    } catch(e) {
        container.innerHTML = '<p style="text-align:center;color:#c62828;padding:16px;font-size:13px;">読み込みに失敗しました</p>';
    }
}

function showOrgEditUserModal(userId, name, email) {
    document.getElementById('orgEditUserId').value = userId;
    document.getElementById('orgEditUserName').value = name;
    document.getElementById('orgEditUserEmail').value = email;
    document.getElementById('orgEditUserPassword').value = '';
    document.getElementById('orgEditUserError').style.display = 'none';
    showModal('orgEditUserModal');
}

async function submitOrgEditUser() {
    const userId = document.getElementById('orgEditUserId').value;
    const name = document.getElementById('orgEditUserName').value.trim();
    const email = document.getElementById('orgEditUserEmail').value.trim();
    const password = document.getElementById('orgEditUserPassword').value;
    const errEl = document.getElementById('orgEditUserError');
    errEl.style.display = 'none';
    if (!name || !email) { errEl.textContent = '名前・メールを�E力してください'; errEl.style.display = 'block'; return; }
    const payload = { name, email };
    if (password) payload.password = password;
    try {
        const res = await fetch('/partner/orgs/' + orgAccountsCurrentOrgId + '/users/' + userId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message, 'success');
            hideModal('orgEditUserModal');
            await loadOrgUsers();
        } else {
            errEl.textContent = data.message || 'エラーが発生しました';
            errEl.style.display = 'block';
        }
    } catch(e) { errEl.textContent = '通信エラーが発生しました'; errEl.style.display = 'block'; }
}

async function confirmDeleteOrgUser(userId, name) {
    if (!confirm(name + ' を削除しますか�E�E)) return;
    try {
        const res = await fetch('/partner/orgs/' + orgAccountsCurrentOrgId + '/users/' + userId, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (res.ok && data.success) { showToast(data.message, 'success'); await loadOrgUsers(); }
        else showToast(data.message || '削除に失敗しました', 'error');
    } catch(e) { showToast('通信エラー', 'error'); }
}

// ===== パスワードリセチE�� =====
function showOrgResetPasswordModal(userId, name) {
    document.getElementById('orgResetPasswordUserId').value = userId;
    document.getElementById('orgResetPasswordName').textContent = name;
    document.getElementById('orgResetPasswordError').style.display = 'none';
    generatePassword('orgResetPasswordValue');
    showModal('orgResetPasswordModal');
}

async function submitOrgResetPassword() {
    const userId = document.getElementById('orgResetPasswordUserId').value;
    const password = document.getElementById('orgResetPasswordValue').value;
    const errEl = document.getElementById('orgResetPasswordError');
    errEl.style.display = 'none';
    if (!password) { errEl.textContent = 'パスワードを入力してください'; errEl.style.display = 'block'; return; }
    try {
        const res = await fetch('/partner/orgs/' + orgAccountsCurrentOrgId + '/users/' + userId + '/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ password })
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message, 'success');
            hideModal('orgResetPasswordModal');
        } else {
            errEl.textContent = data.message || 'エラーが発生しました';
            errEl.style.display = 'block';
        }
    } catch(e) { errEl.textContent = '通信エラーが発生しました'; errEl.style.display = 'block'; }
}
</script>
@endsection
