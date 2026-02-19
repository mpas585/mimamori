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

    /* ===== 警告解除ボタン ===== */
    .clear-alert-btn {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 600;
        font-family: inherit;
        color: var(--red);
        background: var(--white);
        border: 1px solid var(--red-light);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 6px;
        white-space: nowrap;
    }
    .clear-alert-btn:hover {
        background: var(--red-light);
        border-color: var(--red);
    }
    .detail-clear-alert-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        color: var(--red);
        background: var(--white);
        border: 1px solid var(--red-light);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 10px;
        vertical-align: middle;
    }
    .detail-clear-alert-btn:hover {
        background: var(--red-light);
        border-color: var(--red);
    }

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
    .detail-status-row {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
        gap: 0;
    }
    .detail-status-badge {
        display: inline-block;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
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

    /* ===== タイマー一覧モーダル ===== */
    .timer-list-loading {
        padding: 40px 20px;
        text-align: center;
        color: var(--gray-400);
        font-size: 13px;
    }
    .timer-list-empty {
        padding: 40px 20px;
        text-align: center;
        color: var(--gray-400);
        font-size: 13px;
    }
    .timer-device-group {
        margin-bottom: 16px;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .timer-device-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: var(--beige);
        border-bottom: 1px solid var(--gray-200);
    }
    .timer-device-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .timer-device-room {
        font-size: 14px;
        font-weight: 700;
        color: var(--gray-800);
    }
    .timer-device-name {
        font-size: 12px;
        color: var(--gray-500);
    }
    .timer-device-id {
        font-family: monospace;
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        background: var(--white);
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid var(--gray-200);
    }
    .timer-away-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        background: var(--yellow-light);
        color: #a16207;
    }
    .timer-schedule-item {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        border-bottom: 1px solid var(--gray-100);
        font-size: 13px;
    }
    .timer-schedule-item:last-child { border-bottom: none; }
    .timer-schedule-item:nth-child(even) { background: var(--cream); }
    .timer-schedule-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 14px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .timer-schedule-icon.oneshot { background: #eff6ff; }
    .timer-schedule-icon.recurring { background: #f0fdf4; }
    .timer-schedule-info { flex: 1; }
    .timer-schedule-main {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 2px;
    }
    .timer-schedule-sub {
        font-size: 11px;
        color: var(--gray-500);
    }
    .timer-schedule-type {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 3px;
        margin-left: 8px;
        flex-shrink: 0;
    }
    .timer-schedule-type.oneshot { background: #eff6ff; color: #1d4ed8; }
    .timer-schedule-type.recurring { background: #f0fdf4; color: #15803d; }
    .timer-summary {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .timer-summary-item {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 10px 14px;
        flex: 1;
        min-width: 120px;
        text-align: center;
    }
    .timer-summary-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-800);
    }
    .timer-summary-label {
        font-size: 11px;
        color: var(--gray-500);
        margin-top: 2px;
    }
    .timer-add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        width: 100%;
        padding: 8px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        color: var(--gray-500);
        background: var(--cream);
        border: 1px dashed var(--gray-300);
        border-radius: 0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .timer-add-btn:hover { background: var(--beige); color: var(--gray-700); }
    .timer-delete-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: var(--gray-400);
        background: transparent;
        border: 1px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 8px;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .timer-delete-btn:hover { color: var(--red); background: var(--red-light); border-color: var(--red-light); }
    .schedule-form-group { margin-bottom: 14px; }
    .schedule-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 4px;
    }
    .schedule-form-group input,
    .schedule-form-group select {
        width: 100%;
        padding: 8px 10px;
        font-size: 13px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
    }
    .schedule-form-group input:focus,
    .schedule-form-group select:focus {
        outline: none;
        border-color: var(--gray-500);
        box-shadow: 0 0 0 3px rgba(168,162,158,0.15);
    }
    .schedule-type-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }
    .schedule-type-tab {
        flex: 1;
        padding: 10px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        background: var(--white);
        cursor: pointer;
        transition: all 0.2s;
        color: var(--gray-600);
    }
    .schedule-type-tab.active {
        border-color: var(--gray-800);
        background: var(--beige);
        color: var(--gray-800);
    }
    .schedule-type-tab:hover:not(.active) { background: var(--cream); }
    .schedule-days {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .schedule-day-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: 2px solid var(--gray-200);
        border-radius: 50%;
        background: var(--white);
        cursor: pointer;
        transition: all 0.2s;
        color: var(--gray-600);
    }
    .schedule-day-btn.active {
        border-color: var(--gray-800);
        background: var(--gray-800);
        color: var(--white);
    }
    .schedule-day-btn:hover:not(.active) { background: var(--cream); }
    .schedule-time-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .schedule-time-row input { width: auto; flex: 1; }
    .schedule-time-row span { font-size: 13px; color: var(--gray-500); white-space: nowrap; }
    .schedule-nextday-check {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: var(--gray-600);
    }
    .schedule-device-label {
        font-size: 12px;
        color: var(--gray-500);
        margin-bottom: 12px;
        padding: 8px 12px;
        background: var(--beige);
        border-radius: var(--radius);
    }
    .schedule-device-label strong {
        color: var(--gray-800);
    }
    .detail-schedule-list {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 10px;
    }
    .detail-schedule-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-bottom: 1px solid var(--gray-100);
        font-size: 13px;
    }
    .detail-schedule-item:last-child { border-bottom: none; }
    .detail-schedule-item:nth-child(even) { background: var(--cream); }
    .detail-schedule-icon {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 13px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .detail-schedule-icon.oneshot { background: #eff6ff; }
    .detail-schedule-icon.recurring { background: #f0fdf4; }
    .detail-schedule-info { flex: 1; min-width: 0; }
    .detail-schedule-main {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-800);
    }
    .detail-schedule-sub {
        font-size: 11px;
        color: var(--gray-500);
    }
    .detail-schedule-del {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: var(--gray-400);
        background: transparent;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .detail-schedule-del:hover { color: var(--red); background: var(--red-light); }
    .detail-schedule-empty {
        padding: 16px;
        text-align: center;
        font-size: 12px;
        color: var(--gray-400);
    }
    .detail-schedule-add {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        width: 100%;
        padding: 8px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        color: var(--gray-500);
        background: var(--cream);
        border: 1px dashed var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
    }
    .detail-schedule-add:hover { background: var(--beige); color: var(--gray-700); }

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
            <button class="toolbar-btn" onclick="showNotificationModal()">🔔 通知設定</button>
            <button class="toolbar-btn" onclick="showTimerListModal()">⏰ タイマー一覧</button>
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
                        <tr id="row-{{ $device->device_id }}">
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
                                        <button class="clear-alert-btn" onclick="confirmClearAlert('{{ $device->device_id }}', '{{ $roomNumber }}', '{{ $tenantName }}')" title="警告を解除してステータスを初期化">✕ 解除</button>
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

    {{-- ===== モーダル: 警告解除確認 ===== --}}
    <div id="clearAlertModal" class="modal-overlay" onclick="if(event.target===this)hideModal('clearAlertModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>⚠️ 警告解除</h3>
                <button class="modal-close" onclick="hideModal('clearAlertModal')">×</button>
            </div>
            <div class="modal-body">
                <p id="clearAlertTarget" style="margin-bottom:8px;"></p>
                <p>このデバイスの警告を解除しますか？</p>
                <p style="color:var(--gray-500);font-size:13px;margin-top:8px;">
                    ステータスが初期状態（-）に戻り、検知ログもクリアされます。<br>
                    退去・長期不在等でデバイスを初期化する場合にご利用ください。
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('clearAlertModal')">キャンセル</button>
                <button class="btn btn-danger" onclick="executeClearAlert()">警告を解除する</button>
            </div>
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
                <div class="detail-status-row">
                    <div class="detail-status-badge normal" id="detailStatusBadge">-</div>
                    <button class="detail-clear-alert-btn" id="detailClearAlertBtn" style="display:none;" onclick="confirmClearAlertFromDetail()">✕ 警告解除</button>
                </div>

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

                <div class="detail-section">
                    <div class="detail-section-title">📅 スケジュール</div>
                    <div id="detailScheduleList"></div>
                    <button class="detail-schedule-add" id="detailScheduleAddBtn" onclick="openScheduleAddFromDetail()">＋ スケジュール追加</button>
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

    {{-- ===== モーダル: タイマー一覧 ===== --}}
    <div id="timerListModal" class="modal-overlay" onclick="if(event.target===this)hideModal('timerListModal')">
        <div class="modal" style="max-width:620px;">
            <div class="modal-header">
                <h3>⏰ タイマー一覧</h3>
                <button class="modal-close" onclick="hideModal('timerListModal')">×</button>
            </div>
            <div class="modal-body" id="timerListBody">
                <div class="timer-list-loading">読み込み中...</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('timerListModal')">閉じる</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: スケジュール追加 ===== --}}
    <div id="scheduleAddModal" class="modal-overlay" onclick="if(event.target===this)hideScheduleAddModal()">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <h3>📅 スケジュール追加</h3>
                <button class="modal-close" onclick="hideScheduleAddModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="schedule-device-label" id="scheduleDeviceLabel">
                    対象: <strong>-</strong>
                </div>

                <div class="schedule-type-tabs">
                    <button class="schedule-type-tab active" id="tabOneshot" onclick="switchScheduleType('oneshot')">📅 単発</button>
                    <button class="schedule-type-tab" id="tabRecurring" onclick="switchScheduleType('recurring')">🔁 定期</button>
                </div>

                {{-- 単発フォーム --}}
                <div id="oneshotForm">
                    <div class="schedule-form-group">
                        <label>開始日時</label>
                        <input type="datetime-local" id="schedStartAt">
                    </div>
                    <div class="schedule-form-group">
                        <label>終了日時（空欄＝手動復帰）</label>
                        <input type="datetime-local" id="schedEndAt">
                    </div>
                </div>

                {{-- 定期フォーム --}}
                <div id="recurringForm" style="display:none;">
                    <div class="schedule-form-group">
                        <label>曜日</label>
                        <div class="schedule-days" id="scheduleDays">
                            <button type="button" class="schedule-day-btn" data-day="0" onclick="toggleDay(this)">日</button>
                            <button type="button" class="schedule-day-btn" data-day="1" onclick="toggleDay(this)">月</button>
                            <button type="button" class="schedule-day-btn" data-day="2" onclick="toggleDay(this)">火</button>
                            <button type="button" class="schedule-day-btn" data-day="3" onclick="toggleDay(this)">水</button>
                            <button type="button" class="schedule-day-btn" data-day="4" onclick="toggleDay(this)">木</button>
                            <button type="button" class="schedule-day-btn" data-day="5" onclick="toggleDay(this)">金</button>
                            <button type="button" class="schedule-day-btn" data-day="6" onclick="toggleDay(this)">土</button>
                        </div>
                    </div>
                    <div class="schedule-form-group">
                        <label>時間帯</label>
                        <div class="schedule-time-row">
                            <input type="time" id="schedStartTime">
                            <span>〜</span>
                            <input type="time" id="schedEndTime">
                        </div>
                        <label class="schedule-nextday-check">
                            <input type="checkbox" id="schedNextDay"> 翌日にまたがる
                        </label>
                    </div>
                </div>

                {{-- 共通 --}}
                <div class="schedule-form-group">
                    <label>メモ（任意）</label>
                    <input type="text" id="schedMemo" placeholder="例: デイサービス" maxlength="200">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideScheduleAddModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="submitSchedule()">追加</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: スケジュール削除確認 ===== --}}
    <div id="scheduleDeleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('scheduleDeleteModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>⚠️ スケジュール削除</h3>
                <button class="modal-close" onclick="hideModal('scheduleDeleteModal')">×</button>
            </div>
            <div class="modal-body">
                <p>このスケジュールを削除しますか？</p>
                <p id="scheduleDeleteDetail" style="color:var(--gray-500);font-size:13px;margin-top:8px;"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('scheduleDeleteModal')">キャンセル</button>
                <button class="btn btn-danger" onclick="executeDeleteSchedule()">削除する</button>
            </div>
        </div>
    </div>

    {{-- ===== モーダル: 組織通知設定 ===== --}}
    @include('admin.partials.notification-modal')

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

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
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

// ===== 警告解除 =====
let clearAlertDeviceId = null;

function confirmClearAlert(deviceId, roomNumber, tenantName) {
    clearAlertDeviceId = deviceId;
    var label = '';
    if (roomNumber) label += roomNumber + ' ';
    if (tenantName) label += tenantName + ' ';
    label += '（' + deviceId + '）';
    document.getElementById('clearAlertTarget').innerHTML = '対象: <strong class="mono">' + escapeHtml(label) + '</strong>';
    showModal('clearAlertModal');
}

function confirmClearAlertFromDetail() {
    if (!currentDetailDeviceId) return;
    hideModal('detailModal');
    confirmClearAlert(currentDetailDeviceId, currentDetailRoomNumber, currentDetailTenantName);
}

function executeClearAlert() {
    if (!clearAlertDeviceId) return;

    fetch('/admin/org/devices/' + clearAlertDeviceId + '/clear-alert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            showToast(data.message, 'success');
            hideModal('clearAlertModal');
            // ページをリロードして最新状態を反映
            setTimeout(function() { location.reload(); }, 500);
        } else {
            showToast(data.message || 'エラーが発生しました', 'error');
        }
    })
    .catch(function() {
        showToast('通信エラーが発生しました', 'error');
    });
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

        // 警告解除ボタンの表示/非表示
        var clearBtn = document.getElementById('detailClearAlertBtn');
        if (data.status === 'alert') {
            clearBtn.style.display = 'inline-flex';
        } else {
            clearBtn.style.display = 'none';
        }

        // 基本情報
        document.getElementById('detailDeviceId').textContent = data.device_id;
        document.getElementById('detailRoom').textContent = data.room_number || '-';
        document.getElementById('detailTenant').textContent = data.tenant_name || '-';
        document.getElementById('detailLastDetected').textContent = data.last_human_detected || '-';
        currentDetailRoomNumber = data.room_number || '';
        currentDetailTenantName = data.tenant_name || '';

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

        // スケジュール一覧
        renderDetailSchedules(data.schedules || [], data.device_id);

        showModal('detailModal');
    })
    .catch(function() {
        showToast('詳細の取得に失敗しました', 'error');
    });
}

