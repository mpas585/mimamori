@extends('layouts.app')

@section('title', '課金管理')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:900px;">
        <a href="/partner" class="back-btn">←</a>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">課金管理</h1>
    </div>
</header>
@endsection

@section('styles')
<style>
    .back-btn {
        width:36px;height:36px;display:flex;align-items:center;justify-content:center;
        background:var(--beige);border:none;border-radius:var(--radius);cursor:pointer;
        font-size:18px;transition:all 0.2s;text-decoration:none;color:var(--gray-700);
    }
    .back-btn:hover{background:var(--gray-200);}
    .container{max-width:900px;margin:0 auto;padding:24px 20px;}
    .section{background:var(--white);border-radius:var(--radius-lg);padding:24px;margin-bottom:20px;box-shadow:var(--shadow-sm);border:1px solid var(--gray-200);}
    .section-title{font-size:15px;font-weight:600;color:var(--gray-800);margin-bottom:20px;display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:2px solid var(--gray-200);}

    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
    .form-group{margin-bottom:16px;}
    .form-label{display:block;font-size:13px;font-weight:600;color:var(--gray-700);margin-bottom:6px;}
    .form-input{width:100%;padding:12px 14px;font-size:15px;font-family:inherit;border:1px solid var(--gray-300);border-radius:var(--radius);background:var(--cream);}
    .form-input:focus{outline:none;border-color:var(--gray-500);background:var(--white);}
    .form-select{width:100%;padding:12px 14px;font-size:15px;font-family:inherit;border:1px solid var(--gray-300);border-radius:var(--radius);background:var(--cream);}

    .payjp-element{padding:14px 16px;border:1px solid var(--gray-300);border-radius:var(--radius);background:var(--cream);min-height:48px;}
    .payjp-element.focused{border-color:var(--gray-500);background:var(--white);}

    .amount-preview{background:var(--beige);border-radius:var(--radius);padding:16px;text-align:center;margin-bottom:16px;}
    .amount-preview-label{font-size:12px;color:var(--gray-500);margin-bottom:4px;}
    .amount-preview-value{font-size:28px;font-weight:700;color:var(--gray-800);}

    .btn-primary{width:100%;padding:14px;font-size:14px;font-weight:700;font-family:inherit;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:var(--radius);cursor:pointer;transition:all 0.2s;}
    .btn-primary:hover{opacity:0.9;}
    .btn-primary:disabled{opacity:0.6;cursor:not-allowed;}
    .btn-sm{padding:8px 14px;font-size:13px;font-weight:600;font-family:inherit;border-radius:var(--radius);cursor:pointer;border:none;transition:all 0.2s;}
    .btn-outline{background:var(--white);color:var(--gray-600);border:1px solid var(--gray-300);}
    .btn-outline:hover{background:var(--gray-100);}
    .btn-danger{background:#fee2e2;color:#991b1b;}
    .btn-danger:hover{background:#fecaca;}
    .btn-success{background:#dcfce7;color:#166534;}
    .btn-success:hover{background:#bbf7d0;}

    .contracts-table{width:100%;border-collapse:collapse;font-size:14px;}
    .contracts-table th{text-align:left;padding:10px 12px;font-size:12px;font-weight:600;color:var(--gray-500);border-bottom:2px solid var(--gray-200);white-space:nowrap;}
    .contracts-table td{padding:14px 12px;border-bottom:1px solid var(--gray-100);vertical-align:middle;}
    .contracts-table tr:last-child td{border-bottom:none;}

    .status-badge{display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;}
    .status-badge.active{background:#dcfce7;color:#166534;}
    .status-badge.past_due{background:#fee2e2;color:#991b1b;}
    .status-badge.canceled{background:var(--gray-200);color:var(--gray-500);}

    .log-mini{font-size:11px;color:var(--gray-500);}
    .log-mini.success{color:#166534;}
    .log-mini.failed{color:#991b1b;}

    .error-msg{padding:12px 16px;background:#fee2e2;border-radius:var(--radius);font-size:13px;color:#991b1b;margin-top:12px;display:none;}

    @media(max-width:640px){.form-row{grid-template-columns:1fr;}.contracts-table{font-size:12px;}}
</style>
@endsection

@section('content')
<div class="container">

    {{-- 新規契約登録 --}}
    <section class="section">
        <h2 class="section-title"><span>➕</span>新規契約登録</h2>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">組織</label>
                <select class="form-select" id="orgSelect">
                    <option value="">個人 / 組織なし</option>
                    @foreach(\App\Models\Organization::orderBy('name')->get() as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <div></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">本体台数</label>
                <input type="number" class="form-input" id="deviceCount" value="1" min="1" max="999" oninput="updatePreview()">
            </div>
            <div class="form-group">
                <label class="form-label">プレミアム台数</label>
                <input type="number" class="form-input" id="premiumCount" value="0" min="0" max="999" oninput="updatePreview()">
            </div>
        </div>

        <div class="amount-preview">
            <p class="amount-preview-label">月額請求額（税込）</p>
            <p class="amount-preview-value" id="amountPreview">¥1,000</p>
            <p style="font-size:12px;color:var(--gray-500);margin-top:4px;" id="amountBreakdown">本体1台 × ¥1,000</p>
        </div>

        <div class="form-group">
            <label class="form-label">クレジットカード</label>
            <div id="payjp-card-element" class="payjp-element"></div>
        </div>
        <div class="error-msg" id="card-error"></div>

        <button class="btn-primary" id="registerBtn" onclick="registerContract()">契約を登録する</button>
    </section>

    {{-- 契約一覧 --}}
    <section class="section">
        <h2 class="section-title"><span>📋</span>契約一覧</h2>

        @if($contracts->isEmpty())
        <p style="color:var(--gray-500);font-size:14px;">契約がありません</p>
        @else
        <div style="overflow-x:auto;">
        <table class="contracts-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>組織</th>
                    <th>台数</th>
                    <th>プレミアム</th>
                    <th>月額</th>
                    <th>次回課金</th>
                    <th>ステータス</th>
                    <th>最終課金</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach($contracts as $contract)
            <tr id="row-{{ $contract->id }}">
                <td style="font-family:monospace;color:var(--gray-500);">#{{ $contract->id }}</td>
                <td>{{ $contract->organization?->name ?? '-' }}</td>
                <td>
                    <input type="number" class="form-input" style="width:70px;padding:6px 8px;font-size:13px;"
                        value="{{ $contract->device_count }}" min="1" max="999"
                        id="dc-{{ $contract->id }}" onchange="updateCount({{ $contract->id }})">
                </td>
                <td>
                    <input type="number" class="form-input" style="width:70px;padding:6px 8px;font-size:13px;"
                        value="{{ $contract->premium_device_count }}" min="0" max="999"
                        id="pc-{{ $contract->id }}" onchange="updateCount({{ $contract->id }})">
                </td>
                <td style="font-weight:700;">
                    ¥<span id="amt-{{ $contract->id }}">{{ number_format($contract->amount) }}</span>
                </td>
                <td style="font-size:12px;">{{ $contract->next_billing_date?->format('Y/m/d') ?? '-' }}</td>
                <td><span class="status-badge {{ $contract->status }}">{{ $contract->status }}</span></td>
                <td>
                    @if($log = $contract->logs->first())
                    <span class="log-mini {{ $log->status }}">
                        {{ $log->billed_at->format('m/d') }} {{ $log->status === 'success' ? '✓' : '✗' }}
                    </span>
                    @else
                    <span class="log-mini">-</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @if($contract->status === 'active')
                        <button class="btn-sm btn-success" onclick="chargeNow({{ $contract->id }})">即時課金</button>
                        <button class="btn-sm btn-danger" onclick="cancelContract({{ $contract->id }})">解約</button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        {{ $contracts->links() }}
        @endif
    </section>

</div>
@endsection

@section('scripts')
<script src="https://js.pay.jp/v2/pay.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const headers = {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'};

// payjp.js 初期化
let payjp, elements, cardElement;
window.addEventListener('load', function() {
    payjp = Payjp('{{ config("services.payjp.public_key") }}');
    elements = payjp.elements();
    cardElement = elements.create('card', {
        style: { base: { color:'#3d3935', fontFamily:'"Noto Sans JP",sans-serif', fontSize:'15px', '::placeholder':{color:'#a8a29e'} } },
        hidePostalCode: true,
    });
    cardElement.mount('#payjp-card-element');
    cardElement.on('focus', () => document.getElementById('payjp-card-element').classList.add('focused'));
    cardElement.on('blur',  () => document.getElementById('payjp-card-element').classList.remove('focused'));
});

// 金額プレビュー更新
function updatePreview() {
    const dc = parseInt(document.getElementById('deviceCount').value) || 0;
    const pc = parseInt(document.getElementById('premiumCount').value) || 0;
    const amount = (dc * 1000) + (pc * 500);
    document.getElementById('amountPreview').textContent = '¥' + amount.toLocaleString();
    let breakdown = `本体${dc}台 × ¥1,000`;
    if (pc > 0) breakdown += ` + プレミアム${pc}台 × ¥500`;
    document.getElementById('amountBreakdown').textContent = breakdown;
}

// 契約登録
async function registerContract() {
    const btn = document.getElementById('registerBtn');
    const errEl = document.getElementById('card-error');
    errEl.style.display = 'none';
    btn.disabled = true;
    btn.textContent = '処理中...';

    try {
        const result = await payjp.createToken(cardElement);
        if (result.error) {
            errEl.textContent = result.error.message;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = '契約を登録する';
            return;
        }

        const res = await fetch('/partner/billing', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                payjp_token:          result.id,
                organization_id:      document.getElementById('orgSelect').value || null,
                device_count:         parseInt(document.getElementById('deviceCount').value),
                premium_device_count: parseInt(document.getElementById('premiumCount').value),
            }),
        });
        const data = await res.json();

        if (data.ok) {
            showToast(data.message + `（月額 ¥${data.amount.toLocaleString()}）`);
            setTimeout(() => location.reload(), 1500);
        } else {
            errEl.textContent = data.message;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = '契約を登録する';
        }
    } catch (e) {
        errEl.textContent = '通信エラー';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '契約を登録する';
    }
}

// 台数変更
async function updateCount(id) {
    const dc = parseInt(document.getElementById(`dc-${id}`).value);
    const pc = parseInt(document.getElementById(`pc-${id}`).value);

    const res = await fetch(`/partner/billing/${id}`, {
        method: 'PUT',
        headers: headers,
        body: JSON.stringify({ device_count: dc, premium_device_count: pc }),
    });
    const data = await res.json();
    if (data.ok) {
        document.getElementById(`amt-${id}`).textContent = data.amount.toLocaleString();
        showToast('台数を更新しました');
    }
}

// 即時課金
async function chargeNow(id) {
    if (!confirm('今すぐ課金を実行しますか？')) return;
    const res = await fetch(`/partner/billing/${id}/charge-now`, {
        method: 'POST',
        headers: headers,
    });
    const data = await res.json();
    showToast(data.message);
    if (data.ok) setTimeout(() => location.reload(), 1500);
}

// 解約
async function cancelContract(id) {
    if (!confirm('この契約を解約しますか？')) return;
    const res = await fetch(`/partner/billing/${id}/cancel`, {
        method: 'POST',
        headers: headers,
    });
    const data = await res.json();
    showToast(data.message);
    if (data.ok) setTimeout(() => location.reload(), 1000);
}
</script>
@endsection
