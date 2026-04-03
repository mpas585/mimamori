@extends('layouts.app')

@section('title', '設定 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner" style="max-width:640px;">
        <a href="/mypage" class="back-btn">←</a>
        <h1 style="font-size:16px;font-weight:600;flex:1;text-align:center;">設定</h1>
        <span style="font-size:13px;font-weight:600;color:var(--gray-500);font-family:monospace;letter-spacing:0.05em;">{{ $device->device_id }}</span>
    </div>
</header>
@endsection

@section('styles')
<style>
    .back-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--beige);
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
        text-decoration: none;
        color: var(--gray-700);
    }
    .back-btn:hover { background: var(--gray-200); }

    .container {
        max-width: 640px;
        margin: 0 auto;
        padding: 24px 20px;
    }
    .section {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gray-200);
    }
    .section-title span { font-size: 18px; }

    .setting-item {
        display: flex;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .setting-item:last-child { border-bottom: none; }
    .setting-item:first-of-type { padding-top: 0; }
    .setting-icon {
        width: 44px;
        height: 44px;
        background: var(--beige);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 16px;
        flex-shrink: 0;
        border: 1px solid var(--gray-200);
    }
    .setting-content { flex: 1; min-width: 0; }
    .setting-label {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--gray-800);
    }
    .setting-value {
        font-size: 13px;
        color: var(--gray-500);
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .edit-link {
        font-size: 12px;
        color: var(--blue);
        text-decoration: none;
        margin-left: 8px;
        font-weight: 500;
        cursor: pointer;
    }
    .edit-link:hover { text-decoration: underline; }
    .setting-action { margin-left: 12px; flex-shrink: 0; }

    .toggle {
        position: relative;
        width: 52px;
        height: 30px;
        background: var(--gray-300);
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }
    .toggle.active { background: var(--green); }
    .toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background: var(--white);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }
    .toggle.active::after { left: 25px; }

    .premium-badge {
        display: inline-block;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 500;
        color: var(--white);
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 10px;
        margin-left: 8px;
    }

    .test-notify-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
    }
    .test-notify-btn {
        width: 100%;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        background: var(--beige);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .test-notify-btn:hover { background: var(--gray-200); }
    .test-notify-btn:active { transform: scale(0.98); }
    .test-notify-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .test-notify-btn .spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid var(--gray-300);
        border-top-color: var(--gray-600);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .test-notify-btn.loading .spinner { display: block; }
    .test-notify-btn.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .test-notify-hint {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 8px;
        text-align: center;
    }

    .alert-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .alert-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
    }
    .alert-option:hover { border-color: var(--gray-400); background: var(--gray-100); }
    .alert-option.selected { border-color: var(--gray-800); background: var(--beige); }
    .alert-radio {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray-300);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .alert-option.selected .alert-radio { border-color: var(--gray-800); }
    .alert-option.selected .alert-radio::after {
        content: '';
        width: 10px;
        height: 10px;
        background: var(--gray-800);
        border-radius: 50%;
    }
    .alert-option-content { flex: 1; }
    .alert-option-title { font-size: 15px; font-weight: 600; color: var(--gray-800); }
    .alert-option-desc { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
    .alert-option-badge { font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
    .alert-option-badge.default { background: var(--green-light); color: var(--green-dark); }
    .info-box {
        padding: 14px 16px;
        background: var(--blue-light);
        border-radius: var(--radius);
        font-size: 13px;
        color: var(--gray-700);
        line-height: 1.7;
        margin-top: 16px;
    }
    .info-box strong { color: var(--gray-800); }

    .feature-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: var(--beige);
        border-radius: var(--radius);
        margin-bottom: 20px;
    }
    .feature-toggle-info { flex: 1; }
    .feature-toggle-label { font-size: 15px; font-weight: 600; color: var(--gray-800); }
    .feature-toggle-desc { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
    .pet-settings { transition: all 0.3s ease; overflow: hidden; }
    .pet-settings.hidden { display: none; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; }
    .form-hint { font-size: 11px; color: var(--gray-500); margin-top: 6px; }
    .form-input {
        width: 100%;
        padding: 12px 14px;
        font-size: 16px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
        transition: all 0.2s;
    }
    .form-input:focus { outline: none; border-color: var(--gray-500); background: var(--white); }
    .input-with-unit { display: flex; align-items: center; gap: 8px; max-width: 200px; }
    .input-with-unit input { flex: 1; text-align: right; }
    .input-unit { font-size: 14px; font-weight: 500; color: var(--gray-600); min-width: 30px; }
    .range-info { display: flex; justify-content: space-between; font-size: 10px; color: var(--gray-400); margin-top: 4px; max-width: 200px; }

    .visual-preview { background: var(--beige); border-radius: var(--radius); padding: 20px; margin-top: 20px; text-align: center; }
    .visual-preview-title { font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 16px; }
    #diagramCanvas { display: block; margin: 0 auto; background: var(--white); border-radius: var(--radius); border: 1px solid var(--gray-200); }
    .legend { display: flex; justify-content: center; gap: 20px; margin-top: 12px; font-size: 12px; color: var(--gray-600); }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-color { width: 16px; height: 16px; border-radius: 3px; }
    .legend-color.detect { background: rgba(34,197,94,0.3); border: 1px solid var(--green); }
    .legend-color.exclude { background: rgba(168,162,158,0.3); border: 1px solid var(--gray-400); }
    .area-display { text-align: center; margin-top: 16px; padding: 14px; background: var(--green-light); border-radius: var(--radius); }
    .area-label { font-size: 12px; color: var(--gray-600); margin-bottom: 4px; }
    .area-value { font-size: 20px; font-weight: 700; color: var(--green-dark); }
    .explanation-box { margin-top: 16px; padding: 14px 16px; background: var(--blue-light); border-radius: var(--radius); font-size: 13px; color: var(--gray-700); line-height: 1.7; }
    .explanation-box strong { color: var(--gray-800); }

    /* 電話番号入力モーダル */
    .phone-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .phone-modal-overlay.open { display: flex; }
    .phone-modal {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 28px 24px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .phone-modal-title { font-size: 16px; font-weight: 700; color: var(--gray-800); margin-bottom: 20px; }
    .phone-modal-hint { font-size: 12px; color: var(--gray-500); margin-bottom: 6px; }
    .phone-modal-actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn-cancel { flex: 1; padding: 12px; font-size: 14px; font-family: inherit; background: var(--beige); color: var(--gray-700); border: 1px solid var(--gray-300); border-radius: var(--radius); cursor: pointer; }
    .btn-save { flex: 1; padding: 12px; font-size: 14px; font-weight: 600; font-family: inherit; background: var(--gray-800); color: var(--white); border: none; border-radius: var(--radius); cursor: pointer; }
    .btn-save:hover { background: var(--gray-700); }

    @media (max-width: 480px) {
        .input-with-unit { max-width: 100%; }
        .range-info { max-width: 100%; }
        .alert-option { padding: 12px 14px; }
    }
</style>
@endsection

@section('content')
<div class="container">

    {{-- 通知方法 --}}
    <section class="section">
        <h2 class="section-title"><span>🔔</span>通知方法</h2>

        <div class="setting-item">
            <div class="setting-icon">📧</div>
            <div class="setting-content">
                <p class="setting-label">メール通知</p>
                <p class="setting-value">
                    @if($notif->email_1)
                        {{ $notif->email_1 }}
                        <a href="/email-settings" class="edit-link">編集</a>
                    @else
                        <span style="color:var(--red);">未登録</span>
                        <a href="/email-settings" class="edit-link">登録する</a>
                    @endif
                </p>
            </div>
            <div class="setting-action">
                <button class="toggle {{ $notif->email_enabled ? 'active' : '' }}" id="toggleEmail" onclick="toggleNotif('email_enabled', this)"></button>
            </div>
        </div>

        <div class="setting-item">
            <div class="setting-icon">💬</div>
            <div class="setting-content">
                <p class="setting-label">SMS通知<span class="premium-badge">Premium</span></p>
                <p class="setting-value">
                    @if($notif->sms_phone_1)
                        {{ $notif->sms_phone_1 }}
                        <a class="edit-link" onclick="openPhoneModal('sms')">編集</a>
                    @else
                        <span style="color:var(--gray-400);">未設定</span>
                        <a class="edit-link" onclick="openPhoneModal('sms')">設定する</a>
                    @endif
                </p>
            </div>
            <div class="setting-action">
                <button class="toggle {{ $notif->sms_enabled ? 'active' : '' }}" id="toggleSms" onclick="toggleNotif('sms_enabled', this)"></button>
            </div>
        </div>

        <div class="setting-item">
            <div class="setting-icon">📞</div>
            <div class="setting-content">
                <p class="setting-label">電話通知（AIコール）<span class="premium-badge">Premium</span></p>
                <p class="setting-value">
                    @if($notif->voice_phone_1)
                        {{ $notif->voice_phone_1 }}
                        <a class="edit-link" onclick="openPhoneModal('voice')">編集</a>
                    @else
                        <span style="color:var(--gray-400);">未設定</span>
                        <a class="edit-link" onclick="openPhoneModal('voice')">設定する</a>
                    @endif
                </p>
            </div>
            <div class="setting-action">
                <button class="toggle {{ $notif->voice_enabled ? 'active' : '' }}" id="toggleVoice" onclick="toggleNotif('voice_enabled', this)"></button>
            </div>
        </div>

        {{-- テスト通知 --}}
        <div class="test-notify-section">
            <button class="test-notify-btn" id="testNotifyBtn" onclick="sendTestNotification()">
                <span class="spinner"></span>
                <span class="btn-text">📨 テスト通知を送信</span>
            </button>
            <p class="test-notify-hint">有効な通知先にテストメッセージを送信します</p>
        </div>
    </section>

    {{-- 未検知アラート --}}
    <section class="section">
        <h2 class="section-title"><span>⏰</span>未検知アラート</h2>
        <div class="alert-options">
            @foreach([
                12 => ['title' => '12時間', 'desc' => '早期発見重視。頻繁に外出しない方向け'],
                24 => ['title' => '24時間', 'desc' => '標準的な設定。多くの方におすすめ'],
                36 => ['title' => '36時間', 'desc' => '外出が多い方向け'],
                48 => ['title' => '48時間', 'desc' => '長時間外出が多い方向け'],
                72 => ['title' => '72時間', 'desc' => '最長設定。旅行が多い方向け'],
            ] as $hours => $info)
            <div class="alert-option {{ $device->alert_threshold_hours == $hours ? 'selected' : '' }}" onclick="selectAlert(this, {{ $hours }})">
                <div class="alert-radio"></div>
                <div class="alert-option-content">
                    <p class="alert-option-title">{{ $info['title'] }}</p>
                    <p class="alert-option-desc">{{ $info['desc'] }}</p>
                </div>
                @if($hours === 24)
                    <span class="alert-option-badge default">推奨</span>
                @endif
            </div>
            @endforeach
        </div>

        <div class="info-box">
            <strong>💡 ポイント</strong><br>
            人の動きが検知されない時間がこの設定を超えると、通知が届きます。<br>
            外泊や旅行時は「タイマー設定」機能をご利用ください。
        </div>
    </section>

    {{-- ペット除外設定 --}}
    <section class="section">
        <h2 class="section-title"><span>🐱</span>ペット除外設定</h2>
        <div class="feature-toggle">
            <div class="feature-toggle-info">
                <p class="feature-toggle-label">ペット除外機能</p>
                <p class="feature-toggle-desc">ペットの検知を除外して誤通知を防ぎます</p>
            </div>
            <button class="toggle {{ $device->pet_exclusion_enabled ? 'active' : '' }}" id="petToggle" onclick="togglePetFeature()"></button>
        </div>
        <div class="pet-settings {{ $device->pet_exclusion_enabled ? '' : 'hidden' }}" id="petSettings">
            <div class="form-group">
                <label class="form-label">設置高さ（床からセンサー）</label>
                <div class="input-with-unit">
                    <input type="number" class="form-input" id="installHeight" value="{{ $device->install_height_cm }}" min="150" max="250" oninput="updateDiagram()" onblur="saveHeight()">
                    <span class="input-unit">cm</span>
                </div>
                <div class="range-info"><span>下限: 150cm</span><span>上限: 250cm</span></div>
                <p class="form-hint">ドア枠上部の高さを入力してください</p>
            </div>
            <div class="visual-preview">
                <p class="visual-preview-title">設置シミュレーション</p>
                <canvas id="diagramCanvas" width="280" height="280"></canvas>
                <div class="legend">
                    <div class="legend-item"><div class="legend-color detect"></div>検知エリア</div>
                    <div class="legend-item"><div class="legend-color exclude"></div>除外エリア</div>
                </div>
                <div class="area-display">
                    <p class="area-label">除外エリア（設置高さの半分より下）</p>
                    <p class="area-value">床から <span id="excludeHeight">{{ intval($device->install_height_cm / 2) }}</span>cm まで</p>
                </div>
            </div>
            <div class="explanation-box">
                💡 <strong>検知エリア</strong>: 人間が通ると検知して通知します<br>
                <strong>除外エリア</strong>: ペットが通っても検知されません
            </div>
        </div>
    </section>

</div>

{{-- 電話番号入力モーダル --}}
<div class="phone-modal-overlay" id="phoneModalOverlay" onclick="closePhoneModal(event)">
    <div class="phone-modal">
        <p class="phone-modal-title" id="phoneModalTitle">SMS通知先を設定</p>
        <p class="phone-modal-hint">電話番号1（必須）</p>
        <input type="tel" class="form-input" id="phoneInput1" placeholder="+819012345678" style="margin-bottom:12px;">
        <p class="phone-modal-hint">電話番号2（任意）</p>
        <input type="tel" class="form-input" id="phoneInput2" placeholder="+819012345678">

        <div class="phone-modal-actions">
            <button class="btn-cancel" onclick="closePhoneModal()">キャンセル</button>
            <button class="btn-save" onclick="savePhone()">保存</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'Accept': 'application/json',
};

// ==== 通知トグル ====
function toggleNotif(field, el) {
    el.classList.toggle('active');
    const val = el.classList.contains('active') ? 1 : 0;
    fetch('/settings/notification', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ [field]: val })
    });
    showToast('保存しました');
}

