@extends('layouts.master')

@section('title', 'アカウント・課金管理')

@section('nav')
    <a href="{{ route('admin.dashboard') }}" class="header-nav-item active">アカウント管理</a>
    <a href="#" class="header-nav-item">デバイス発番</a>
    <a href="#" class="header-nav-item">システム設定</a>
@endsection

@section('styles')
    /* ===== ページタイトル ===== */
    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .page-title span { font-size: 24px; }

    /* ===== 通知バナー ===== */
    .notify-banner {
        background: var(--orange-light);
        border: 1px solid #fed7aa;
        border-left: 4px solid var(--orange);
        padding: 14px 20px;
        margin-bottom: 24px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .notify-banner.hidden { display: none; }
    .notify-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .notify-icon { font-size: 20px; }
    .notify-text { font-size: 14px; font-weight: 500; }
    .notify-text strong { color: var(--orange); font-weight: 700; }
    .notify-action {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: none;
        border-radius: var(--radius);
        background: var(--orange);
        color: var(--white);
        cursor: pointer;
        transition: all 0.2s;
    }
    .notify-action:hover { opacity: 0.9; }

    /* ===== サマリーグリッド ===== */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .summary-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .summary-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .summary-card-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .summary-card-icon.blue { background: var(--blue-light); }
    .summary-card-icon.green { background: var(--green-light); }
    .summary-card-icon.yellow { background: var(--yellow-light); }
    .summary-card-icon.purple { background: var(--purple-light); }
    .summary-card-icon.orange { background: var(--orange-light); }
    .summary-card-trend {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .summary-card-trend.up { background: var(--green-light); color: var(--green-dark); }
    .summary-card-trend.down { background: var(--red-light); color: var(--red); }
    .summary-card-trend.warning { background: var(--orange-light); color: var(--orange); }
    .summary-card-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 4px;
    }
    .summary-card-label {
        font-size: 13px;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* ===== タブ ===== */
    .tab-bar {
        display: flex;
        gap: 4px;
        background: var(--white);
        padding: 4px;
        border-radius: var(--radius-lg);
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .tab {
        flex: 1;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        color: var(--gray-500);
        background: transparent;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        font-family: inherit;
    }
    .tab.active { background: var(--gray-800); color: var(--white); }
    .tab:not(.active):hover { background: var(--beige); color: var(--gray-700); }
    .tab-badge {
        position: absolute;
        top: 6px;
        right: 12px;
        min-width: 20px;
        height: 20px;
        background: var(--orange);
        color: var(--white);
        font-size: 11px;
        font-weight: 700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== ツールバー ===== */
    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-box {
        display: flex;
        align-items: center;
        background: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        padding: 0 12px;
        width: 300px;
    }
    .search-box:focus-within {
        border-color: var(--gray-500);
        box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
    }
    .search-box input {
        flex: 1;
        padding: 10px 8px;
        border: none;
        background: transparent;
        font-size: 14px;
        font-family: inherit;
    }
    .search-box input:focus { outline: none; }
    .search-box span { color: var(--gray-400); }
    .filter-select {
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
        color: var(--gray-700);
        cursor: pointer;
        font-weight: 500;
    }
    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toolbar-btn {
        padding: 10px 16px;
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
        font-size: 14px;
    }
    thead { background: var(--beige); }
    th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
        border-bottom: 2px solid var(--gray-300);
        border-right: 1px solid var(--gray-200);
    }
    th:last-child { border-right: none; }
    td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-200);
        border-right: 1px solid var(--gray-100);
        vertical-align: middle;
    }
    td:last-child { border-right: none; }
    tbody tr:nth-child(odd) { background: var(--white); }
    tbody tr:nth-child(even) { background: var(--cream); }
    tbody tr:hover { background: var(--gray-100); }
    tbody tr:last-child td { border-bottom: none; }
    th.sortable { cursor: pointer; user-select: none; }
    th.sortable:hover { background: var(--gray-100); }
    .sort-icon { font-size: 12px; color: var(--gray-400); margin-left: 4px; }
    .mono { font-family: monospace; letter-spacing: 0.03em; }

    /* ===== バッジ類 ===== */
    .plan-badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
    }
    .plan-badge.free { background: var(--gray-100); color: var(--gray-600); }
    .plan-badge.premium {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: var(--white);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 4px;
    }
    .status-badge.active { background: var(--green-light); color: var(--green-dark); }
    .status-badge.expired { background: var(--red-light); color: var(--red); }
    .expiry-cell { display: flex; flex-direction: column; }
    .expiry-date { font-weight: 600; color: var(--gray-800); }
    .expiry-date.warning { color: var(--orange); }
    .expiry-date.expired { color: var(--red); }
    .expiry-remain { font-size: 11px; font-weight: 500; }
    .expiry-remain.ok { color: var(--green-dark); }
    .expiry-remain.warning { color: var(--orange); }
    .expiry-remain.expired { color: var(--red); }
    .payment-type {
        display: inline-block;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
    }
    .payment-type.card { background: var(--blue-light); color: var(--blue); }
    .payment-type.transfer { background: var(--green-light); color: var(--green-dark); }
    .action-btn {
        padding: 6px 12px;
        font-size: 12px;
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
    .action-btn.success {
        background: var(--green);
        color: var(--white);
        border-color: var(--green);
    }
    .action-btn.success:hover { background: var(--green-dark); }

    /* ===== ページネーション ===== */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 2px solid var(--gray-200);
        background: var(--cream);
    }
    .pagination-info {
        font-size: 13px;
        font-weight: 500;
        color: var(--gray-600);
    }
    .pagination-buttons {
        display: flex;
        gap: 4px;
    }
    .page-btn {
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
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
    .page-btn:disabled, .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== 振込カード ===== */
    .transfer-card {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 12px;
    }
    .transfer-card:last-child { margin-bottom: 0; }
    .transfer-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .transfer-card-id {
        font-family: monospace;
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-800);
    }
    .transfer-card-date {
        font-size: 12px;
        color: var(--gray-500);
    }
    .transfer-card-body {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .transfer-card-label {
        font-size: 11px;
        color: var(--gray-500);
        margin-bottom: 2px;
    }
    .transfer-card-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
    }
    .transfer-card-actions {
        display: flex;
        gap: 8px;
    }

    /* ===== 詳細グリッド ===== */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .detail-item {
        padding: 12px;
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

    /* ===== 発番セクション ===== */
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
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 14px;
        font-family: inherit;
        background: var(--cream);
        text-align: center;
    }
    .issue-input:focus {
        outline: none;
        border-color: var(--gray-500);
    }
    .issue-label {
        font-size: 12px;
        color: var(--gray-500);
        margin-bottom: 4px;
    }
    .issued-result {
        background: var(--green-light);
        border: 1px solid #bbf7d0;
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 16px;
    }
    .issued-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--green-dark);
        margin-bottom: 12px;
    }
    .issued-item {
        display: flex;
        gap: 24px;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #a7f3d0;
        font-size: 13px;
    }
    .issued-item:last-child { border-bottom: none; }
    .issued-item .label {
        color: var(--gray-600);
        min-width: 80px;
        font-weight: 500;
    }
    .issued-item .value {
        font-family: monospace;
        font-size: 15px;
        font-weight: 700;
        color: var(--green-dark);
        letter-spacing: 2px;
    }
    .issued-copy-btn {
        background: var(--green-dark);
        color: #fff;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        font-family: inherit;
    }
    .issued-copy-btn:hover { opacity: 0.85; }

    @media (max-width: 1200px) {
        .summary-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .summary-grid { grid-template-columns: 1fr 1fr; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .search-box { width: 100%; }
        .transfer-card-body { grid-template-columns: 1fr; }
        .tab { font-size: 12px; padding: 10px 8px; }
    }
@endsection

@section('content')
    {{-- 振込通知バナー --}}
    @if(($stats['pending_transfers'] ?? 0) > 0)
        <div class="notify-banner" id="transferBanner">
            <div class="notify-content">
                <span class="notify-icon">💰</span>
                <span class="notify-text">振込申請が <strong>{{ $stats['pending_transfers'] ?? 0 }}件</strong> あります。入金確認をお願いします。</span>
            </div>
            <button class="notify-action" onclick="switchTab('transfer')">確認する</button>
        </div>
    @endif

    <h1 class="page-title"><span>👥</span>アカウント・課金管理</h1>

    {{-- サマリーカード --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-header">
                <div class="summary-card-icon blue">📱</div>
                @if(($stats['new_this_month'] ?? 0) > 0)
                    <span class="summary-card-trend up">+{{ $stats['new_this_month'] }} 今月</span>
                @endif
            </div>
            <p class="summary-card-value">{{ number_format($stats['total'] ?? 0) }}</p>
            <p class="summary-card-label">総アカウント数</p>
        </div>
        <div class="summary-card">
            <div class="summary-card-header">
                <div class="summary-card-icon purple">👑</div>
            </div>
            <p class="summary-card-value">{{ number_format($stats['premium'] ?? 0) }}</p>
            <p class="summary-card-label">Premium会員</p>
        </div>
        <div class="summary-card">
            <div class="summary-card-header">
                <div class="summary-card-icon yellow">💰</div>
            </div>
            <p class="summary-card-value">¥{{ number_format($stats['monthly_revenue'] ?? 0) }}</p>
            <p class="summary-card-label">今月売上</p>
        </div>
        <div class="summary-card">
            <div class="summary-card-header">
                <div class="summary-card-icon orange">⏳</div>
                @if(($stats['pending_transfers'] ?? 0) > 0)
                    <span class="summary-card-trend warning">要対応</span>
                @endif
            </div>
            <p class="summary-card-value">{{ $stats['pending_transfers'] ?? 0 }}</p>
            <p class="summary-card-label">振込待ち</p>
        </div>
        <div class="summary-card">
            <div class="summary-card-header">
                <div class="summary-card-icon green">✓</div>
            </div>
            <p class="summary-card-value">{{ $stats['expiring_soon'] ?? 0 }}</p>
            <p class="summary-card-label">期限切れ間近</p>
        </div>
    </div>

    {{-- タブバー --}}
    <div class="tab-bar">
        <button class="tab active" onclick="switchTab('individual')">個人アカウント</button>
        <button class="tab" onclick="switchTab('org')">法人アカウント</button>
        <button class="tab" onclick="switchTab('expiring')">期限切れ間近</button>
        <button class="tab" onclick="switchTab('transfer')">
            振込管理
            @if(($stats['pending_transfers'] ?? 0) > 0)
                <span class="tab-badge">{{ $stats['pending_transfers'] }}</span>
            @endif
        </button>
    </div>

    {{-- ===== 個人アカウントタブ ===== --}}
    <div id="individualTab">
        <div class="toolbar">
            <div class="toolbar-left">
                <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <div class="search-box">
                        <span>🔍</span>
                        <input type="text" name="search" placeholder="デバイスID・メールで検索..." value="{{ request('search') }}">
                    </div>
                    <select name="plan" class="filter-select">
                        <option value="">すべてのプラン</option>
                        <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>無料</option>
                        <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                    <select name="payment" class="filter-select">
                        <option value="">すべての支払方法</option>
                        <option value="card" {{ request('payment') === 'card' ? 'selected' : '' }}>クレカ</option>
                        <option value="transfer" {{ request('payment') === 'transfer' ? 'selected' : '' }}>振込</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary">絞り込み</button>
                </form>
            </div>
            <div class="toolbar-right">
                <a href="#" class="toolbar-btn">📥 CSV出力</a>
            </div>
        </div>

        <div class="table-card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">デバイスID <span class="sort-icon">↕</span></th>
                            <th>メールアドレス</th>
                            <th>プラン</th>
                            <th>支払方法</th>
                            <th class="sortable">有効期限 <span class="sort-icon">↕</span></th>
                            <th>状態</th>
                            <th>登録日</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                            @php
                                $sub = $device->subscription;
                                $plan = $sub ? $sub->plan : 'free';
                                $email = $device->notificationSetting->email_1 ?? null;
                                $expiryDate = $sub && $sub->current_period_end ? \Carbon\Carbon::parse($sub->current_period_end) : null;
                                $daysLeft = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                            @endphp
                            <tr>
                                <td class="mono">{{ $device->device_id }}</td>
                                <td>{{ $email ?: '-' }}</td>
                                <td>
                                    <span class="plan-badge {{ $plan }}">{{ $plan === 'premium' ? 'Premium' : '無料' }}</span>
                                </td>
                                <td>
                                    @if($sub && $sub->billing_cycle)
                                        @if($sub->stripe_subscription_id)
                                            <span class="payment-type card">クレカ</span>
                                        @else
                                            <span class="payment-type transfer">振込</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="expiry-cell">
                                        @if($expiryDate)
                                            <span class="expiry-date {{ $daysLeft !== null && $daysLeft < 0 ? 'expired' : ($daysLeft !== null && $daysLeft <= 14 ? 'warning' : '') }}">
                                                {{ $expiryDate->format('Y/m/d') }}
                                            </span>
                                            <span class="expiry-remain {{ $daysLeft !== null && $daysLeft < 0 ? 'expired' : ($daysLeft !== null && $daysLeft <= 14 ? 'warning' : 'ok') }}">
                                                {{ $daysLeft !== null && $daysLeft < 0 ? '期限切れ' : 'あと' . $daysLeft . '日' }}
                                            </span>
                                        @else
                                            <span class="expiry-date">-</span>
                                            <span class="expiry-remain">無期限</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($daysLeft !== null && $daysLeft < 0)
                                        <span class="status-badge expired">● 期限切れ</span>
                                    @else
                                        <span class="status-badge active">● 有効</span>
                                    @endif
                                </td>
                                <td style="font-size:13px;">{{ $device->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <button class="action-btn"
                                        onclick="showPlanEditModal('{{ $device->device_id }}', '{{ $plan }}', '{{ $expiryDate ? $expiryDate->format('Y-m-d') : '' }}', '{{ $sub && $sub->stripe_subscription_id ? 'card' : ($sub && $sub->billing_cycle ? 'transfer' : '') }}')">
                                        編集
                                    </button>
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
                        全{{ $devices->total() }}件中 {{ $devices->firstItem() }}-{{ $devices->lastItem() }}件を表示
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
    </div>

    {{-- ===== 法人アカウントタブ ===== --}}
    <div id="orgTab" style="display: none;">
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-box">
                    <span>🔍</span>
                    <input type="text" placeholder="組織名・IDで検索..." id="orgSearchInput">
                </div>
            </div>
            <div class="toolbar-right">
                <button class="toolbar-btn" onclick="showAddOrgModal()">➕ 法人追加</button>
            </div>
        </div>

        <div class="table-card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>組織ID</th>
                            <th>組織名</th>
                            <th>デバイス数</th>
                            <th>有効期限</th>
                            <th>状態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations ?? [] as $org)
                            <tr>
                                <td class="mono">ORG-{{ str_pad($org->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $org->name }}</td>
                                <td>{{ $org->devices_count ?? 0 }}台</td>
                                <td>
                                    <div class="expiry-cell">
                                        <span class="expiry-date">{{ $org->expires_at ? \Carbon\Carbon::parse($org->expires_at)->format('Y/m/d') : '-' }}</span>
                                    </div>
                                </td>
                                <td><span class="status-badge active">● 有効</span></td>
                                <td>
                                    <button class="action-btn">編集</button>
                                    <button class="action-btn">詳細</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--gray-400);padding:40px;">法人アカウントがありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== 期限切れ間近タブ ===== --}}
    <div id="expiringTab" style="display: none;">
        <div class="toolbar">
            <div class="toolbar-left">
                <select class="filter-select" id="expiringDays">
                    <option value="7">7日以内</option>
                    <option value="14">14日以内</option>
                    <option value="30" selected>30日以内</option>
                </select>
            </div>
        </div>

        <div class="table-card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>デバイスID</th>
                            <th>メールアドレス</th>
                            <th>プラン</th>
                            <th>支払方法</th>
                            <th class="sortable">有効期限 <span class="sort-icon">↕</span></th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringDevices ?? [] as $device)
                            @php
                                $sub = $device->subscription;
                                $email = $device->notificationSetting->email_1 ?? null;
                                $expiryDate = $sub && $sub->current_period_end ? \Carbon\Carbon::parse($sub->current_period_end) : null;
                                $daysLeft = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                            @endphp
                            <tr>
                                <td class="mono">{{ $device->device_id }}</td>
                                <td>{{ $email ?: '-' }}</td>
                                <td><span class="plan-badge premium">Premium</span></td>
                                <td>
                                    @if($sub && $sub->stripe_subscription_id)
                                        <span class="payment-type card">クレカ</span>
                                    @else
                                        <span class="payment-type transfer">振込</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="expiry-cell">
                                        <span class="expiry-date {{ $daysLeft !== null && $daysLeft < 0 ? 'expired' : 'warning' }}">
                                            {{ $expiryDate ? $expiryDate->format('Y/m/d') : '-' }}
                                        </span>
                                        <span class="expiry-remain {{ $daysLeft !== null && $daysLeft < 0 ? 'expired' : 'warning' }}">
                                            {{ $daysLeft !== null && $daysLeft < 0 ? '期限切れ' : 'あと' . $daysLeft . '日' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn"
                                        onclick="showPlanEditModal('{{ $device->device_id }}', 'Premium', '{{ $expiryDate ? $expiryDate->format('Y-m-d') : '' }}', '{{ $sub && $sub->stripe_subscription_id ? 'card' : 'transfer' }}')">
                                        編集
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--gray-400);padding:40px;">期限切れ間近のアカウントはありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== 振込管理タブ ===== --}}
    <div id="transferTab" style="display: none;">
        <div class="toolbar">
            <div class="toolbar-left">
                <select class="filter-select" id="transferFilter">
                    <option value="pending">入金待ち</option>
                    <option value="confirmed">入金済み</option>
                    <option value="all">すべて</option>
                </select>
            </div>
        </div>

        @forelse($pendingTransfers ?? [] as $transfer)
            <div class="transfer-card">
                <div class="transfer-card-header">
                    <span class="transfer-card-id">{{ $transfer->device->device_id ?? '-' }}</span>
                    <span class="transfer-card-date">申請日: {{ $transfer->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <div class="transfer-card-body">
                    <div>
                        <p class="transfer-card-label">メールアドレス</p>
                        <p class="transfer-card-value">{{ $transfer->device->notificationSetting->email_1 ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="transfer-card-label">申請プラン</p>
                        <p class="transfer-card-value">Premium（{{ $transfer->billing_cycle === 'yearly' ? '年払い' : '月払い' }}）</p>
                    </div>
                    <div>
                        <p class="transfer-card-label">金額</p>
                        <p class="transfer-card-value">¥{{ number_format($transfer->billing_cycle === 'yearly' ? 3000 : 500) }}</p>
                    </div>
                </div>
                <div class="transfer-card-actions">
                    <button class="action-btn success" onclick="confirmTransfer('{{ $transfer->device->device_id ?? '' }}')">✓ 入金確認</button>
                    <button class="action-btn" onclick="cancelTransfer('{{ $transfer->device->device_id ?? '' }}')">キャンセル</button>
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center;color:var(--gray-400);padding:40px;">
                振込待ちの申請はありません
            </div>
        @endforelse
    </div>

    {{-- ===== デバイス発番セクション ===== --}}
    <div class="card" style="margin-top:24px;">
        <div class="card-title">🔧 デバイス発番</div>
        <div class="issue-section">
            <form method="POST" action="{{ route('admin.issue') }}" class="issue-form">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">1台発番</button>
            </form>
            <form method="POST" action="{{ route('admin.issue-bulk') }}" class="issue-form">
                @csrf
                <div>
                    <div class="issue-label">台数</div>
                    <input type="number" name="count" class="issue-input" value="5" min="1" max="100">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">一括発番</button>
            </form>
        </div>
        @error('count')
            <div style="color:var(--red);font-size:12px;margin-top:8px;">{{ $message }}</div>
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
                    <span style="color:var(--gray-400);margin:0 8px;">/</span>
                    <span class="value">{{ $item['pin'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===== モーダル: プラン編集 ===== --}}
    <div id="planEditModal" class="modal-overlay" onclick="if(event.target===this)hidePlanEditModal()">
        <div class="modal">
            <div class="modal-header">
                <h3>プラン・有効期限の編集</h3>
                <button class="modal-close" onclick="hidePlanEditModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid" style="margin-bottom: 20px;">
                    <div class="detail-item">
                        <p class="detail-item-label">デバイスID</p>
                        <p class="detail-item-value" id="editDeviceId">-</p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-item-label">現在のプラン</p>
                        <p class="detail-item-value" id="editCurrentPlan">-</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">プラン</label>
                    <select class="form-input" id="editPlan">
                        <option value="free">無料プラン</option>
                        <option value="premium">Premiumプラン</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">支払い方法</label>
                    <select class="form-input" id="editPaymentType">
                        <option value="">-（無料プランの場合）</option>
                        <option value="card">クレジットカード</option>
                        <option value="transfer">銀行振込</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">有効期限</label>
                    <input type="date" class="form-input" id="editExpiry">
                    <p class="form-hint">無料プランの場合は空欄でOK。有料プランは必ず設定。</p>
                </div>
                <div class="form-group">
                    <label class="form-label">メモ（任意）</label>
                    <input type="text" class="form-input" id="editMemo" placeholder="例：振込確認済み、特別対応など">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hidePlanEditModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="savePlanEdit()">保存</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: 入金確認 ===== --}}
    <div id="transferConfirmModal" class="modal-overlay" onclick="if(event.target===this)hideTransferConfirmModal()">
        <div class="modal">
            <div class="modal-header">
                <h3>入金確認</h3>
                <button class="modal-close" onclick="hideTransferConfirmModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid" style="margin-bottom: 20px;">
                    <div class="detail-item">
                        <p class="detail-item-label">デバイスID</p>
                        <p class="detail-item-value" id="confirmDeviceId">-</p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-item-label">金額</p>
                        <p class="detail-item-value" id="confirmAmount">-</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">有効期限を設定</label>
                    <input type="date" class="form-input" id="confirmExpiry">
                    <p class="form-hint">年払いなら1年後を設定</p>
                </div>
                <div class="form-group">
                    <label class="form-label">入金日</label>
                    <input type="date" class="form-input" id="confirmPaymentDate">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideTransferConfirmModal()">キャンセル</button>
                <button class="btn btn-success" onclick="executeTransferConfirm()">入金確認を完了</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: 法人追加 ===== --}}
    <div id="addOrgModal" class="modal-overlay" onclick="if(event.target===this)hideAddOrgModal()">
        <div class="modal" style="max-width: 600px;">
            <div class="modal-header">
                <h3>法人アカウント追加</h3>
                <button class="modal-close" onclick="hideAddOrgModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">組織名 *</label>
                    <input type="text" class="form-input" id="orgName" placeholder="例：〇〇マンション管理組合">
                </div>
                <div class="form-group">
                    <label class="form-label">組織タイプ</label>
                    <select class="form-input" id="orgType">
                        <option value="mansion">マンション管理組合</option>
                        <option value="realtor">不動産会社</option>
                        <option value="senior">高齢者住宅</option>
                        <option value="care">介護事業者</option>
                        <option value="other">その他</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">デバイス上限数</label>
                    <input type="number" class="form-input" id="orgDeviceLimit" value="50" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">有効期限 *</label>
                    <input type="date" class="form-input" id="orgExpiry">
                </div>
                <div style="border-top: 2px solid var(--gray-200); margin: 24px 0; padding-top: 20px;">
                    <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <span>👤</span>管理者アカウント（admin）
                    </h4>
                    <div class="form-group">
                        <label class="form-label">管理者メールアドレス *</label>
                        <input type="email" class="form-input" id="adminEmail" placeholder="admin@example.com">
                        <p class="form-hint">この宛先にログイン情報を送信します</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">管理者名</label>
                        <input type="text" class="form-input" id="adminName" placeholder="例：山田太郎">
                    </div>
                    <div class="form-group">
                        <label class="form-label">初期パスワード</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" class="form-input" id="adminPassword" placeholder="自動生成されます" style="flex: 1;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="generatePassword()" style="white-space: nowrap;">生成</button>
                        </div>
                        <p class="form-hint">初回ログイン後に変更を促します</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideAddOrgModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="addOrg()">作成</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// タブ切り替え
function switchTab(tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('individualTab').style.display = tab === 'individual' ? 'block' : 'none';
    document.getElementById('orgTab').style.display = tab === 'org' ? 'block' : 'none';
    document.getElementById('expiringTab').style.display = tab === 'expiring' ? 'block' : 'none';
    document.getElementById('transferTab').style.display = tab === 'transfer' ? 'block' : 'none';
}

// プラン編集モーダル
let currentEditId = '';
function showPlanEditModal(deviceId, plan, expiry, paymentType) {
    currentEditId = deviceId;
    document.getElementById('editDeviceId').textContent = deviceId;
    document.getElementById('editCurrentPlan').textContent = plan === 'premium' ? 'Premium' : '無料';
    document.getElementById('editPlan').value = plan;
    document.getElementById('editPaymentType').value = paymentType || '';
    document.getElementById('editExpiry').value = expiry || '';
    document.getElementById('planEditModal').classList.add('show');
}
function hidePlanEditModal() {
    document.getElementById('planEditModal').classList.remove('show');
}
function savePlanEdit() {
    const plan = document.getElementById('editPlan').value;
    const expiry = document.getElementById('editExpiry').value;
    if (plan === 'premium' && !expiry) {
        alert('有料プランの場合は有効期限を設定してください');
        return;
    }
    alert(currentEditId + ' のプラン情報を更新しました');
    hidePlanEditModal();
}

// 入金確認モーダル
let currentTransferId = '';
function confirmTransfer(deviceId) {
    currentTransferId = deviceId;
    document.getElementById('confirmDeviceId').textContent = deviceId;
    const oneYearLater = new Date();
    oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
    document.getElementById('confirmExpiry').value = oneYearLater.toISOString().split('T')[0];
    document.getElementById('confirmPaymentDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('transferConfirmModal').classList.add('show');
}
function hideTransferConfirmModal() {
    document.getElementById('transferConfirmModal').classList.remove('show');
}
function executeTransferConfirm() {
    const expiry = document.getElementById('confirmExpiry').value;
    const paymentDate = document.getElementById('confirmPaymentDate').value;
    if (!expiry || !paymentDate) {
        alert('有効期限と入金日を入力してください');
        return;
    }
    alert(currentTransferId + ' の入金確認を完了しました\n有効期限: ' + expiry);
    hideTransferConfirmModal();
}
function cancelTransfer(deviceId) {
    if (confirm(deviceId + ' の振込申請をキャンセルしますか？')) {
        alert('キャンセルしました');
    }
}

// 法人追加モーダル
function showAddOrgModal() {
    document.getElementById('orgName').value = '';
    document.getElementById('orgType').value = 'mansion';
    document.getElementById('orgDeviceLimit').value = '50';
    document.getElementById('adminEmail').value = '';
    document.getElementById('adminName').value = '';
    document.getElementById('adminPassword').value = '';
    const oneYearLater = new Date();
    oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
    document.getElementById('orgExpiry').value = oneYearLater.toISOString().split('T')[0];
    generatePassword();
    document.getElementById('addOrgModal').classList.add('show');
}
function hideAddOrgModal() {
    document.getElementById('addOrgModal').classList.remove('show');
}
function generatePassword() {
    const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('adminPassword').value = password;
}
function addOrg() {
    const orgName = document.getElementById('orgName').value;
    const adminEmail = document.getElementById('adminEmail').value;
    if (!orgName || !adminEmail) {
        alert('組織名と管理者メールアドレスは必須です');
        return;
    }
    alert('法人アカウント「' + orgName + '」を作成しました');
    hideAddOrgModal();
}

// コピー
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
