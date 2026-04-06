@extends('layouts.app')

@section('title', 'プラン - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <a href="/settings" class="back-btn">←</a>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">プラン</h1>
        <span style="font-size:13px;font-weight:600;color:var(--gray-500);font-family:monospace;letter-spacing:0.05em;">{{ $device->device_id }}</span>
    </div>
</header>
@endsection

@section('styles')
<style>
    .back-btn {
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        background: var(--beige); border: none;
        border-radius: var(--radius); cursor: pointer;
        font-size: 18px; transition: all 0.2s;
        text-decoration: none; color: var(--gray-700);
    }
    .back-btn:hover { background: var(--gray-200); }

    .container { max-width: 640px; margin: 0 auto; padding: 24px 20px; }

    .section {
        background: var(--white); border-radius: var(--radius-lg);
        padding: 24px; margin-bottom: 20px;
        box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200);
    }
    .section-title {
        font-size: 15px; font-weight: 600; color: var(--gray-800);
        margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
        padding-bottom: 12px; border-bottom: 2px solid var(--gray-200);
    }
    .section-title span { font-size: 18px; }

    /* 現在のプランバナー */
    .plan-banner {
        border-radius: var(--radius-lg); padding: 24px;
        margin-bottom: 20px; text-align: center;
    }
    .plan-banner.free {
        background: var(--beige); border: 2px solid var(--gray-300);
    }
    .plan-banner.premium {
        background: linear-gradient(135deg, #667eea15, #764ba215);
        border: 2px solid #667eea;
    }
    .plan-banner.canceled {
        background: #fff8f0; border: 2px solid #f59e0b;
    }
    .plan-badge {
        display: inline-block; padding: 6px 16px;
        font-size: 13px; font-weight: 700;
        border-radius: 20px; margin-bottom: 12px;
    }
    .plan-badge.free { background: var(--gray-200); color: var(--gray-600); }
    .plan-badge.premium { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
    .plan-badge.canceled { background: #f59e0b; color: #fff; }
    .plan-name { font-size: 24px; font-weight: 700; color: var(--gray-800); margin-bottom: 6px; }
    .plan-sub { font-size: 13px; color: var(--gray-500); }
    .plan-end-date {
        margin-top: 12px; padding: 10px 16px;
        background: #fff8f0; border-radius: var(--radius);
        font-size: 13px; color: #92400e; font-weight: 500;
    }

    /* プラン比較 */
    .plan-compare { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .plan-card {
        border-radius: var(--radius-lg); padding: 20px;
        border: 2px solid var(--gray-200); transition: all 0.2s;
        cursor: pointer; position: relative; overflow: hidden;
    }
    .plan-card.selected { border-color: #667eea; }
    .plan-card.current-plan { border-color: var(--green); cursor: default; }
    .plan-card-check {
        position: absolute; top: 12px; right: 12px;
        width: 22px; height: 22px; border-radius: 50%;
        background: var(--gray-200); display: flex;
        align-items: center; justify-content: center;
        font-size: 12px; transition: all 0.2s;
    }
    .plan-card.selected .plan-card-check { background: #667eea; color: #fff; }
    .plan-card-name { font-size: 13px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; }
    .plan-card-price { font-size: 28px; font-weight: 700; color: var(--gray-800); }
    .plan-card-price span { font-size: 14px; font-weight: 500; color: var(--gray-500); }
    .plan-card-note { font-size: 11px; color: var(--gray-500); margin-top: 4px; }
    .plan-card-badge {
        position: absolute; top: 12px; left: 12px;
        padding: 3px 8px; background: #22c55e; color: #fff;
        font-size: 10px; font-weight: 700; border-radius: 4px;
    }

    /* プレミアム機能リスト */
    .feature-list { list-style: none; padding: 0; margin: 0; }
    .feature-list li {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 10px 0; border-bottom: 1px solid var(--gray-100);
        font-size: 14px; color: var(--gray-700);
    }
    .feature-list li:last-child { border-bottom: none; }
    .feature-list .check { color: #667eea; font-size: 16px; flex-shrink: 0; margin-top: 1px; }
    .feature-list .free-check { color: var(--green); font-size: 16px; flex-shrink: 0; margin-top: 1px; }

    /* カード入力フォーム */
    .card-form { margin-top: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; }
    .payjp-element {
        padding: 14px 16px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
        min-height: 48px;
    }
    .payjp-element.focused { border-color: var(--gray-500); background: var(--white); }

    .btn-primary {
        width: 100%; padding: 16px 20px;
        font-size: 15px; font-weight: 700; font-family: inherit;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff; border: none; border-radius: var(--radius);
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary:active { transform: scale(0.98); }
    .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .btn-primary .spinner {
        display: none; width: 18px; height: 18px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff; border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .btn-primary.loading .spinner { display: block; }
    .btn-primary.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .btn-cancel-plan {
        width: 100%; padding: 14px 20px;
        font-size: 14px; font-weight: 600; font-family: inherit;
        background: var(--white); color: var(--gray-500);
        border: 1px solid var(--gray-300); border-radius: var(--radius);
        cursor: pointer; transition: all 0.2s; margin-top: 12px;
    }
    .btn-cancel-plan:hover { background: var(--gray-100); color: var(--gray-700); }

    .secure-note {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; color: var(--gray-500); margin-top: 10px;
        justify-content: center;
    }
    .error-msg {
        padding: 12px 16px; background: #fee2e2;
        border-radius: var(--radius); font-size: 13px; color: #991b1b;
        margin-top: 12px; display: none;
    }

    /* 解約済み後の案内 */
    .info-box {
        padding: 14px 16px; background: var(--blue-light);
        border-radius: var(--radius); font-size: 13px; color: var(--gray-700); line-height: 1.7;
    }

    @media (max-width: 480px) {
        .plan-compare { grid-template-columns: 1fr; gap: 12px; }
        .plan-card-price { font-size: 24px; }
    }
</style>
@endsection

@section('content')
<div class="container">

    {{-- 現在のプラン状態 --}}
    @php
        $isPremium = $device->premium_enabled;
        $isCanceled = $subscription && $subscription->status === 'canceled';
    @endphp

    @if($isPremium && !$isCanceled)
    {{-- ✅ プレミアム有効 --}}
    <div class="plan-banner premium">
        <span class="plan-badge premium">Premium</span>
        <p class="plan-name">プレミアムプラン</p>
        <p class="plan-sub">
            {{ $subscription?->billing_cycle === 'yearly' ? '年払い（¥3,000/年）' : '月払い（¥500/月）' }}
        </p>
        @if($subscription?->current_period_end)
        <p class="plan-sub" style="margin-top:6px;">
            次回更新: {{ $subscription->current_period_end->format('Y年n月j日') }}
        </p>
        @endif
    </div>

    @elseif($isCanceled)
    {{-- ⚠️ 解約済み（期間内） --}}
    <div class="plan-banner canceled">
        <span class="plan-badge canceled">解約済み</span>
        <p class="plan-name">プレミアムプラン（解約済み）</p>
        <p class="plan-end-date">
            📅 {{ $subscription->current_period_end->format('Y年n月j日') }} まで引き続きご利用いただけます
        </p>
    </div>

    @else
    {{-- 無料プラン --}}
    <div class="plan-banner free">
        <span class="plan-badge free">無料プラン</span>
        <p class="plan-name">無料プラン</p>
        <p class="plan-sub">メール通知・Webプッシュ通知</p>
    </div>
    @endif

    {{-- プレミアム機能一覧 --}}
    <section class="section">
        <h2 class="section-title"><span>✨</span>プレミアム機能</h2>
        <ul class="feature-list">
            <li>
                <span class="free-check">✓</span>
                <div><strong>メール通知</strong>（無料プランでも利用可）</div>
            </li>
            <li>
                <span class="free-check">✓</span>
                <div><strong>Webプッシュ通知</strong>（無料プランでも利用可）</div>
            </li>
            <li>
                <span class="check">✦</span>
                <div><strong>SMS通知</strong> — 電波があればどこでも受信できる確実な通知</div>
            </li>
            <li>
                <span class="check">✦</span>
                <div><strong>電話通知（AIコール）</strong> — 固定電話にも対応。最も確実な通知手段</div>
            </li>
            <li>
                <span class="check">✦</span>
                <div><strong>検知ログ1年保存</strong>（無料プランは90日）</div>
            </li>
        </ul>
    </section>

    @if(!$isPremium || $isCanceled)
    {{-- プラン選択 + 購読フォーム（未加入 or 解約済みの場合） --}}
    @if($isCanceled)
    <section class="section">
        <h2 class="section-title"><span>🔄</span>プレミアムを再開</h2>
    @else
    <section class="section">
        <h2 class="section-title"><span>🚀</span>プレミアムにアップグレード</h2>
    @endif

        {{-- 料金選択 --}}
        <div class="plan-compare">
            <div class="plan-card selected" id="cardMonthly" onclick="selectCycle('monthly')">
                <div class="plan-card-check">✓</div>
                <p class="plan-card-name">月払い</p>
                <p class="plan-card-price">¥500<span>/月</span></p>
                <p class="plan-card-note">いつでも解約可能</p>
            </div>
            <div class="plan-card" id="cardYearly" onclick="selectCycle('yearly')">
                <div class="plan-card-badge">2ヶ月分お得</div>
                <div class="plan-card-check"></div>
                <p class="plan-card-name">年払い</p>
                <p class="plan-card-price">¥3,000<span>/年</span></p>
                <p class="plan-card-note">¥250/月相当</p>
            </div>
        </div>

        {{-- カード入力 --}}
        <div class="card-form">
            <label class="form-label">クレジットカード</label>
            <div id="payjp-card-element" class="payjp-element"></div>

            <div class="error-msg" id="card-error"></div>

            <button class="btn-primary" id="subscribeBtn" style="margin-top:16px;" onclick="startSubscribe()">
                <span class="spinner"></span>
                <span class="btn-text">プレミアムを開始する</span>
            </button>

            <p class="secure-note">🔒 カード情報はPay.jpで安全に処理されます。当サービスのサーバーには保存されません。</p>
        </div>
    </section>

    @else
    {{-- プレミアム有効 → 解約ボタン --}}
    <section class="section">
        <h2 class="section-title"><span>⚙️</span>プランの管理</h2>
        <div class="info-box" style="margin-bottom:16px;">
            解約しても <strong>{{ $subscription?->current_period_end?->format('Y年n月j日') ?? '期間終了日' }}</strong> まで引き続きご利用いただけます。
        </div>
        <button class="btn-cancel-plan" onclick="cancelPlan()">プレミアムを解約する</button>
    </section>
    @endif

</div>
@endsection

@section('scripts')
<script src="https://js.pay.jp/v2/pay.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'Accept': 'application/json',
};

let selectedCycle = 'monthly';
let payjp, elements, cardElement;

// payjp.js 初期化（購読フォームが表示される場合のみ）
@if(!$isPremium || ($subscription && $subscription->status === 'canceled'))
window.addEventListener('load', function() {
    payjp = Payjp('{{ config("services.payjp.public_key") }}');
    elements = payjp.elements();
    cardElement = elements.create('card', {
        style: {
            base: {
                color: '#3d3935',
                fontFamily: '"Noto Sans JP", sans-serif',
                fontSize: '15px',
                '::placeholder': { color: '#a8a29e' },
            }
        },
        hidePostalCode: true,
    });
    cardElement.mount('#payjp-card-element');
    cardElement.on('focus', () => document.getElementById('payjp-card-element').classList.add('focused'));
    cardElement.on('blur',  () => document.getElementById('payjp-card-element').classList.remove('focused'));
});
@endif

// 支払いサイクル選択
function selectCycle(cycle) {
    selectedCycle = cycle;
    document.getElementById('cardMonthly').classList.toggle('selected', cycle === 'monthly');
    document.getElementById('cardYearly').classList.toggle('selected', cycle === 'yearly');
    document.getElementById('cardMonthly').querySelector('.plan-card-check').textContent = cycle === 'monthly' ? '✓' : '';
    document.getElementById('cardYearly').querySelector('.plan-card-check').textContent = cycle === 'yearly' ? '✓' : '';
}

// 購読開始
async function startSubscribe() {
    const btn = document.getElementById('subscribeBtn');
    const errEl = document.getElementById('card-error');
    errEl.style.display = 'none';

    btn.classList.add('loading');
    btn.disabled = true;

    try {
        // トークン取得
        const result = await payjp.createToken(cardElement);
        if (result.error) {
            errEl.textContent = result.error.message;
            errEl.style.display = 'block';
            btn.classList.remove('loading');
            btn.disabled = false;
            return;
        }

        // バックエンドへ
        const res = await fetch('/plan/subscribe', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                payjp_token: result.id,
                billing_cycle: selectedCycle,
            }),
        });
        const data = await res.json();

        if (data.ok) {
            showToast(data.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            errEl.textContent = data.message || '購読処理に失敗しました';
            errEl.style.display = 'block';
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    } catch (e) {
        errEl.textContent = '通信エラーが発生しました';
        errEl.style.display = 'block';
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

// 解約
async function cancelPlan() {
    if (!confirm('プレミアムプランを解約しますか？\n期間終了日まで引き続きご利用いただけます。')) return;

    const res = await fetch('/plan/cancel', {
        method: 'POST',
        headers: headers,
    });
    const data = await res.json();

    showToast(data.message || (data.ok ? '解約しました' : '解約に失敗しました'));
    if (data.ok) {
        setTimeout(() => location.reload(), 1500);
    }
}
</script>
@endsection
