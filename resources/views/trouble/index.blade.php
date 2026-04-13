@extends('layouts.app')

@section('title', '故障・通報 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <a href="/mypage" class="back-btn">←</a>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">故障・通報</h1>
        <span style="font-size:13px;font-weight:600;color:var(--gray-500);font-family:monospace;letter-spacing:0.05em;">{{ $device->device_id }}</span>
    </div>
</header>
@endsection

@section('styles')
<style>
    .back-btn {
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        background: var(--beige); border: none; border-radius: var(--radius);
        cursor: pointer; font-size: 18px; text-decoration: none; color: var(--gray-700);
        transition: all 0.2s;
    }
    .back-btn:hover { background: var(--gray-200); }

    .container { max-width: 640px; margin: 0 auto; padding: 24px 20px; }

    /* タブ */
    .tab-nav {
        display: flex; gap: 0;
        background: var(--gray-200); border-radius: var(--radius); padding: 3px;
        margin-bottom: 24px;
    }
    .tab-btn {
        flex: 1; padding: 10px 16px;
        font-size: 14px; font-weight: 600; font-family: inherit;
        background: transparent; border: none; border-radius: var(--radius);
        color: var(--gray-500); cursor: pointer; transition: all 0.2s;
        text-align: center;
    }
    .tab-btn.active { background: var(--white); color: var(--gray-800); box-shadow: var(--shadow-sm); }

    /* セクション */
    .section {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .section-title {
        font-size: 15px; font-weight: 600; color: var(--gray-800);
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
        padding-bottom: 12px; border-bottom: 2px solid var(--gray-200);
    }
    .section-title span { font-size: 18px; }

    /* フォーム */
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block; font-size: 13px; font-weight: 600;
        color: var(--gray-700); margin-bottom: 8px;
    }
    .form-select, .form-textarea {
        width: 100%; padding: 12px 14px;
        font-size: 14px; font-family: inherit;
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        background: var(--cream); transition: all 0.2s;
    }
    .form-select:focus, .form-textarea:focus {
        outline: none; border-color: var(--gray-500); background: var(--white);
    }
    .form-textarea { resize: vertical; min-height: 120px; line-height: 1.6; }
    .form-hint { font-size: 11px; color: var(--gray-500); margin-top: 6px; }

    .submit-btn {
        width: 100%; padding: 14px 20px;
        font-size: 15px; font-weight: 600; font-family: inherit;
        background: var(--gray-800); color: var(--white);
        border: none; border-radius: var(--radius);
        cursor: pointer; transition: all 0.2s;
    }
    .submit-btn:hover { background: var(--gray-700); }
    .submit-btn:active { transform: scale(0.98); }

    /* 案内ボックス */
    .info-box {
        padding: 14px 16px;
        background: var(--blue-light); border-radius: var(--radius);
        font-size: 13px; color: var(--gray-700); line-height: 1.7;
        margin-bottom: 20px;
    }
    .info-box strong { color: var(--gray-800); }

    .warning-box {
        padding: 14px 16px;
        background: #fef2f2; border-radius: var(--radius);
        font-size: 13px; color: #991b1b; line-height: 1.7;
        margin-bottom: 20px;
        border: 1px solid #fecaca;
    }

    /* 履歴 */
    .history-title {
        font-size: 14px; font-weight: 600; color: var(--gray-700);
        margin-bottom: 16px;
        padding-bottom: 8px; border-bottom: 1px solid var(--gray-200);
    }
    .report-card {
        padding: 16px;
        background: var(--beige); border-radius: var(--radius);
        margin-bottom: 12px;
        border: 1px solid var(--gray-200);
    }
    .report-card-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 8px;
    }
    .report-type {
        font-size: 13px; font-weight: 600; color: var(--gray-800);
    }
    .report-status {
        display: inline-block;
        padding: 2px 10px;
        font-size: 11px; font-weight: 600;
        border-radius: 10px;
    }
    .report-status.open { background: #dbeafe; color: #1d4ed8; }
    .report-status.in_progress { background: #fef3c7; color: #92400e; }
    .report-status.resolved { background: #d1fae5; color: #065f46; }
    .report-status.closed { background: var(--gray-200); color: var(--gray-600); }

    .report-meta {
        font-size: 12px; color: var(--gray-500); margin-bottom: 4px;
    }
    .report-desc {
        font-size: 13px; color: var(--gray-600); line-height: 1.5;
        margin-top: 8px;
    }
    .report-admin-notes {
        margin-top: 8px; padding: 10px 12px;
        background: var(--white); border-radius: var(--radius);
        font-size: 12px; color: var(--gray-700); line-height: 1.5;
    }
    .report-admin-notes-label {
        font-size: 11px; font-weight: 600; color: var(--gray-500);
        margin-bottom: 4px;
    }

    .empty-state {
        text-align: center; padding: 40px 20px; color: var(--gray-500);
    }
    .empty-state-icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state-text { font-size: 13px; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }
</style>
@endsection

@section('content')
<div class="container">

    {{-- タブ切り替え --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('malfunction')">🔧 故障・交換申請</button>
        <button class="tab-btn" onclick="switchTab('abuse')">⚠️ 不正利用通報</button>
    </div>

    {{-- 故障・交換申請フォーム --}}
    <div class="tab-content active" id="tab-malfunction">
        <section class="section">
            <h2 class="section-title"><span>🔧</span>故障・交換申請</h2>

            <div class="info-box">
                <strong>💡 故障対応について</strong><br>
                フォームを送信後、確認のうえ代替機を発送いたします。<br>
                故障した端末の返送は不要です（各自で廃棄をお願いします）。
            </div>

            <form method="POST" action="/trouble">
                @csrf
                <input type="hidden" name="type" value="malfunction">

                <div class="form-group">
                    <label class="form-label">症状を選択してください</label>
                    <select name="symptom" class="form-select">
                        <option value="">選択してください</option>
                        @foreach($symptoms as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">詳細説明（任意）</label>
                    <textarea name="description" class="form-textarea" placeholder="症状の詳細や発生状況を教えてください"></textarea>
                    <p class="form-hint">具体的に記載いただくと対応がスムーズです</p>
                </div>

                <button type="submit" class="submit-btn">申請を送信</button>
            </form>
        </section>
    </div>

    {{-- 不正利用通報フォーム --}}
    <div class="tab-content" id="tab-abuse">
        <section class="section">
            <h2 class="section-title"><span>⚠️</span>不正利用通報</h2>

            <div class="warning-box">
                <strong>⚠️ 不正利用とは</strong><br>
                同意なく設置されたデバイスや、ストーカー行為・DV等に悪用されている疑いがある場合にご利用ください。<br>
                緊急の場合は<strong>警察（110番）</strong>へ直接ご連絡ください。
            </div>

            <form method="POST" action="/trouble">
                @csrf
                <input type="hidden" name="type" value="abuse_report">

                <div class="form-group">
                    <label class="form-label">通報内容</label>
                    <textarea name="description" class="form-textarea" placeholder="不正利用の状況を詳しく教えてください（設置された経緯、心当たりなど）"></textarea>
                </div>

                <button type="submit" class="submit-btn" style="background:#991b1b;">通報を送信</button>
            </form>
        </section>

        <div class="info-box" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
            <strong>📞 緊急連絡先</strong><br>
            警察: <strong>110</strong>（緊急通報）<br>
            警察相談ダイヤル: <strong>#9110</strong>（相談）<br>
            配偶者暴力相談支援センター: <strong>0120-279-889</strong>
        </div>
    </div>

    {{-- 申請履歴 --}}
    <section class="section">
        <h2 class="section-title"><span>📋</span>申請履歴</h2>

        @if($reports->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <p class="empty-state-text">申請履歴はありません</p>
            </div>
        @else
            @foreach($reports as $report)
                <div class="report-card">
                    <div class="report-card-header">
                        <span class="report-type">{{ $typeLabels[$report->type] ?? $report->type }}</span>
                        <span class="report-status {{ $report->status }}">{{ $statusLabels[$report->status] ?? $report->status }}</span>
                    </div>
                    <div class="report-meta">
                        {{ $report->created_at->format('Y/m/d H:i') }}
                        @if($report->symptom)
                            ・{{ $symptoms[$report->symptom] ?? $report->symptom }}
                        @endif
                    </div>
                    @if($report->description)
                        <div class="report-desc">{{ $report->description }}</div>
                    @endif
                    @if($report->admin_notes)
                        <div class="report-admin-notes">
                            <div class="report-admin-notes-label">📌 運営からの回答</div>
                            {{ $report->admin_notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </section>

</div>
@endsection

@section('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    if (tab === 'malfunction') {
        document.querySelectorAll('.tab-btn')[0].classList.add('active');
        document.getElementById('tab-malfunction').classList.add('active');
    } else {
        document.querySelectorAll('.tab-btn')[1].classList.add('active');
        document.getElementById('tab-abuse').classList.add('active');
    }
}
</script>
@endsection
