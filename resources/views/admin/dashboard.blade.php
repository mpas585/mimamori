@extends('layouts.admin')

@section('title', 'デバイス管理')

@section('styles')
    /* ===== 契約情報 ===== */
    .contract-info {
        display: flex;
        gap: 20px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .contract-item {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        flex: 1;
        min-width: 200px;
    }
    .contract-label {
        font-size: 12px;
        color: var(--gray-500);
        margin-bottom: 4px;
    }
    .contract-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-800);
    }
    .contract-note {
        font-size: 11px;
        color: var(--gray-400);
        margin-top: 4px;
    }

    /* ===== ステータスグリッド ===== */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .status-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 16px;
        text-align: center;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        cursor: pointer;
        transition: all 0.2s;
    }
    .status-card:hover {
        box-shadow: var(--shadow);
        transform: translateY(-1px);
    }
    .status-card.active {
        border-color: var(--gray-800);
        box-shadow: 0 0 0 2px var(--gray-800);
    }
    .status-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }
    .status-value.green { color: var(--green-dark); }
    .status-value.yellow { color: var(--yellow); }
    .status-value.red { color: var(--red); }
    .status-value.gray { color: var(--gray-600); }
    .status-value.light { color: var(--gray-400); }
    .status-label {
        font-size: 11px;
        color: var(--gray-500);
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.green { background: var(--green); }
    .status-dot.yellow { background: var(--yellow); }
    .status-dot.red { background: var(--red); }
    .status-dot.gray { background: var(--gray-600); }
    .status-dot.light { background: var(--gray-300); }
    .status-legend {
        display: flex;
        gap: 16px;
        font-size: 11px;
        color: var(--gray-500);
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    /* ===== ツールバー ===== */
    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        gap: 12px;
        flex-wrap: wrap;
    }
    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .search-box {
        display: flex;
        align-items: center;
        background: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        padding: 0 12px;
        width: 240px;
    }
    .search-box:focus-within {
        border-color: var(--gray-500);
        box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
    }
    .search-box input {
        flex: 1;
        padding: 8px 8px;
        border: none;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
    }
    .search-box input:focus { outline: none; }
    .search-box span { color: var(--gray-400); font-size: 14px; }
    .filter-select {
        padding: 8px 12px;
        font-size: 13px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
        color: var(--gray-700);
        cursor: pointer;
        font-weight: 500;
    }
    .toolbar-btn {
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .toolbar-btn:hover { background: var(--beige); border-color: var(--gray-400); }
    .toolbar-count {
        font-size: 13px;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* ===== テーブル ===== */
    .table-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border: 1px solid var(--gray-200);
    }
    .table-wrapper { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    thead { background: var(--beige); }
    th {
        padding: 12px 14px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
        border-bottom: 2px solid var(--gray-300);
        border-right: 1px solid var(--gray-200);
        font-size: 12px;
    }
    th:last-child { border-right: none; }
    th.sortable { cursor: pointer; user-select: none; }
    th.sortable:hover { background: var(--gray-100); }
    .sort-icon { font-size: 11px; color: var(--gray-400); margin-left: 4px; }
    td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--gray-200);
        border-right: 1px solid var(--gray-100);
        vertical-align: middle;
    }
    td:last-child { border-right: none; }
    tbody tr:nth-child(odd) { background: var(--white); }
    tbody tr:nth-child(even) { background: var(--cream); }
    tbody tr:hover { background: var(--gray-100); }
    tbody tr:last-child td { border-bottom: none; }

    /* ===== ステータスバッジ ===== */
    .device-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
    }
    .device-status.normal { background: var(--green-light); color: var(--green-dark); }
    .device-status.warning { background: var(--yellow-light); color: #a16207; }
    .device-status.alert { background: var(--red-light); color: var(--red); }
    .device-status.offline { background: var(--gray-100); color: var(--gray-600); }
    .device-status.vacant { background: #f8fafc; color: var(--gray-400); border: 1px solid var(--gray-200); }

    /* ===== 見守りトグル ===== */
    .watch-toggle {
        position: relative;
        width: 44px;
        height: 24px;
        display: inline-block;
    }
    .watch-toggle input { opacity: 0; width: 0; height: 0; }
    .watch-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: var(--gray-300);
        border-radius: 12px;
        transition: 0.3s;
    }
    .watch-slider::before {
        content: '';
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }
    .watch-toggle input:checked + .watch-slider { background: var(--green); }
    .watch-toggle input:checked + .watch-slider::before { transform: translateX(20px); }
    .watch-timer-icon {
        font-size: 12px;
        color: var(--orange);
        margin-left: 4px;
    }

    .mono {
        font-family: monospace;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* ===== 電池・電波 ===== */
    .battery-low { color: var(--red); font-weight: 600; }
    .signal-weak { color: var(--orange); }

    /* ===== アクションボタン ===== */
    .action-btn {
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: 4px;
        background: var(--white);
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 4px;
    }
    .action-btn:hover { background: var(--beige); }
    .action-btn.danger { color: var(--red); border-color: var(--red-light); }
    .action-btn.danger:hover { background: var(--red-light); }
    .action-btn.setup { background: var(--green); color: var(--white); border-color: var(--green); }
    .action-btn.setup:hover { background: var(--green-dark); }

    /* ===== ページネーション ===== */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-top: 2px solid var(--gray-200);
        background: var(--cream);
        font-size: 13px;
    }
    .pagination-info { color: var(--gray-600); font-weight: 500; }
    .pagination-buttons { display: flex; gap: 4px; }
    .page-btn {
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        background: var(--white);
        color: var(--gray-700);
        cursor: pointer;
        text-decoration: none;
    }
    .page-btn:hover { background: var(--beige); }
    .page-btn.active {
        background: var(--gray-800);
        color: var(--white);
        border-color: var(--gray-800);
    }
    .page-btn.disabled { opacity: 0.5; cursor: not-allowed; }

    /* ===== 詳細モーダル ===== */
    .detail-section {
        margin-bottom: 20px;
    }
    .detail-section-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .detail-item {
        padding: 10px 12px;
        background: var(--beige);
        border-radius: var(--radius);
    }
    .detail-item-label {
        font-size: 11px;
        color: var(--gray-500);
        margin-bottom: 2px;
    }
    .detail-item-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
    }
    .detail-status-badge {
        display: inline-block;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .detail-status-badge.normal { background: var(--green-light); color: var(--green-dark); }
    .detail-status-badge.warning { background: var(--yellow-light); color: #a16207; }
    .detail-status-badge.alert { background: var(--red-light); color: var(--red); }
    .detail-status-badge.offline { background: var(--gray-100); color: var(--gray-600); }

    /* ===== トースト通知 ===== */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 14px 20px;
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 600;
        color: var(--white);
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.success { background: var(--green-dark); }
    .toast.error { background: var(--red); }

    @media (max-width: 768px) {
        .status-grid { grid-template-columns: repeat(3, 1fr); }
        .toolbar { flex-direction: column; align-items: stretch; }
        .search-box { width: 100%; }
        .contract-info { flex-direction: column; }
    }
    @media (max-width: 480px) {
        .status-grid { grid-template-columns: repeat(2, 1fr); }
    }
@endsection

@section('content')
    {{-- 契約情報 --}}
    @if(isset($organization))
        <div class="contract-info">
            <div class="contract-item">
                <div class="contract-label">契約プラン</div>
                <div class="contract-value">ビジネスプラン（{{ $organization->device_limit ?? 0 }}台）</div>
            </div>
            <div class="contract-item">
                <div class="contract-label">有効期限</div>
                <div class="contract-value">
                    {{ $organization->expires_at ? \Carbon\Carbon::parse($organization->expires_at)->format('Y/m/d') : '-' }}
                </div>
                <div class="contract-note">ご契約に関するお問い合わせは管理会社まで</div>
            </div>
        </div>
    @endif

    {{-- アラートバナー --}}
    @if(($stats['alert'] ?? 0) > 0)
        <div class="alert-banner warning">
            <span>🔴 <strong>{{ $stats['alert'] }}件</strong>のデバイスで24時間以上検知がありません（要確認）</span>
            <button class="alert-banner-btn" onclick="filterByStatus('alert')">確認する</button>
        </div>
    @endif
    @if(($stats['offline'] ?? 0) > 0)
        <div class="alert-banner offline">
            <span>⚫ <strong>{{ $stats['offline'] }}件</strong>のデバイスが48時間以上通信していません（電波障害または電池切れの可能性）</span>
            <button class="alert-banner-btn" onclick="filterByStatus('offline')">確認する</button>
        </div>
    @endif

    {{-- ステータスカード --}}
    <div class="status-grid">
        <div class="status-card {{ request('status') === 'normal' ? 'active' : '' }}" onclick="filterByStatus('normal')">
            <div class="status-value green">{{ $stats['normal'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot green"></span> 正常</div>
        </div>
        <div class="status-card {{ request('status') === 'warning' ? 'active' : '' }}" onclick="filterByStatus('warning')">
            <div class="status-value yellow">{{ $stats['warning'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot yellow"></span> 注意</div>
        </div>
        <div class="status-card {{ request('status') === 'alert' ? 'active' : '' }}" onclick="filterByStatus('alert')">
            <div class="status-value red">{{ $stats['alert'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot red"></span> 警告</div>
        </div>
        <div class="status-card {{ request('status') === 'offline' ? 'active' : '' }}" onclick="filterByStatus('offline')">
            <div class="status-value gray">{{ $stats['offline'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot gray"></span> 離線</div>
        </div>
        <div class="status-card {{ request('status') === 'vacant' ? 'active' : '' }}" onclick="filterByStatus('vacant')">
            <div class="status-value light">{{ $stats['vacant'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot light"></span> 空室</div>
        </div>
    </div>

    <div class="status-legend">
        <span>正常: 検知あり</span>
        <span>注意: 電池低下/未検知気味</span>
        <span>警告: 長時間未検知</span>
        <span>離線: 通信途絶</span>
        <span>空室: デバイス未割当</span>
    </div>

    {{-- ツールバー --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('admin.org.dashboard') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <div class="search-box">
                    <span>🔍</span>
                    <input type="text" name="search" placeholder="部屋番号・名前で検索..." value="{{ request('search') }}">
                </div>
                <select name="status" class="filter-select">
                    <option value="">すべてのステータス</option>
                    <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>🟢 正常のみ</option>
                    <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>🟡 注意のみ</option>
                    <option value="alert" {{ request('status') === 'alert' ? 'selected' : '' }}>🔴 警告のみ</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>⚫ 離線のみ</option>
                    <option value="vacant" {{ request('status') === 'vacant' ? 'selected' : '' }}>⚪ 空室のみ</option>
                </select>
                <select name="watch" class="filter-select">
                    <option value="">すべての見守り状態</option>
                    <option value="on" {{ request('watch') === 'on' ? 'selected' : '' }}>見守りON</option>
                    <option value="off" {{ request('watch') === 'off' ? 'selected' : '' }}>見守りOFF</option>
                    <option value="timer" {{ request('watch') === 'timer' ? 'selected' : '' }}>⏰ タイマー設定中</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">絞り込み</button>
            </form>
            <span class="toolbar-count">登録: <strong>{{ $devices->total() ?? 0 }}</strong> / {{ $organization->device_limit ?? 100 }}台</span>
        </div>
        <div class="toolbar-right">
            <button class="toolbar-btn" onclick="showAddDeviceModal()">➕ デバイス追加</button>
            <a href="{{ route('admin.org.csv') }}" class="toolbar-btn">📥 CSV出力</a>
        </div>
    </div>

    {{-- デバイステーブル --}}
    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>部屋 / 名前</th>
                        <th>デバイスID</th>
                        <th>見守り</th>
                        <th>最終検知</th>
                        <th>電池</th>
                        <th>電波</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        @php
                            $assignment = $device->orgAssignment ?? null;
                            $roomNumber = $assignment ? $assignment->room_number : null;
                            $tenantName = $assignment ? $assignment->tenant_name : null;
                            $isVacant = !$assignment || !$tenantName;
                            $displayStatus = $isVacant ? 'vacant' : $device->status;

                            $lastDetected = $device->last_human_detected_at;
                            $timeSince = $lastDetected ? $lastDetected->diffForHumans() : null;

                            $rssi = $device->rssi;
                            $signalLabel = '-';
                            if ($rssi !== null) {
                                if ($rssi > -70) $signalLabel = '良好';
                                elseif ($rssi > -85) $signalLabel = '普通';
                                else $signalLabel = '弱い';
                            }
                        @endphp
                        <tr>
                            <td>
                                @switch($displayStatus)
                                    @case('normal')
                                        <span class="device-status normal">正常</span>
                                        @break
                                    @case('warning')
                                        <span class="device-status warning">注意</span>
                                        @break
                                    @case('alert')
                                        <span class="device-status alert">警告</span>
                                        @break
                                    @case('offline')
                                        <span class="device-status offline">離線</span>
                                        @break
                                    @case('vacant')
                                        <span class="device-status vacant">空室</span>
                                        @break
                                    @default
                                        <span class="device-status offline">-</span>
                                @endswitch
                            </td>
                            <td>
                                @if($roomNumber)
                                    <strong>{{ $roomNumber }}</strong><br>
                                    <span style="font-size:12px;color:var(--gray-500);">{{ $tenantName ?: '-' }}</span>
                                @else
                                    <span style="color:var(--gray-400);">-</span>
                                @endif
                            </td>
                            <td class="mono">{{ $device->device_id }}</td>
                            <td>
                                @if(!$isVacant)
                                    <label class="watch-toggle">
                                        <input type="checkbox" {{ !$device->away_mode ? 'checked' : '' }}
                                            onchange="toggleWatch('{{ $device->device_id }}', this.checked, this)">
                                        <span class="watch-slider"></span>
                                    </label>
                                    @if($device->away_until)
                                        <span class="watch-timer-icon">⏰</span>
                                    @endif
                                @endif
                            </td>
                            <td style="font-size:12px;">
                                {{ $timeSince ?: '-' }}
                            </td>
                            <td class="{{ $device->battery_pct && $device->battery_pct < 20 ? 'battery-low' : '' }}" style="font-size:12px;">
                                {{ $device->battery_pct ? $device->battery_pct . '%' : '-' }}
                            </td>
                            <td class="{{ $rssi !== null && $rssi < -85 ? 'signal-weak' : '' }}" style="font-size:12px;">
                                {{ $signalLabel }}
                            </td>
                            <td>
                                <button class="action-btn" onclick="showDeviceDetail('{{ $device->device_id }}')">詳細</button>
                                <button class="action-btn danger" onclick="confirmDelete('{{ $device->device_id }}')">削除</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--gray-400);padding:40px;">デバイスがありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
            <div class="pagination-bar">
                <span class="pagination-info">
                    {{ $devices->firstItem() }}〜{{ $devices->lastItem() }}件 / 全{{ $devices->total() }}件
                </span>
                <div class="pagination-buttons">
                    @if($devices->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a href="{{ $devices->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif

                    @foreach($devices->getUrlRange(max(1, $devices->currentPage() - 2), min($devices->lastPage(), $devices->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $devices->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach

                    @if($devices->currentPage() + 2 < $devices->lastPage())
                        <span class="page-btn disabled">...</span>
                        <a href="{{ $devices->url($devices->lastPage()) }}" class="page-btn">{{ $devices->lastPage() }}</a>
                    @endif

                    @if($devices->hasMorePages())
                        <a href="{{ $devices->nextPageUrl() }}" class="page-btn">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- ===== モーダル: デバイス追加 ===== --}}
    <div id="addDeviceModal" class="modal-overlay" onclick="if(event.target===this)hideModal('addDeviceModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>➕ デバイス追加</h3>
                <button class="modal-close" onclick="hideModal('addDeviceModal')">×</button>
            </div>
            <form method="POST" action="{{ route('admin.org.devices.add') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">デバイスID</label>
                        <input type="text" class="form-input" name="device_id" placeholder="A3K9X2" maxlength="6" style="text-transform:uppercase;" required>
                        <p class="form-hint">製品ラベルに記載の6文字</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">部屋番号</label>
                        <input type="text" class="form-input" name="room_number" placeholder="101">
                    </div>
                    <div class="form-group">
                        <label class="form-label">入居者名（任意）</label>
                        <input type="text" class="form-input" name="tenant_name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">メモ（任意）</label>
                        <input type="text" class="form-input" name="memo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal('addDeviceModal')">キャンセル</button>
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== モーダル: デバイス削除確認 ===== --}}
    <div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('deleteModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>⚠️ デバイス削除</h3>
                <button class="modal-close" onclick="hideModal('deleteModal')">×</button>
            </div>
            <form id="deleteForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>デバイス <strong id="deleteDeviceId" class="mono">-</strong> を組織から削除しますか？</p>
                    <p style="color:var(--gray-500);font-size:13px;margin-top:8px;">デバイスの登録データは残りますが、組織との紐付けが解除されます。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal('deleteModal')">キャンセル</button>
                    <button type="submit" class="btn btn-danger">削除する</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== モーダル: デバイス詳細 ===== --}}
    <div id="detailModal" class="modal-overlay" onclick="if(event.target===this)hideModal('detailModal')">
        <div class="modal" style="max-width:560px;">
            <div class="modal-header">
                <h3>📋 デバイス詳細</h3>
                <button class="modal-close" onclick="hideModal('detailModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="detail-status-badge normal" id="detailStatusBadge">-</div>

                <div class="detail-section">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <p class="detail-item-label">デバイスID</p>
                            <p class="detail-item-value mono" id="detailDeviceId">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">部屋番号</p>
                            <p class="detail-item-value" id="detailRoom">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">入居者名</p>
                            <p class="detail-item-value" id="detailTenant">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">最終検知</p>
                            <p class="detail-item-value" id="detailLastDetected">-</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">📊 デバイス状態</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <p class="detail-item-label">電池残量</p>
                            <p class="detail-item-value" id="detailBattery">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">電波強度</p>
                            <p class="detail-item-value" id="detailSignal">-</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">⚙️ 見守り設定</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <p class="detail-item-label">アラート時間</p>
                            <p class="detail-item-value" id="detailAlertHours">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">設置高さ</p>
                            <p class="detail-item-value" id="detailHeight">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">ペット除外</p>
                            <p class="detail-item-value" id="detailPetExclusion">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">見守り</p>
                            <p class="detail-item-value" id="detailAwayMode">-</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">📝 登録情報</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <p class="detail-item-label">登録日</p>
                            <p class="detail-item-value" id="detailRegistered">-</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-item-label">メモ</p>
                            <p class="detail-item-value" id="detailMemo">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('detailModal')">閉じる</button>
                <button class="btn btn-primary" id="detailEditBtn" onclick="openEditFromDetail()">編集</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: デバイス編集 ===== --}}
    <div id="editModal" class="modal-overlay" onclick="if(event.target===this)hideModal('editModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>✏️ デバイス編集</h3>
                <button class="modal-close" onclick="hideModal('editModal')">×</button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">デバイスID</label>
                        <input type="text" class="form-input" id="editDeviceId" disabled style="background:var(--gray-100);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">部屋番号</label>
                        <input type="text" class="form-input" name="room_number" id="editRoomNumber" placeholder="101">
                    </div>
                    <div class="form-group">
                        <label class="form-label">入居者名</label>
                        <input type="text" class="form-input" name="tenant_name" id="editTenantName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">メモ</label>
                        <input type="text" class="form-input" name="memo" id="editMemo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal('editModal')">キャンセル</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== モーダル: 見守りOFF確認 ===== --}}
    <div id="watchOffModal" class="modal-overlay" onclick="if(event.target===this)hideModal('watchOffModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>⚠️ 見守りをOFFにしますか？</h3>
                <button class="modal-close" onclick="hideModal('watchOffModal')">×</button>
            </div>
            <div class="modal-body">
                <p><strong>⚠️ 注意:</strong> OFFにすると、このデバイスの未検知アラートが送信されなくなります。</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="cancelWatchOff()">キャンセル</button>
                <button class="btn btn-danger" onclick="executeWatchOff()">OFFにする</button>
            </div>
        </div>
    </div>

    {{-- トースト --}}
    <div id="toast" class="toast"></div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// ===== ユーティリティ =====
function showModal(id) { document.getElementById(id).classList.add('show'); }
function hideModal(id) { document.getElementById(id).classList.remove('show'); }

function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast ' + type + ' show';
    setTimeout(function() { toast.classList.remove('show'); }, 3000);
}

// ===== ステータスフィルタ =====
function filterByStatus(status) {
    const url = new URL(window.location);
    if (url.searchParams.get('status') === status) {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }
    window.location = url;
}

// ===== デバイス追加 =====
function showAddDeviceModal() {
    showModal('addDeviceModal');
}

// ===== デバイス削除 =====
function confirmDelete(deviceId) {
    document.getElementById('deleteDeviceId').textContent = deviceId;
    document.getElementById('deleteForm').action = '/admin/org/devices/' + deviceId + '/remove';
    showModal('deleteModal');
}

// ===== 見守りトグル =====
let pendingToggleDevice = null;
let pendingToggleCheckbox = null;

function toggleWatch(deviceId, checked, checkbox) {
    if (!checked) {
        // OFFにする → 確認モーダル
        pendingToggleDevice = deviceId;
        pendingToggleCheckbox = checkbox;
        checkbox.checked = true; // 一旦戻す
        showModal('watchOffModal');
        return;
    }
    // ONにする → そのまま実行
    sendToggleWatch(deviceId, false);
}

function cancelWatchOff() {
    hideModal('watchOffModal');
    pendingToggleDevice = null;
    pendingToggleCheckbox = null;
}

function executeWatchOff() {
    if (pendingToggleDevice) {
        sendToggleWatch(pendingToggleDevice, true);
        if (pendingToggleCheckbox) {
            pendingToggleCheckbox.checked = false;
        }
    }
    hideModal('watchOffModal');
}

function sendToggleWatch(deviceId, awayMode) {
    fetch('/admin/org/devices/' + deviceId + '/toggle-watch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ away_mode: awayMode }),
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast('エラーが発生しました', 'error');
        }
    })
    .catch(function() {
        showToast('通信エラーが発生しました', 'error');
    });
}

// ===== デバイス詳細 =====
let currentDetailDeviceId = null;

function showDeviceDetail(deviceId) {
    currentDetailDeviceId = deviceId;

    fetch('/admin/org/devices/' + deviceId + '/detail', {
        headers: { 'Accept': 'application/json' },
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        // ステータスバッジ
        const badge = document.getElementById('detailStatusBadge');
        const statusLabels = { normal: '正常稼働中', warning: '注意', alert: '未検知警告', offline: '通信途絶' };
        badge.textContent = statusLabels[data.status] || data.status;
        badge.className = 'detail-status-badge ' + (data.status || 'offline');

        // 基本情報
        document.getElementById('detailDeviceId').textContent = data.device_id;
        document.getElementById('detailRoom').textContent = data.room_number || '-';
        document.getElementById('detailTenant').textContent = data.tenant_name || '-';
        document.getElementById('detailLastDetected').textContent = data.last_human_detected || '-';

        // デバイス状態
        document.getElementById('detailBattery').textContent = data.battery_pct !== null ? data.battery_pct + '%' : '-';
        var rssiLabel = '-';
        if (data.rssi !== null) {
            if (data.rssi > -70) rssiLabel = '良好 (' + data.rssi + 'dBm)';
            else if (data.rssi > -85) rssiLabel = '普通 (' + data.rssi + 'dBm)';
            else rssiLabel = '弱い (' + data.rssi + 'dBm)';
        }
        document.getElementById('detailSignal').textContent = rssiLabel;

        // 見守り設定
        document.getElementById('detailAlertHours').textContent = data.alert_threshold_hours + '時間';
        document.getElementById('detailHeight').textContent = data.install_height_cm + 'cm';
        document.getElementById('detailPetExclusion').textContent = data.pet_exclusion_enabled ? 'ON（' + data.pet_exclusion_threshold_cm + 'cm）' : 'OFF';
        var awayText = data.away_mode ? 'OFF（見守り停止中）' : 'ON';
        if (data.away_until) awayText += '（〜' + data.away_until + '）';
        document.getElementById('detailAwayMode').textContent = awayText;

        // 登録情報
        document.getElementById('detailRegistered').textContent = data.registered_at || '-';
        document.getElementById('detailMemo').textContent = data.memo || '-';

        showModal('detailModal');
    })
    .catch(function() {
        showToast('詳細の取得に失敗しました', 'error');
    });
}

// ===== 詳細 → 編集 =====
function openEditFromDetail() {
    if (!currentDetailDeviceId) return;
    hideModal('detailModal');

    // 詳細モーダルの値を編集モーダルに転記
    document.getElementById('editDeviceId').value = document.getElementById('detailDeviceId').textContent;
    var room = document.getElementById('detailRoom').textContent;
    var tenant = document.getElementById('detailTenant').textContent;
    var memo = document.getElementById('detailMemo').textContent;
    document.getElementById('editRoomNumber').value = (room !== '-') ? room : '';
    document.getElementById('editTenantName').value = (tenant !== '-') ? tenant : '';
    document.getElementById('editMemo').value = (memo !== '-') ? memo : '';
    document.getElementById('editForm').action = '/admin/org/devices/' + currentDetailDeviceId + '/assignment';

    showModal('editModal');
}

// ===== フラッシュメッセージ自動表示 =====
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast('{{ session("success") }}', 'success');
    @endif
    @if(session('error'))
        showToast('{{ session("error") }}', 'error');
    @endif
});
</script>
@endsection