// ===== 詳細 → 編集 =====
let currentDetailRoomNumber = '';
let currentDetailTenantName = '';

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

// ===== 詳細モーダル内スケジュール描画 =====
function renderDetailSchedules(schedules, deviceId) {
    var container = document.getElementById('detailScheduleList');
    if (!schedules || schedules.length === 0) {
        container.innerHTML = '<div class="detail-schedule-empty">スケジュールなし</div>';
        return;
    }

    var html = '<div class="detail-schedule-list">';
    schedules.forEach(function(s) {
        html += '<div class="detail-schedule-item">';
        if (s.type === 'oneshot') {
            html += '<div class="detail-schedule-icon oneshot">📅</div>';
            html += '<div class="detail-schedule-info">';
            html += '<p class="detail-schedule-main">' + formatTimerDateTime(s.start_at) + ' 〜 ' + (s.end_at ? formatTimerDateTime(s.end_at) : '手動復帰') + '</p>';
            html += '<p class="detail-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '単発') + '</p>';
            html += '</div>';
        } else {
            html += '<div class="detail-schedule-icon recurring">🔁</div>';
            html += '<div class="detail-schedule-info">';
            var timeStr = s.start_time + '〜' + (s.next_day ? '翌' : '') + s.end_time;
            html += '<p class="detail-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + timeStr + '</p>';
            html += '<p class="detail-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '定期') + '</p>';
            html += '</div>';
        }
        html += '<button class="detail-schedule-del" onclick="confirmDeleteSchedule(\'' + escapeHtml(deviceId) + '\',' + s.id + ',\'' + escapeHtml(s.type === 'oneshot' ? formatTimerDateTime(s.start_at) : s.days_label) + '\')" title="削除">×</button>';
        html += '</div>';
    });
    html += '</div>';
    container.innerHTML = html;
}