// ==== テスト通知 ====
function sendTestNotification() {
    const btn = document.getElementById('testNotifyBtn');
    btn.classList.add('loading');
    btn.disabled = true;

    fetch('/settings/test-notification', {
        method: 'POST',
        headers: headers,
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.remove('loading');
        btn.disabled = false;
        showToast(data.message || 'テスト通知を送信しました');
    })
    .catch(() => {
        btn.classList.remove('loading');
        btn.disabled = false;
        showToast('送信に失敗しました');
    });
}

// ==== アラート時間選択 ====
function selectAlert(el, hours) {
    document.querySelectorAll('.alert-option').forEach(opt => opt.classList.remove('selected'));
    el.classList.add('selected');
    fetch('/settings/device', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ alert_threshold_hours: hours })
    });
    showToast('保存しました');
}

// ==== ペット除外 ====
function togglePetFeature() {
    const toggle = document.getElementById('petToggle');
    const settings = document.getElementById('petSettings');
    toggle.classList.toggle('active');
    const enabled = toggle.classList.contains('active');
    if (enabled) {
        settings.classList.remove('hidden');
        updateDiagram();
    } else {
        settings.classList.add('hidden');
    }
    fetch('/settings/device', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ pet_exclusion_enabled: enabled ? 1 : 0 })
    });
    showToast('保存しました');
}

