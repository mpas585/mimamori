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
    .org-notify-icons { display: flex; gap: 6px; align-items: center; font-size: 11px; color: var(--gray-500); }
    /* モーダル内共通 */
    .modal-section { margin-bottom: 20px; }
    .modal-section-title { font-size: 13px; font-weight: 700; color: var(--gray-600); margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid var(--gray-200); }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    /* デバイス詳細モーダル */
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .detail-item { padding: 10px 12px; background: var(--beige, #faf8f4); border-radius: var(--radius, 6px); }
    .detail-item-label { font-size: 11px; color: var(--gray-500, #888); margin-bottom: 4px; }
    .detail-item-value { font-size: 14px; font-weight: 600; color: var(--gray-800, #333); }
    .detail-status-badge { display: inline-block; padding: 4px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; margin-bottom: 16px; }
    .detail-status-badge.normal { background: #e8f5e9; color: #2e7d32; }
    .detail-status-badge.warning { background: #fff3e0; color: #e65100; }
    .detail-status-badge.alert { background: #fbe9e7; color: #c62828; }
    .detail-status-badge.offline { background: #eeeeee; color: #616161; }
    .detail-status-badge.inactive { background: #f5f5f5; color: #9e9e9e; }
    .detail-notify-row { display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: var(--beige, #faf8f4); border-radius: var(--radius, 6px); font-size: 13px; margin-bottom: 6px; }
    .detail-notify-label { min-width: 90px; font-size: 12px; color: var(--gray-500, #888); }
    .detail-notify-value { font-weight: 600; color: var(--gray-800, #333); }
    .detail-notify-enabled { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; }
    .detail-notify-enabled.on { background: #e8f5e9; color: #2e7d32; }
    .detail-notify-enabled.off { background: #f5f5f5; color: #9e9e9e; }
    .mono { font-family: monospace; font-weight: 700; letter-spacing: 1px; }
    .flash-success { background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
    .flash-error { background: #fbe9e7; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
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

@if(session('success'))
    <div class="flash-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error">⚠️ {{ session('error') }}</div>
@endif

<div class="tab-bar">
    <button class="tab active" onclick="switchTab('devices', this)">デバイス管理</button>
    <button class="tab" onclick="switchTab('admins', this)">管理者アカウント</button>
    <button class="tab" onclick="switchTab('orgs', this)">組織管理</button>
</div>

{{-- ===== デバイス管理タブ ===== --}}
<div id="tab-devices" class="tab-content active">
    <div class="card">
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
        @error('count')
            <div style="color:#c62828;font-size:12px;margin-top:8px;">{{ $message }}</div>
        @enderror
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
                        <td><button class="action-btn" onclick="showDeviceDetail('{{ $device->device_id }}')">詳細</button></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty-row">デバイスがありません</td></tr>
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
                <tr><th>組織名</th><th>担当者</th><th>連絡先</th><th>デバイス数</th><th>台数上限</th><th>契約期限</th><th>プレミアム</th><th>通知設定</th><th>操作</th></tr>
            </thead>
            <tbody>
                @forelse($organizations as $org)
                    @php
                        $expiresAt = $org->expires_at;
                        $isExpired = $expiresAt && $expiresAt->isPast();
                        $isExpiringSoon = $expiresAt && !$isExpired && $expiresAt->diffInDays(now()) <= 30;
                        $hasNotifyEmail = $org->notification_email_1 || $org->notification_email_2 || $org->notification_email_3;
                        $hasNotifySms = $org->notification_sms_1 || $org->notification_sms_2;
                    @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $org->name }}</td>
                        <td style="font-size:12px;">{{ $org->contact_name ?: '-' }}</td>
                        <td style="font-size:12px;">{{ $org->contact_email }}</td>
                        <td style="font-size:13px;">
                            <span style="{{ $org->devices_count >= ($org->device_limit ?? 100) ? 'color:var(--red);font-weight:600;' : '' }}">{{ $org->devices_count }}台</span>
                        </td>
                        <td style="font-size:13px;">{{ $org->device_limit ?? 100 }}台</td>
                        <td style="font-size:12px;">
                            @if($expiresAt)
                                <span class="{{ $isExpired || $isExpiringSoon ? 'expires-warn' : 'expires-ok' }}">
                                    {{ $expiresAt->format('Y/m/d') }}
                                    @if($isExpired) ⚠️期限切れ @elseif($isExpiringSoon) ⚠️あと{{ $expiresAt->diffInDays(now()) }}日 @endif
                                </span>
                            @else
                                <span style="color:var(--gray-400);">-</span>
                            @endif
                        </td>
                        <td>
                            <label class="watch-toggle">
                                <input type="checkbox" {{ $org->premium_enabled ? 'checked' : '' }} onchange="toggleOrgPremium({{ $org->id }}, this.checked, this)">
                                <span class="watch-slider"></span>
                            </label>
                            <span class="org-premium-label-{{ $org->id }}" style="font-size:12px;color:var(--gray-500);margin-left:8px;">{{ $org->premium_enabled ? '有効' : '無効' }}</span>
                        </td>
                        <td>
                            <div class="org-notify-icons">
                                @if($hasNotifyEmail) <span title="メール通知設定あり" style="{{ $org->notification_enabled ? '' : 'opacity:0.4;' }}">📧</span> @endif
                                @if($hasNotifySms) <span title="SMS通知設定あり" style="{{ $org->notification_sms_enabled ? '' : 'opacity:0.4;' }}">💬</span> @endif
                                @if(!$hasNotifyEmail && !$hasNotifySms) <span style="color:var(--gray-300);">未設定</span> @endif
                            </div>
                        </td>
                        <td>
                            <button class="action-btn" onclick="showEditOrgModal({{ json_encode([
                                'id' => $org->id,
                                'name' => $org->name,
                                'contact_name' => $org->contact_name,
                                'contact_email' => $org->contact_email,
                                'contact_phone' => $org->contact_phone,
                                'address' => $org->address,
                                'notes' => $org->notes,
                                'device_limit' => $org->device_limit ?? 100,
                                'expires_at' => $org->expires_at ? $org->expires_at->format('Y-m-d') : '',
                                'notification_email_1' => $org->notification_email_1,
                                'notification_email_2' => $org->notification_email_2,
                                'notification_email_3' => $org->notification_email_3,
                                'notification_sms_1' => $org->notification_sms_1,
                                'notification_sms_2' => $org->notification_sms_2,
                            ]) }})">編集</button>
                            @if($org->devices_count === 0)
                                <button class="action-btn danger" onclick="confirmDeleteOrg({{ $org->id }}, '{{ $org->name }}')">削除</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty-row">組織がありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== デバイス詳細モーダル ===== --}}
<div id="deviceDetailModal" class="modal-overlay" onclick="if(event.target===this)hideDeviceDetailModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>📋 デバイス詳細</h3><button class="modal-close" onclick="hideDeviceDetailModal()">×</button></div>
        <div class="modal-body" id="deviceDetailBody">
            <div style="text-align:center;color:#aaa;padding:40px 0;">読み込み中...</div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" onclick="hideDeviceDetailModal()">閉じる</button></div>
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
                    <p class="form-hint">オペレーター：担当組織のデバイスのみ管理可能 / マスター：全機能にアクセス可能</p>
                </div>
                <div class="form-group" id="addAdminOrgRow">
                    <label class="form-label">所属組織</label>
                    <select name="organization_id" class="form-input">
                        <option value="">未割当</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">初期パスワード *</label>
                    <div class="password-field">
                        <input type="text" name="password" id="addAdminPassword" class="form-input" placeholder="自動生成されます" required>
                        <button type="button" class="password-generate-btn" onclick="generatePassword('addAdminPassword')">生成</button>
                    </div>
                    <p class="form-hint">初回ログイン後にパスワード変更を推奨してください</p>
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
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label class="form-label">名前 *</label><input type="text" name="name" id="editAdminName" class="form-input" required></div>
                <div class="form-group"><label class="form-label">メールアドレス *</label><input type="email" name="email" id="editAdminEmail" class="form-input" required></div>
                <div class="form-group">
                    <label class="form-label">権限 *</label>
                    <select name="role" id="editAdminRole" class="form-input" onchange="toggleOrgSelect('editAdminOrgRow', this.value)">
                        <option value="operator">オペレーター（組織管理者）</option>
                        <option value="master">マスター（全権限）</option>
                    </select>
                </div>
                <div class="form-group" id="editAdminOrgRow">
                    <label class="form-label">所属組織</label>
                    <select name="organization_id" id="editAdminOrg" class="form-input">
                        <option value="">未割当</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">新しいパスワード</label>
                    <div class="password-field">
                        <input type="text" name="password" id="editAdminPassword" class="form-input" placeholder="変更しない場合は空欄">
                        <button type="button" class="password-generate-btn" onclick="generatePassword('editAdminPassword')">生成</button>
                    </div>
                    <p class="form-hint">空欄の場合、パスワードは変更されません</p>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditAdminModal()">キャンセル</button><button type="submit" class="btn btn-primary">保存</button></div>
        </form>
    </div>
</div>

<form id="deleteAdminForm" method="POST" action="" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- ===== 組織追加モーダル ===== --}}
<div id="addOrgModal" class="modal-overlay" onclick="if(event.target===this)hideAddOrgModal()">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header"><h3>組織追加</h3><button class="modal-close" onclick="hideAddOrgModal()">×</button></div>
        <form method="POST" action="{{ route('partner.orgs.store') }}">
            @csrf
            <div class="modal-body">
                <div class="modal-section">
                    <div class="modal-section-title">基本情報</div>
                    <div class="form-group"><label class="form-label">組織名 *</label><input type="text" name="name" class="form-input" placeholder="例：〇〇不動産株式会社" required></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">担当者名</label><input type="text" name="contact_name" class="form-input" placeholder="山田 太郎"></div>
                        <div class="form-group"><label class="form-label">連絡先メール *</label><input type="email" name="contact_email" class="form-input" placeholder="admin@example.com" required></div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">電話番号</label><input type="text" name="contact_phone" class="form-input" placeholder="03-0000-0000"></div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-group"><label class="form-label">住所</label><input type="text" name="address" class="form-input" placeholder="東京都〇〇区..."></div>
                    <div class="form-group"><label class="form-label">メモ</label><textarea name="notes" class="form-input" rows="2" style="resize:vertical;"></textarea></div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title">契約情報</div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">台数上限</label><input type="number" name="device_limit" class="form-input" value="100" min="1" max="9999"><p class="form-hint">デバイス登録できる最大台数</p></div>
                        <div class="form-group"><label class="form-label">契約期限</label><input type="date" name="expires_at" class="form-input"><p class="form-hint">空欄の場合は無期限</p></div>
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
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="modal-section">
                    <div class="modal-section-title">基本情報</div>
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
                <div class="modal-section">
                    <div class="modal-section-title">契約情報</div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">台数上限</label><input type="number" name="device_limit" id="editOrgDeviceLimit" class="form-input" min="1" max="9999"><p class="form-hint">デバイス登録できる最大台数</p></div>
                        <div class="form-group"><label class="form-label">契約期限</label><input type="date" name="expires_at" id="editOrgExpiresAt" class="form-input"><p class="form-hint">空欄の場合は無期限</p></div>
                    </div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title">通知設定（確認・修正用）</div>
                    <p style="font-size:12px;color:var(--gray-500);margin-bottom:12px;">通常はパートナー側で設定します。緊急時のみ修正してください。</p>
                    <div class="form-group"><label class="form-label">通知メール 1</label><input type="email" name="notification_email_1" id="editOrgEmail1" class="form-input" placeholder="notify@example.com"></div>
                    <div class="form-group"><label class="form-label">通知メール 2</label><input type="email" name="notification_email_2" id="editOrgEmail2" class="form-input"></div>
                    <div class="form-group"><label class="form-label">通知メール 3</label><input type="email" name="notification_email_3" id="editOrgEmail3" class="form-input"></div>
                    <div class="form-row-2">
                        <div class="form-group"><label class="form-label">SMS通知先 1</label><input type="text" name="notification_sms_1" id="editOrgSms1" class="form-input" placeholder="09012345678"></div>
                        <div class="form-group"><label class="form-label">SMS通知先 2</label><input type="text" name="notification_sms_2" id="editOrgSms2" class="form-input"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideEditOrgModal()">キャンセル</button><button type="submit" class="btn btn-primary">保存</button></div>
        </form>
    </div>
</div>

<form id="deleteOrgForm" method="POST" action="" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

function switchTab(tabName, btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'admins') switchTab('admins', document.querySelectorAll('.tab')[1]);
    else if (tab === 'orgs') switchTab('orgs', document.querySelectorAll('.tab')[2]);
});

function copyText(id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        btn.textContent = 'コピー済';
        setTimeout(() => { btn.textContent = 'コピー'; }, 1500);
    });
}

function generatePassword(inputId) {
    const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let pw = '';
    for (let i = 0; i < 12; i++) pw += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById(inputId).value = pw;
}

function escapeHtml(s) {
    if (!s) return '-';
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

// ===== デバイス詳細 =====
async function showDeviceDetail(deviceId) {
    document.getElementById('deviceDetailBody').innerHTML = '<div style="text-align:center;color:#aaa;padding:40px 0;">読み込み中...</div>';
    document.getElementById('deviceDetailModal').classList.add('show');

    try {
        const res = await fetch('/partner/devices/' + deviceId + '/detail', { headers: { 'Accept': 'application/json' } });
        const d = await res.json();

        const statusLabels = { normal: '正常稼働中', warning: '注意', alert: '未検知警告', offline: '通信途絶', inactive: '未稼働' };
        const awayText = d.away_mode ? ('外出中' + (d.away_until ? '（' + d.away_until + 'まで）' : '')) : 'OFF';

        let html = '<div class="detail-status-badge ' + (d.status || 'inactive') + '">' + (statusLabels[d.status] || d.status) + '</div>';

        html += '<div class="modal-section"><div class="modal-section-title">基本情報</div><div class="detail-grid">';
        html += '<div class="detail-item"><p class="detail-item-label">デバイスID</p><p class="detail-item-value mono">' + escapeHtml(d.device_id) + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">組織</p><p class="detail-item-value">' + escapeHtml(d.organization_name) + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">部屋番号</p><p class="detail-item-value">' + escapeHtml(d.room_number) + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">入居者名</p><p class="detail-item-value">' + escapeHtml(d.tenant_name) + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">最終受信</p><p class="detail-item-value">' + (d.last_received_at || '-') + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">最終検知</p><p class="detail-item-value">' + (d.last_human_detected_at || '-') + '</p></div>';
        html += '</div></div>';

        html += '<div class="modal-section"><div class="modal-section-title">センサー状態</div><div class="detail-grid">';
        const batteryClass = (d.battery_pct !== null && d.battery_pct < 20) ? ' style="color:#c62828;"' : '';
        html += '<div class="detail-item"><p class="detail-item-label">電池残量</p><p class="detail-item-value"' + batteryClass + '>' + (d.battery_pct !== null ? d.battery_pct + '%' : '-') + (d.battery_voltage ? ' / ' + d.battery_voltage + 'V' : '') + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">電波強度</p><p class="detail-item-value">' + escapeHtml(d.rssi_label) + '</p></div>';
        html += '</div></div>';

        html += '<div class="modal-section"><div class="modal-section-title">設定</div><div class="detail-grid">';
        html += '<div class="detail-item"><p class="detail-item-label">アラート閾値</p><p class="detail-item-value">' + (d.alert_threshold_hours || 24) + '時間</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">ペット除外</p><p class="detail-item-value">' + (d.pet_exclusion_enabled ? 'ON' : 'OFF') + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">設置高さ</p><p class="detail-item-value">' + (d.install_height_cm ? d.install_height_cm + 'cm' : '-') + '</p></div>';
        html += '<div class="detail-item"><p class="detail-item-label">外出モード</p><p class="detail-item-value">' + awayText + '</p></div>';
        html += '</div></div>';

        html += '<div class="modal-section"><div class="modal-section-title">通知設定</div>';
        // SMS
        html += '<div class="detail-notify-row"><span class="detail-notify-label">💬 SMS通知</span>';
        html += '<span class="detail-notify-enabled ' + (d.sms_enabled ? 'on' : 'off') + '">' + (d.sms_enabled ? '有効' : '無効') + '</span>';
        if (d.sms_phone_1) html += '<span class="detail-notify-value" style="margin-left:8px;">' + escapeHtml(d.sms_phone_1) + (d.sms_phone_2 ? ' / ' + escapeHtml(d.sms_phone_2) : '') + '</span>';
        html += '</div>';
        // 電話
        html += '<div class="detail-notify-row"><span class="detail-notify-label">📞 電話通知</span>';
        html += '<span class="detail-notify-enabled ' + (d.voice_enabled ? 'on' : 'off') + '">' + (d.voice_enabled ? '有効' : '無効') + '</span>';
        if (d.voice_phone_1) html += '<span class="detail-notify-value" style="margin-left:8px;">' + escapeHtml(d.voice_phone_1) + (d.voice_phone_2 ? ' / ' + escapeHtml(d.voice_phone_2) : '') + '</span>';
        html += '</div>';
        // プレミアム
        html += '<div style="font-size:12px;color:var(--gray-500);margin-top:8px;">プレミアム: ' + (d.premium_enabled ? '<span style="color:#2e7d32;font-weight:600;">有効</span>' : '<span style="color:#9e9e9e;">無効</span>') + '</div>';
        html += '</div>';

        if (d.memo) {
            html += '<div class="modal-section"><div class="modal-section-title">メモ</div><div style="font-size:13px;color:var(--gray-700);padding:8px 0;">' + escapeHtml(d.memo) + '</div></div>';
        }

        html += '<div style="font-size:11px;color:var(--gray-400);margin-top:8px;">登録日: ' + (d.registered_at || '-') + '</div>';

        document.getElementById('deviceDetailBody').innerHTML = html;
    } catch(e) {
        document.getElementById('deviceDetailBody').innerHTML = '<div style="text-align:center;color:#c62828;padding:40px 0;">詳細の取得に失敗しました</div>';
    }
}

function hideDeviceDetailModal() { document.getElementById('deviceDetailModal').classList.remove('show'); }

// ===== 管理者アカウント =====
function toggleOrgSelect(rowId, role) {
    var row = document.getElementById(rowId);
    if (row) row.style.display = role === 'operator' ? '' : 'none';
}

function showAddAdminModal() {
    generatePassword('addAdminPassword');
    toggleOrgSelect('addAdminOrgRow', 'operator');
    document.getElementById('addAdminModal').classList.add('show');
}
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
        const form = document.getElementById('deleteAdminForm');
        form.action = '/partner/admin-users/' + id;
        form.submit();
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
        const form = document.getElementById('deleteOrgForm');
        form.action = '/partner/orgs/' + id;
        form.submit();
    }
}

async function toggleOrgPremium(orgId, enabled, checkbox) {
    try {
        var res = await fetch('/partner/orgs/' + orgId + '/toggle-premium', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ premium_enabled: enabled ? 1 : 0 })
        });
        var data = await res.json();
        if (data.success) {
            var label = document.querySelector('.org-premium-label-' + orgId);
            if (label) label.textContent = enabled ? '有効' : '無効';
        } else {
            checkbox.checked = !enabled;
        }
    } catch (e) {
        checkbox.checked = !enabled;
        console.error(e);
    }
}
</script>
@endsection