// ===== 詳細モーダルからスケジュール追加 =====
let scheduleAddOrigin = null; // 'detail' or 'timerlist'

function openScheduleAddFromDetail() {
    scheduleAddOrigin = 'detail';
    openScheduleAddModal(currentDetailDeviceId, currentDetailRoomNumber, currentDetailTenantName);
}

// ===== タイマー一覧 =====
function showTimerListModal() {
    showModal('timerListModal');
    loadTimerList();
}

async function loadTimerList() {
    const body = document.getElementById('timerListBody');
    body.innerHTML = '<div class="timer-list-loading">読み込み中...</div>';

    try {
        const res = await fetch('{{ route("admin.org.timers") }}', {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) {
            body.innerHTML = '<div class="timer-list-empty">データの取得に失敗しました</div>';
            return;
        }

        const data = await res.json();

        if (data.length === 0) {
            body.innerHTML = '<div class="timer-list-empty">タイマーが設定されているデバイスはありません</div>';
            return;
        }

        // サマリー集計
        let awayCount = 0;
        let oneshotCount = 0;
        let recurringCount = 0;
        data.forEach(function(d) {
            if (d.away_mode) awayCount++;
            d.schedules.forEach(function(s) {
                if (s.type === 'oneshot') oneshotCount++;
                else recurringCount++;
            });
        });

        let html = '';

        // サマリー
        html += '<div class="timer-summary">';
        html += '<div class="timer-summary-item"><div class="timer-summary-value">' + data.length + '</div><div class="timer-summary-label">対象デバイス</div></div>';
        html += '<div class="timer-summary-item"><div class="timer-summary-value">' + awayCount + '</div><div class="timer-summary-label">見守りOFF中</div></div>';
        html += '<div class="timer-summary-item"><div class="timer-summary-value">' + oneshotCount + '</div><div class="timer-summary-label">単発予定</div></div>';
        html += '<div class="timer-summary-item"><div class="timer-summary-value">' + recurringCount + '</div><div class="timer-summary-label">定期スケジュール</div></div>';
        html += '</div>';

        // デバイスごとのグループ
        data.forEach(function(d) {
            html += '<div class="timer-device-group">';
            html += '<div class="timer-device-header">';
            html += '<div class="timer-device-info">';
            if (d.room_number) html += '<span class="timer-device-room">' + escapeHtml(d.room_number) + '</span>';
            if (d.tenant_name) html += '<span class="timer-device-name">' + escapeHtml(d.tenant_name) + '</span>';
            html += '<span class="timer-device-id">' + escapeHtml(d.device_id) + '</span>';
            html += '</div>';
            if (d.away_mode) {
                html += '<span class="timer-away-badge">⏸ 見守りOFF';
                if (d.away_until) html += '（〜' + formatTimerDateTime(d.away_until) + '）';
                html += '</span>';
            }
            html += '</div>';

            if (d.schedules.length > 0) {
                d.schedules.forEach(function(s) {
                    html += '<div class="timer-schedule-item">';
                    if (s.type === 'oneshot') {
                        html += '<div class="timer-schedule-icon oneshot">📅</div>';
                        html += '<div class="timer-schedule-info">';
                        html += '<p class="timer-schedule-main">' + formatTimerDateTime(s.start_at) + ' 〜 ' + (s.end_at ? formatTimerDateTime(s.end_at) : '手動復帰') + '</p>';
                        html += '<p class="timer-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '（メモなし）') + '</p>';
                        html += '</div>';
                        html += '<span class="timer-schedule-type oneshot">単発</span>';
                    } else {
                        html += '<div class="timer-schedule-icon recurring">🔁</div>';
                        html += '<div class="timer-schedule-info">';
                        var timeStr = s.start_time + '〜' + (s.next_day ? '翌' : '') + s.end_time;
                        html += '<p class="timer-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + timeStr + '</p>';
                        html += '<p class="timer-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '（メモなし）') + '</p>';
                        html += '</div>';
                        html += '<span class="timer-schedule-type recurring">定期</span>';
                    }
                    html += '<button class="timer-delete-btn" onclick="confirmDeleteSchedule(\'' + escapeHtml(d.device_id) + '\',' + s.id + ',\'' + escapeHtml(s.type === 'oneshot' ? formatTimerDateTime(s.start_at) : s.days_label) + '\')" title="削除">×</button>';
                    html += '</div>';
                });
            } else if (d.away_mode) {
                html += '<div class="timer-schedule-item">';
                html += '<div class="timer-schedule-icon oneshot">⏸</div>';
                html += '<div class="timer-schedule-info">';
                html += '<p class="timer-schedule-main">手動で見守りOFF中</p>';
                html += '<p class="timer-schedule-sub">スケジュール設定なし</p>';
                html += '</div>';
                html += '</div>';
            }

            // スケジュール追加ボタン
            html += '<button class="timer-add-btn" onclick="scheduleAddOrigin=\'timerlist\';openScheduleAddModal(\'' + escapeHtml(d.device_id) + '\',\'' + escapeHtml(d.room_number || '') + '\',\'' + escapeHtml(d.tenant_name || '') + '\')">＋ スケジュール追加</button>';

            html += '</div>';
        });

        body.innerHTML = html;
    } catch (e) {
        console.error('タイマー一覧読み込みエラー:', e);
        body.innerHTML = '<div class="timer-list-empty">通信エラーが発生しました</div>';
    }
}

function formatTimerDateTime(dtStr) {
    if (!dtStr) return '-';
    var parts = dtStr.split(' ');
    if (parts.length === 2) {
        var dateParts = parts[0].split('-');
        if (dateParts.length === 3) {
            return parseInt(dateParts[1]) + '/' + parseInt(dateParts[2]) + ' ' + parts[1];
        }
    }
    return dtStr;
}

// ===== スケジュール追加モーダル =====
let scheduleTargetDeviceId = null;
let scheduleType = 'oneshot';

function openScheduleAddModal(deviceId, roomNumber, tenantName) {
    scheduleTargetDeviceId = deviceId;
    scheduleType = 'oneshot';

    // ラベル更新
    var label = roomNumber ? roomNumber : '';
    if (tenantName) label += (label ? ' ' : '') + tenantName;
    label += (label ? '（' : '') + deviceId + (label ? '）' : '');
    document.getElementById('scheduleDeviceLabel').innerHTML = '対象: <strong>' + escapeHtml(label) + '</strong>';

    // フォームリセット
    document.getElementById('schedStartAt').value = '';
    document.getElementById('schedEndAt').value = '';
    document.getElementById('schedStartTime').value = '';
    document.getElementById('schedEndTime').value = '';
    document.getElementById('schedNextDay').checked = false;
    document.getElementById('schedMemo').value = '';
    document.querySelectorAll('.schedule-day-btn').forEach(function(b) { b.classList.remove('active'); });

    // タブリセット
    switchScheduleType('oneshot');

    showModal('scheduleAddModal');
}

function hideScheduleAddModal() {
    hideModal('scheduleAddModal');
}

function switchScheduleType(type) {
    scheduleType = type;
    document.getElementById('tabOneshot').classList.toggle('active', type === 'oneshot');
    document.getElementById('tabRecurring').classList.toggle('active', type === 'recurring');
    document.getElementById('oneshotForm').style.display = type === 'oneshot' ? 'block' : 'none';
    document.getElementById('recurringForm').style.display = type === 'recurring' ? 'block' : 'none';
}

function toggleDay(btn) {
    btn.classList.toggle('active');
}

async function submitSchedule() {
    if (!scheduleTargetDeviceId) return;

    var payload = {
        type: scheduleType,
        memo: document.getElementById('schedMemo').value || null,
    };

    if (scheduleType === 'oneshot') {
        var startAt = document.getElementById('schedStartAt').value;
        if (!startAt) {
            showToast('開始日時を入力してください', 'error');
            return;
        }
        payload.start_at = startAt;
        var endAt = document.getElementById('schedEndAt').value;
        if (endAt) payload.end_at = endAt;
    } else {
        var days = [];
        document.querySelectorAll('.schedule-day-btn.active').forEach(function(b) {
            days.push(parseInt(b.getAttribute('data-day')));
        });
        if (days.length === 0) {
            showToast('曜日を1つ以上選択してください', 'error');
            return;
        }
        var startTime = document.getElementById('schedStartTime').value;
        var endTime = document.getElementById('schedEndTime').value;
        if (!startTime || !endTime) {
            showToast('開始時間と終了時間を入力してください', 'error');
            return;
        }
        payload.days_of_week = days;
        payload.start_time = startTime;
        payload.end_time = endTime;
        payload.next_day = document.getElementById('schedNextDay').checked;
    }

    try {
        var res = await fetch('/admin/org/devices/' + scheduleTargetDeviceId + '/schedules', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        var data = await res.json();

        if (res.ok && data.success) {
            showToast('スケジュールを追加しました', 'success');
            hideScheduleAddModal();
            if (scheduleAddOrigin === 'detail' && currentDetailDeviceId) {
                showDeviceDetail(currentDetailDeviceId); // 詳細モーダル再読み込み
            }
            loadTimerList(); // タイマー一覧も再読み込み
        } else {
            var msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : '追加に失敗しました');
            showToast(msg, 'error');
        }
    } catch (e) {
        console.error('スケジュール追加エラー:', e);
        showToast('通信エラーが発生しました', 'error');
    }
}

// ===== スケジュール削除 =====
let deleteScheduleDeviceId = null;
let deleteScheduleId = null;

function confirmDeleteSchedule(deviceId, scheduleId, detail) {
    deleteScheduleDeviceId = deviceId;
    deleteScheduleId = scheduleId;
    document.getElementById('scheduleDeleteDetail').textContent = deviceId + ' のスケジュール: ' + detail;
    showModal('scheduleDeleteModal');
}

async function executeDeleteSchedule() {
    if (!deleteScheduleDeviceId || !deleteScheduleId) return;

    try {
        var res = await fetch('/admin/org/devices/' + deleteScheduleDeviceId + '/schedules/' + deleteScheduleId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        var data = await res.json();

        if (res.ok && data.success) {
            showToast('スケジュールを削除しました', 'success');
            hideModal('scheduleDeleteModal');
            if (currentDetailDeviceId && document.getElementById('detailModal').classList.contains('show')) {
                showDeviceDetail(currentDetailDeviceId); // 詳細モーダル再読み込み
            }
            loadTimerList(); // タイマー一覧も再読み込み
        } else {
            showToast(data.message || '削除に失敗しました', 'error');
        }
    } catch (e) {
        console.error('スケジュール削除エラー:', e);
        showToast('通信エラーが発生しました', 'error');
    }
}

// ===== 組織通知設定 =====
function showNotificationModal() {
    // 現在の設定を取得して表示
    fetch('{{ route("admin.org.notification") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        document.getElementById('orgNotifEmail1').value = data.notification_email_1 || '';
        document.getElementById('orgNotifEmail2').value = data.notification_email_2 || '';
        document.getElementById('orgNotifEmail3').value = data.notification_email_3 || '';
        document.getElementById('orgNotifEnabled').checked = data.notification_enabled;
        showModal('notificationModal');
    })
    .catch(function() {
        // 取得失敗でも空フォームで開く
        showModal('notificationModal');
    });
}

function saveOrgNotification() {
    var payload = {
        notification_email_1: document.getElementById('orgNotifEmail1').value || null,
        notification_email_2: document.getElementById('orgNotifEmail2').value || null,
        notification_email_3: document.getElementById('orgNotifEmail3').value || null,
        notification_enabled: document.getElementById('orgNotifEnabled').checked ? 1 : 0,
    };

    fetch('{{ route("admin.org.notification.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            showToast(data.message, 'success');
            hideModal('notificationModal');
        } else {
            var msg = data.message || '保存に失敗しました';
            if (data.errors) {
                msg = Object.values(data.errors).flat().join(', ');
            }
            showToast(msg, 'error');
        }
    })
    .catch(function() {
        showToast('通信エラーが発生しました', 'error');
    });
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