let lastHeight = {{ $device->install_height_cm }};
function saveHeight() {
    const input = document.getElementById('installHeight');
    let v = parseInt(input.value) || 200;
    if (v < 150) v = 150;
    if (v > 250) v = 250;
    input.value = v;
    if (v !== lastHeight) {
        lastHeight = v;
        fetch('/settings/device', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ install_height_cm: v })
        });
        showToast('保存しました');
    }
}

// ==== 電話番号モーダル ====
let currentPhoneType = 'sms';

function openPhoneModal(type) {
    currentPhoneType = type;
    const title = type === 'sms' ? 'SMS通知先を設定' : '電話通知先を設定';
    document.getElementById('phoneModalTitle').textContent = title;
    document.getElementById('phoneInput1').value = '';
    document.getElementById('phoneInput2').value = '';
    document.getElementById('phoneModalOverlay').classList.add('open');
}

function closePhoneModal(event) {
    if (!event || event.target === document.getElementById('phoneModalOverlay')) {
        document.getElementById('phoneModalOverlay').classList.remove('open');
    }
}

function savePhone() {
    const phone1 = document.getElementById('phoneInput1').value.trim();
    const phone2 = document.getElementById('phoneInput2').value.trim();

    const field1 = currentPhoneType === 'sms' ? 'sms_phone_1' : 'voice_phone_1';
    const field2 = currentPhoneType === 'sms' ? 'sms_phone_2' : 'voice_phone_2';

    fetch('/settings/notification', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ [field1]: phone1, [field2]: phone2 })
    })
    .then(r => r.json())
    .then(() => {
        closePhoneModal();
        showToast('保存しました');
        setTimeout(() => location.reload(), 800);
    })
    .catch(() => showToast('保存に失敗しました'));
}

