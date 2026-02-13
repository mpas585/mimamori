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

    /* タブ */
    .tab-bar {
        display: flex;
        gap: 4px;
        background: var(--white);
        padding: 4px;
        border-radius: var(--radius-lg);
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .tab {
        flex: 1;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        color: var(--gray-500);
        background: transparent;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .tab.active {
        background: var(--gray-800);
        color: var(--white);
    }
    .tab:not(.active):hover {
        background: var(--beige);
        color: var(--gray-700);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

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

    /* 管理者テーブル */
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .admin-table th {
        text-align: left;
        padding: 10px 12px;
        border-bottom: 2px solid #e0d8cc;
        font-weight: 500;
        color: #8b7e6a;
        font-size: 12px;
        white-space: nowrap;
    }
    .admin-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f0ebe1;
        vertical-align: middle;
    }
    .admin-table tr:hover td {
        background: #faf8f4;
    }
    .role-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .role-master {
        background: var(--purple-light, #f3e8ff);
        color: #7c3aed;
    }
    .role-operator {
        background: var(--blue-light, #dbeafe);
        color: #2563eb;
    }
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
    .action-btn:hover {
        background: var(--beige);
    }
    .action-btn.danger {
        color: var(--red);
        border-color: #fecaca;
    }
    .action-btn.danger:hover {
        background: var(--red-light);
    }
    .toolbar-right {
        display: flex;
        gap: 8px;
    }

    /* パスワード表示 */
    .password-field {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .password-field .form-input {
        flex: 1;
    }
    .password-generate-btn {
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--beige);
        color: var(--gray-700);
        cursor: pointer;
        white-space: nowrap;
    }
    .password-generate-btn:hover {
        background: var(--gray-200);
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

{{-- タブ --}}
<div class="tab-bar">
    <button class="tab active" onclick="switchTab('devices', this)">デバイス管理</button>
    <button class="tab" onclick="switchTab('admins', this)">管理者アカウント</button>
</div>

{{-- ===== デバイス管理タブ ===== --}}
<div id="tab-devices" class="tab-content active">

    {{-- デバイス発番 --}}
    <div class="card">
        <div class="card-title" style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">デバイス発番</div>
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
        <div style="font-size:15px;font-weight:600;color:#5a5245;margin-bottom:12px;">デバイス一覧</div>

        <form method="GET" action="/admin" class="filter-bar">
            <input type="hidden" name="tab" value="devices">
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
</div>

{{-- ===== 管理者アカウントタブ ===== --}}
<div id="tab-admins" class="tab-content">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:600;color:#5a5245;">管理者アカウント一覧</div>
            <div class="toolbar-right">
                <button class="btn btn-sm btn-primary" onclick="showAddAdminModal()">＋ アカウント追加</button>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>権限</th>
                    <th>最終ログイン</th>
                    <th>作成日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adminUsers as $admin)
                    <tr>
                        <td style="font-size:12px;color:#999;">{{ $admin->id }}</td>
                        <td style="font-weight:500;">{{ $admin->name }}</td>
                        <td style="font-size:13px;">{{ $admin->email }}</td>
                        <td>
                            <span class="role-badge {{ $admin->role === 'master' ? 'role-master' : 'role-operator' }}">
                                {{ $admin->role === 'master' ? 'マスター' : 'オペレーター' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#888;">
                            {{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->format('Y/m/d H:i') : '未ログイン' }}
                        </td>
                        <td style="font-size:12px;color:#888;">
                            {{ $admin->created_at->format('Y/m/d') }}
                        </td>
                        <td>
                            <button class="action-btn" onclick="showEditAdminModal({{ json_encode([
                                'id' => $admin->id,
                                'name' => $admin->name,
                                'email' => $admin->email,
                                'role' => $admin->role,
                            ]) }})">編集</button>
                            @if($admin->id !== Auth::guard('admin')->id())
                                <button class="action-btn danger" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ $admin->name }}')">削除</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">管理者アカウントがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== 管理者追加モーダル ===== --}}
