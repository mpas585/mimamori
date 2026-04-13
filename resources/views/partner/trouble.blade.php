@extends('layouts.partner')

@section('title', '故障・通報管理')

@section('styles')
<style>
    .trouble-container { max-width: 1000px; margin: 0 auto; padding: 24px 20px; }

    .trouble-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .trouble-header-left {
        display: flex; align-items: center; gap: 12px;
    }
    .trouble-back-btn {
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        background: var(--beige); border: none; border-radius: var(--radius);
        cursor: pointer; font-size: 18px; text-decoration: none; color: var(--gray-700);
        transition: background 0.2s; flex-shrink: 0;
    }
    .trouble-back-btn:hover { background: var(--gray-200); }
    .trouble-page-title { font-size: 18px; font-weight: 700; color: var(--gray-800); }

    .new-report-btn {
        padding: 10px 20px;
        font-size: 14px; font-weight: 600; font-family: inherit;
        background: var(--gray-800); color: var(--white);
        border: none; border-radius: var(--radius);
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; gap: 6px;
    }
    .new-report-btn:hover { background: var(--gray-700); }

    /* フィルタ */
    .filter-bar {
        display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-input, .filter-select {
        padding: 9px 14px; font-size: 13px; font-family: inherit;
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        background: var(--white);
    }
    .filter-input { flex: 1; min-width: 160px; }
    .filter-input:focus, .filter-select:focus { outline: none; border-color: var(--gray-500); }

    /* テーブル */
    .report-table-wrap {
        background: var(--white); border-radius: var(--radius-lg);
        overflow: hidden; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200);
    }
    .report-table {
        width: 100%; border-collapse: collapse; font-size: 13px;
    }
    .report-table thead { background: var(--beige); }
    .report-table th {
        padding: 12px 16px; text-align: left; font-weight: 600;
        color: var(--gray-600); font-size: 12px; white-space: nowrap;
        border-bottom: 1px solid var(--gray-200);
    }
    .report-table td {
        padding: 14px 16px; border-bottom: 1px solid var(--gray-100);
        color: var(--gray-700); vertical-align: top;
    }
    .report-table tr:last-child td { border-bottom: none; }
    .report-table tr:hover td { background: var(--gray-100); }

    .mono { font-family: monospace; font-weight: 600; letter-spacing: 0.05em; }

    .type-badge {
        display: inline-block; padding: 2px 8px; font-size: 11px; font-weight: 600;
        border-radius: 4px; white-space: nowrap;
    }
    .type-badge.malfunction { background: #dbeafe; color: #1d4ed8; }
    .type-badge.abuse_report { background: #fef2f2; color: #991b1b; }

    .status-badge {
        display: inline-block; padding: 2px 10px; font-size: 11px; font-weight: 600;
        border-radius: 10px; white-space: nowrap; cursor: default;
    }
    .status-badge.open { background: #dbeafe; color: #1d4ed8; }
    .status-badge.in_progress { background: #fef3c7; color: #92400e; }
    .status-badge.resolved { background: #d1fae5; color: #065f46; }
    .status-badge.closed { background: var(--gray-200); color: var(--gray-600); }

    .desc-preview {
        max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        font-size: 12px; color: var(--gray-500);
    }

    .action-btn {
        padding: 6px 12px; font-size: 12px; font-weight: 500; font-family: inherit;
        background: var(--beige); border: 1px solid var(--gray-300);
        border-radius: var(--radius); cursor: pointer; transition: all 0.2s;
        color: var(--gray-700);
    }
    .action-btn:hover { background: var(--gray-200); }

    /* モーダル */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 1000;
        align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
        background: var(--white); border-radius: var(--radius-lg);
        padding: 28px 24px; width: 90%; max-width: 520px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        max-height: 90vh; overflow-y: auto;
    }
    .modal-title {
        font-size: 16px; font-weight: 700; color: var(--gray-800);
        margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--gray-200);
    }
    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block; font-size: 13px; font-weight: 600;
        color: var(--gray-700); margin-bottom: 6px;
    }
    .form-select, .form-input, .form-textarea {
        width: 100%; padding: 10px 14px;
        font-size: 14px; font-family: inherit;
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        background: var(--cream); transition: all 0.2s; box-sizing: border-box;
    }
    .form-select:focus, .form-input:focus, .form-textarea:focus {
        outline: none; border-color: var(--gray-500); background: var(--white);
    }
    .form-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
    .form-hint { font-size: 11px; color: var(--gray-500); margin-top: 4px; }

    .modal-actions {
        display: flex; gap: 12px; margin-top: 24px;
    }
    .btn-cancel {
        flex: 1; padding: 12px; font-size: 14px; font-family: inherit;
        background: var(--beige); color: var(--gray-700);
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        cursor: pointer;
    }
    .btn-submit {
        flex: 1; padding: 12px; font-size: 14px; font-weight: 600; font-family: inherit;
        background: var(--gray-800); color: var(--white);
        border: none; border-radius: var(--radius); cursor: pointer;
    }
    .btn-submit:hover { background: var(--gray-700); }
    .btn-submit.danger { background: #991b1b; }
    .btn-submit.danger:hover { background: #7f1d1d; }

    /* 詳細モーダル */
    .detail-row { margin-bottom: 12px; }
    .detail-label { font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 2px; }
    .detail-value { font-size: 14px; color: var(--gray-800); line-height: 1.5; }
    .detail-notes-input {
        width: 100%; padding: 10px 12px;
        font-size: 13px; font-family: inherit;
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        background: var(--cream); resize: vertical; min-height: 80px; box-sizing: border-box;
    }
    .status-select {
        padding: 8px 12px; font-size: 13px; font-family: inherit;
        border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white);
    }

    /* 空状態 */
    .empty-state { text-align: center; padding: 60px 20px; color: var(--gray-500); }
    .empty-state-icon { font-size: 48px; margin-bottom: 16px; }
    .empty-state-text { font-size: 14px; }

    /* ページネーション */
    .pagination-area {
        margin-top: 20px; display: flex; flex-direction: column;
        align-items: center; gap: 8px;
    }
    .pagination-wrap {
        display: flex; justify-content: center; gap: 4px; flex-wrap: wrap;
    }
    .pagination-wrap a, .pagination-wrap span {
        display: inline-block; padding: 8px 14px; border-radius: var(--radius);
        font-size: 13px; text-decoration: none; color: var(--gray-600);
        background: var(--white); border: 1px solid var(--gray-200); transition: all 0.2s;
    }
    .pagination-wrap a:hover { background: var(--beige); }
    .pagination-wrap span.current { background: var(--gray-800); color: var(--white); border-color: var(--gray-800); }
    .pagination-wrap span.disabled { color: var(--gray-300); }

    @media (max-width: 768px) {
        .report-table { font-size: 12px; }
        .report-table th, .report-table td { padding: 10px 12px; }
        .trouble-header { flex-direction: column; align-items: stretch; }
        .filter-bar { flex-direction: column; }
    }
</style>
@endsection

@section('content')
<div class="trouble-container">

    {{-- ヘッダー --}}
    <div class="trouble-header">
        <div class="trouble-header-left">
            <a href="/partner" class="trouble-back-btn">←</a>
            <h1 class="trouble-page-title">故障・通報管理</h1>
        </div>
        <button class="new-report-btn" onclick="openNewModal()">＋ 新規申請</button>
    </div>

    {{-- フィルタ --}}
    <form method="GET" action="{{ route('partner.trouble-reports') }}" id="filterForm">
        <div class="filter-bar">
            <input type="text" name="search" class="filter-input" placeholder="デバイスIDで検索"
                   value="{{ request('search') }}" onchange="this.form.submit()">
            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">種別: すべて</option>
                <option value="malfunction" {{ request('type') === 'malfunction' ? 'selected' : '' }}>故障・交換</option>
                <option value="abuse_report" {{ request('type') === 'abuse_report' ? 'selected' : '' }}>不正利用通報</option>
            </select>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">状態: すべて</option>
                @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- 一覧テーブル --}}
    @if($reports->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <p class="empty-state-text">申請データはありません</p>
        </div>
    @else
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>日時</th>
                        <th>デバイス</th>
                        <th>種別</th>
                        <th>症状</th>
                        <th>状態</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        <td style="white-space:nowrap;">{{ $report->created_at->format('Y/m/d H:i') }}</td>
                        <td>
                            <span class="mono">{{ $report->device->device_id ?? '-' }}</span>
                            @if($report->device && $report->device->organization)
                                <br><span style="font-size:11px;color:var(--gray-500);">{{ $report->device->organization->name }}</span>
                            @endif
                        </td>
                        <td><span class="type-badge {{ $report->type }}">{{ $typeLabels[$report->type] ?? $report->type }}</span></td>
                        <td>{{ $symptoms[$report->symptom] ?? ($report->symptom ?: '-') }}</td>
                        <td><span class="status-badge {{ $report->status }}">{{ $statusLabels[$report->status] ?? $report->status }}</span></td>
                        <td><button class="action-btn" onclick="openDetail({{ $report->id }})">詳細</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="pagination-area">
                <div class="pagination-wrap">
                    @if($reports->onFirstPage())
                        <span class="disabled">‹</span>
                    @else
                        <a href="{{ $reports->previousPageUrl() }}">‹</a>
                    @endif
                    @foreach($reports->getUrlRange(max(1, $reports->currentPage()-2), min($reports->lastPage(), $reports->currentPage()+2)) as $page => $url)
                        @if($page == $reports->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($reports->hasMorePages())
                        <a href="{{ $reports->nextPageUrl() }}">›</a>
                    @else
                        <span class="disabled">›</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- 新規申請モーダル --}}
<div class="modal-overlay" id="newModal" onclick="if(event.target===this)closeNewModal()">
    <div class="modal">
        <h3 class="modal-title">🔧 新規申請</h3>
        <form method="POST" action="{{ route('partner.trouble-reports.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">デバイス</label>
                <select name="device_id" class="form-select" required>
                    <option value="">選択してください</option>
                    @foreach($devices as $d)
                        <option value="{{ $d->id }}">{{ $d->device_id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">種別</label>
                <select name="type" class="form-select" required id="newReportType" onchange="toggleSymptom()">
                    <option value="malfunction">故障・交換申請</option>
                    <option value="abuse_report">不正利用通報</option>
                </select>
            </div>

            <div class="form-group" id="symptomGroup">
                <label class="form-label">症状</label>
                <select name="symptom" class="form-select">
                    <option value="">選択してください</option>
                    @foreach($symptoms as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">詳細説明（任意）</label>
                <textarea name="description" class="form-textarea" placeholder="状況の詳細を入力"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeNewModal()">キャンセル</button>
                <button type="submit" class="btn-submit">申請を送信</button>
            </div>
        </form>
    </div>
</div>

{{-- 詳細モーダル --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)closeDetail()">
    <div class="modal">
        <h3 class="modal-title">📋 申請詳細</h3>

        <div class="detail-row">
            <div class="detail-label">デバイスID</div>
            <div class="detail-value" id="detailDeviceId">-</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">種別</div>
            <div class="detail-value" id="detailType">-</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">症状</div>
            <div class="detail-value" id="detailSymptom">-</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">詳細説明</div>
            <div class="detail-value" id="detailDesc">-</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">申請日時</div>
            <div class="detail-value" id="detailDate">-</div>
        </div>

        @if($isMaster)
        <hr style="border:none;border-top:1px solid var(--gray-200);margin:16px 0;">
        <div class="form-group">
            <label class="form-label">ステータス</label>
            <select class="status-select" id="detailStatus">
                @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">管理者メモ</label>
            <textarea class="detail-notes-input" id="detailNotes" placeholder="対応内容や連絡事項を記入"></textarea>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDetail()">閉じる</button>
            <button type="button" class="btn-submit" onclick="saveDetail()">保存</button>
        </div>
        @else
        <div class="detail-row" id="detailNotesRow" style="display:none;">
            <div class="detail-label">📌 運営からの回答</div>
            <div class="detail-value" id="detailNotesView">-</div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDetail()" style="flex:1;">閉じる</button>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// 申請データ（テンプレート内で生成）
const reportData = @json($reports->items());
const symptomLabels = @json($symptoms);
const typeLabelsMap = @json($typeLabels);
const statusLabelsMap = @json($statusLabels);

let currentReportId = null;

// ==== 新規申請モーダル ====
function openNewModal() {
    document.getElementById('newModal').classList.add('open');
}
function closeNewModal() {
    document.getElementById('newModal').classList.remove('open');
}
function toggleSymptom() {
    const type = document.getElementById('newReportType').value;
    document.getElementById('symptomGroup').style.display = type === 'malfunction' ? 'block' : 'none';
}

// ==== 詳細モーダル ====
function openDetail(id) {
    const report = reportData.find(r => r.id === id);
    if (!report) return;

    currentReportId = id;
    document.getElementById('detailDeviceId').textContent = report.device ? report.device.device_id : '-';
    document.getElementById('detailType').innerHTML = '<span class="type-badge ' + report.type + '">' + (typeLabelsMap[report.type] || report.type) + '</span>';
    document.getElementById('detailSymptom').textContent = symptomLabels[report.symptom] || report.symptom || '-';
    document.getElementById('detailDesc').textContent = report.description || '（なし）';
    document.getElementById('detailDate').textContent = report.created_at ? new Date(report.created_at).toLocaleString('ja-JP') : '-';

    @if($isMaster)
    document.getElementById('detailStatus').value = report.status;
    document.getElementById('detailNotes').value = report.admin_notes || '';
    @else
    if (report.admin_notes) {
        document.getElementById('detailNotesRow').style.display = 'block';
        document.getElementById('detailNotesView').textContent = report.admin_notes;
    } else {
        document.getElementById('detailNotesRow').style.display = 'none';
    }
    @endif

    document.getElementById('detailModal').classList.add('open');
}
function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    currentReportId = null;
}

@if($isMaster)
function saveDetail() {
    if (!currentReportId) return;

    const status = document.getElementById('detailStatus').value;
    const notes = document.getElementById('detailNotes').value;

    fetch('/partner/trouble-reports/' + currentReportId + '/status', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: status, admin_notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeDetail();
            location.reload();
        } else {
            alert(data.message || 'エラーが発生しました');
        }
    })
    .catch(() => alert('通信エラーが発生しました'));
}
@endif
</script>
@endsection