// ==== Canvas 設置シミュレーション ====
const canvas = document.getElementById('diagramCanvas');
const ctx = canvas ? canvas.getContext('2d') : null;

function updateDiagram() {
    if (!ctx) return;
    let h = parseInt(document.getElementById('installHeight').value) || 200;
    if (h < 150) h = 150;
    if (h > 250) h = 250;
    const exH = Math.floor(h / 2);
    document.getElementById('excludeHeight').textContent = exH;

    const W = canvas.width, H = canvas.height;
    const floorY = H - 25, doorX = 75, doorW = 100;
    const scale = (floorY - 35) / 250;
    ctx.clearRect(0, 0, W, H);

    ctx.fillStyle = '#a8a29e';
    ctx.fillRect(20, floorY, W - 40, 3);

    const doorTop = floorY - h * scale;
    ctx.strokeStyle = '#78716c';
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.moveTo(doorX, floorY);
    ctx.lineTo(doorX, doorTop);
    ctx.lineTo(doorX + doorW, doorTop);
    ctx.lineTo(doorX + doorW, floorY);
    ctx.stroke();

    const boundaryY = floorY - exH * scale;

    ctx.fillStyle = 'rgba(34, 197, 94, 0.15)';
    ctx.fillRect(doorX + 2, doorTop + 2, doorW - 4, boundaryY - doorTop - 2);

    ctx.fillStyle = 'rgba(168, 162, 158, 0.2)';
    ctx.fillRect(doorX + 2, boundaryY, doorW - 4, floorY - boundaryY);

    const sX = doorX + doorW / 2;
    ctx.fillStyle = '#fff';
    ctx.strokeStyle = '#57534e';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(sX - 16, doorTop - 2);
    ctx.lineTo(sX + 16, doorTop - 2);
    ctx.lineTo(sX + 16, doorTop + 8);
    ctx.lineTo(sX - 16, doorTop + 8);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    ctx.fillStyle = '#22c55e';
    ctx.beginPath();
    ctx.arc(sX, doorTop + 3, 2, 0, Math.PI * 2);
    ctx.fill();

    const sY = doorTop + 6;
    const beamH = boundaryY - sY;
    const spread = beamH * 0.15;
    const grad = ctx.createLinearGradient(sX, sY, sX, boundaryY);
    grad.addColorStop(0, 'rgba(34, 197, 94, 0.6)');
    grad.addColorStop(0.7, 'rgba(34, 197, 94, 0.2)');
    grad.addColorStop(1, 'rgba(34, 197, 94, 0.05)');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.moveTo(sX - 2, sY);
    ctx.lineTo(sX + 2, sY);
    ctx.lineTo(sX + spread, boundaryY);
    ctx.lineTo(sX - spread, boundaryY);
    ctx.closePath();
    ctx.fill();

    ctx.setLineDash([5, 4]);
    ctx.strokeStyle = '#22c55e';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(doorX + 6, boundaryY);
    ctx.lineTo(doorX + doorW - 6, boundaryY);
    ctx.stroke();
    ctx.setLineDash([]);

    drawCat(ctx, sX - 12, floorY - 25 * scale, 25 * scale, '#57534e');

    ctx.font = '10px "Noto Sans JP", sans-serif';
    ctx.fillStyle = '#57534e';
    drawDim(ctx, doorX - 22, floorY, doorTop, h + 'cm');

    ctx.fillStyle = '#78716c';
    ctx.textAlign = 'left';
    ctx.fillText(exH + 'cm', doorX + doorW + 8, boundaryY + 4);

    ctx.textAlign = 'center';
    ctx.fillStyle = '#16a34a';
    ctx.fillText('検知', sX, doorTop + (boundaryY - doorTop) / 2 + 3);
    ctx.fillStyle = '#78716c';
    ctx.fillText('除外', sX, boundaryY + (floorY - boundaryY) / 2 + 3);
}