<div id="addAdminModal" class="modal-overlay" onclick="if(event.target===this)hideAddAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3>管理者アカウント追加</h3>
            <button class="modal-close" onclick="hideAddAdminModal()">×</button>
        </div>
        <form method="POST" action="{{ route('admin.admin-users.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">名前 *</label>
                    <input type="text" name="name" class="form-input" placeholder="例：山田太郎" required>
                </div>
                <div class="form-group">
                    <label class="form-label">メールアドレス *</label>
                    <input type="email" name="email" class="form-input" placeholder="admin@example.com" required>
                    <p class="form-hint">このアドレスでログインします</p>
                </div>
                <div class="form-group">
                    <label class="form-label">権限 *</label>
                    <select name="role" class="form-input">
                        <option value="operator">オペレーター（組織管理者）</option>
                        <option value="master">マスター（全権限）</option>
                    </select>
                    <p class="form-hint">オペレーター：担当組織のデバイスのみ管理可能 / マスター：全機能にアクセス可能</p>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideAddAdminModal()">キャンセル</button>
                <button type="submit" class="btn btn-primary">作成</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== 管理者編集モーダル ===== --}}
<div id="editAdminModal" class="modal-overlay" onclick="if(event.target===this)hideEditAdminModal()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3>管理者アカウント編集</h3>
            <button class="modal-close" onclick="hideEditAdminModal()">×</button>
        </div>
        <form method="POST" id="editAdminForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">名前 *</label>
                    <input type="text" name="name" id="editAdminName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">メールアドレス *</label>
                    <input type="email" name="email" id="editAdminEmail" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">権限 *</label>
                    <select name="role" id="editAdminRole" class="form-input">
                        <option value="operator">オペレーター（組織管理者）</option>
                        <option value="master">マスター（全権限）</option>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideEditAdminModal()">キャンセル</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== 管理者削除フォーム（hidden） ===== --}}
<form id="deleteAdminForm" method="POST" action="" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
// タブ切り替え
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// URLパラメータでタブ復元
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'admins') {
        const btn = document.querySelectorAll('.tab')[1];
        switchTab('admins', btn);
    }

    // バリデーションエラーがあれば管理者タブに戻す
    @if($errors->has('admin_name') || $errors->has('admin_email') || $errors->has('admin_password') || $errors->has('admin_role'))
        const adminBtn = document.querySelectorAll('.tab')[1];
        switchTab('admins', adminBtn);
    @endif
});

// テキストコピー
function copyText(id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        btn.textContent = 'コピー済';
        setTimeout(() => { btn.textContent = 'コピー'; }, 1500);
    });
}

// パスワード生成
function generatePassword(inputId) {
    const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById(inputId).value = password;
}

// 管理者追加モーダル
function showAddAdminModal() {
    generatePassword('addAdminPassword');
    document.getElementById('addAdminModal').classList.add('show');
}
function hideAddAdminModal() {
    document.getElementById('addAdminModal').classList.remove('show');
}

// 管理者編集モーダル
function showEditAdminModal(data) {
    document.getElementById('editAdminForm').action = '/admin/admin-users/' + data.id;
    document.getElementById('editAdminName').value = data.name;
    document.getElementById('editAdminEmail').value = data.email;
    document.getElementById('editAdminRole').value = data.role;
    document.getElementById('editAdminPassword').value = '';
    document.getElementById('editAdminModal').classList.add('show');
}
function hideEditAdminModal() {
    document.getElementById('editAdminModal').classList.remove('show');
}

// 管理者削除
function confirmDeleteAdmin(id, name) {
    if (confirm('「' + name + '」のアカウントを削除しますか？\nこの操作は取り消せません。')) {
        const form = document.getElementById('deleteAdminForm');
        form.action = '/admin/admin-users/' + id;
        form.submit();
    }
}
</script>
@endsection