function drawCat(ctx, x, y, h, c) {
    h = Math.max(h, 12);
    const bL = h * 1.2, bH = h * 0.5, lH = h * 0.35, hR = h * 0.35;
    const bY = y + h - lH - bH / 2;
    const bCX = x + bL / 2;
    const hX = x + bL + hR * 0.3, hY = bY - hR * 0.1;
    const eyeY = hY - hR * 0.15;

    ctx.fillStyle = c;
    ctx.beginPath();
    ctx.ellipse(bCX, bY, bL / 2, bH / 2, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.beginPath();
    ctx.arc(hX, hY, hR, 0, Math.PI * 2);
    ctx.fill();

    ctx.beginPath();
    ctx.moveTo(hX - hR * 0.7, hY - hR * 0.2);
    ctx.lineTo(hX - hR * 0.35, hY - hR * 1.3);
    ctx.lineTo(hX, hY - hR * 0.4);
    ctx.fill();
    ctx.beginPath();
    ctx.moveTo(hX, hY - hR * 0.4);
    ctx.lineTo(hX + hR * 0.35, hY - hR * 1.3);
    ctx.lineTo(hX + hR * 0.7, hY - hR * 0.2);
    ctx.fill();

    ctx.fillStyle = '#fff';
    ctx.beginPath();
    ctx.ellipse(hX - hR * 0.3, eyeY, hR * 0.12, hR * 0.15, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.beginPath();
    ctx.ellipse(hX + hR * 0.3, eyeY, hR * 0.12, hR * 0.15, 0, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = '#fff';
    ctx.lineWidth = Math.max(1, h * 0.02);
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.arc(hX - hR * 0.15, hY + hR * 0.35, hR * 0.15, Math.PI, 0, true);
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(hX + hR * 0.15, hY + hR * 0.35, hR * 0.15, Math.PI, 0, true);
    ctx.stroke();

    ctx.fillStyle = c;
    const lW = h * 0.1;
    ctx.fillRect(x + bL * 0.2 - lW / 2, bY + bH / 4, lW, lH);
    ctx.fillRect(x + bL * 0.4 - lW / 2, bY + bH / 4, lW, lH);
    ctx.fillRect(x + bL * 0.6 - lW / 2, bY + bH / 4, lW, lH);
    ctx.fillRect(x + bL * 0.8 - lW / 2, bY + bH / 4, lW, lH);

    ctx.strokeStyle = c;
    ctx.lineWidth = h * 0.08;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.moveTo(x, bY);
    ctx.quadraticCurveTo(x - h * 0.25, bY - h * 0.35, x - h * 0.05, bY - h * 0.55);
    ctx.stroke();
}

function drawDim(ctx, x, y1, y2, label) {
    ctx.strokeStyle = '#a8a29e';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(x, y1);
    ctx.lineTo(x, y2);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x - 3, y1);
    ctx.lineTo(x + 3, y1);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x - 3, y2);
    ctx.lineTo(x + 3, y2);
    ctx.stroke();

    ctx.save();
    ctx.translate(x, (y1 + y2) / 2);
    ctx.rotate(-Math.PI / 2);
    ctx.fillStyle = '#78716c';
    ctx.font = '11px "Noto Sans JP"';
    ctx.textAlign = 'center';
    ctx.fillText(label, 0, -6);
    ctx.restore();
}

if (document.getElementById('petSettings') && !document.getElementById('petSettings').classList.contains('hidden')) {
    updateDiagram();
}
</script>
@endsection
