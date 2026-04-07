@extends('layouts.partner')

@section('title', 'アカウント設定')

@section('styles')
<style>
    .settings-container { max-width: 600px; margin: 0 auto; padding: 24px 20px; }
    .settings-title { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 24px; text-align: center; }
    .settings-card { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); padding: 24px; margin-bottom: 20px; }
    .settings-card-title { font-size: 15px; font-weight: 700; color: var(--gray-800); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .form-group { margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
    .form-input-wrap { position: relative; }
    .form-input { width: 100%; padding: 10px 40px 10px 12px; font-size: 14px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-800); transition: border-color 0.2s, box-shadow 0.2s; }
    .form-input.plain { padding: 10px 12px; }
    .form-input:focus { outline: none; border-color: var(--gray-500); box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15); }
    .form-input.error { border-color: var(--red); }
    .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px; color: var(--gray-400); padding: 4px; }
    .toggle-password:hover { color: var(--gray-600); }
    .form-error { font-size: 12px; color: var(--red); margin-top: 4px; }
    .form-hint { font-size: 11px; color: var(--gray-400); margin-top: 4px; }
    .form-current-value { font-size: 13px; color: var(--gray-500); margin-bottom: 12px; padding: 8px 12px; background: var(--beige); border-radius: var(--radius); }
    .form-current-value strong { color: var(--gray-800); }
    .form-actions { margin-top: 20px; display: flex; justify-content: flex-end; }
    .btn-save { padding: 10px 24px; font-size: 14px; font-weight: 600; font-family: inherit; color: var(--white); background: var(--gray-800); border: none; border-radius: var(--radius); cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: var(--gray-700); }
    .btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
    .strength-bar { display: flex; gap: 4px; margin-top: 8px; }
    .strength-segment { flex: 1; height: 4px; background: var(--gray-200); border-radius: 2px; transition: background 0.3s; }
    .strength-label { font-size: 11px; margin-top: 4px; color: var(--gray-400); transition: color 0.3s; }
    .match-indicator { font-size: 11px; margin-top: 4px; }
    .match-indicator.match { color: var(--green-dark, #2e7d32); }
    .match-indicator.mismatch { color: var(--red); }

    /* カード情報 */
    .card-info-box { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: var(--beige); border-radius: var(--radius); margin-bottom: 16px; }
    .card-info-label { font-size: 13px; color: var(--gray-600); }
    .card-info-value { font-size: 14px; font-weight: 600; color: var(--gray-800); }
    .payjp-element { padding: 12px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--cream); min-height: 44px; }
    .payjp-element.focused { border-color: var(--gray-500); background: var(--white); }
    .btn-card { padding: 10px 24px; font-size: 14px; font-weight: 600; font-family: inherit; color: var(--white); background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: var(--radius); cursor: pointer; transition: opacity 0.2s; }
    .btn-card:hover { opacity: 0.9; }
    .btn-card:disabled { opacity: 0.5; cursor: not-allowed; }
    .error-msg { padding: 10px 14px; background: #fee2e2; border-radius: var(--radius); font-size: 13px; color: #991b1b; margin-top: 10px; display: none; }

    @media (max-width: 480px) {
        .settings-container { padding: 16px 12px; }
        .settings-card { padding: 16px; }
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <h1 class="settings-title">アカウント設定</h1>

    @if(session('success'))
        <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:var(--radius);margin-bottom:16px;font-size:13px;font-weight:600;">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#fbe9e7;color:#c62828;padding:12px 16px;border-radius:var(--radius);margin-bottom:16px;font-size:13px;font-weight:600;">⚠️ {{ session('error') }}</div>
    @endif

    {{-- メールアドレス変更 --}}
    <div class="settings-card">
        <h2 class="settings-card-title">📧 メールアドレス変更</h2>
        <div class="form-current-value">現在のメールアドレス: <strong>{{ $admin->email }}</strong></div>
        <form method="POST" action="{{ route('partner.email-change') }}" id="emailForm">
            @csrf
            <div class="form-group">
                <label class="form-label">新しいメールアドレス</label>
                <input type="email" class="form-input plain {{ $errors->has('email') ? 'error' : '' }}" name="email" value="{{ old('email') }}" placeholder="example@company.com">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">確認用パスワード</label>
                <div class="form-input-wrap">
                    <input type="password" class="form-input {{ $errors->has('email_password') ? 'error' : '' }}" id="email_password" name="email_password" placeholder="現在のパスワード">
                    <button type="button" class="toggle-password" onclick="toggleVisibility('email_password', this)">👁</button>
                </div>
                @error('email_password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save" id="emailSubmitBtn">メールアドレスを変更</button>
            </div>
        </form>
    </div>

    {{-- パスワード変更 --}}
    <div class="settings-card">
        <h2 class="settings-card-title">🔐 パスワード変更</h2>
        <form method="POST" action="{{ route('partner.password-change.update') }}" id="passwordForm">
            @csrf
            <div class="form-group">
                <label class="form-label">現在のパスワード</label>
                <div class="form-input-wrap">
                    <input type="password" class="form-input {{ $errors->has('current_password') ? 'error' : '' }}" id="current_password" name="current_password" placeholder="現在のパスワード">
                    <button type="button" class="toggle-password" onclick="toggleVisibility('current_password', this)">👁</button>
                </div>
                @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">新しいパスワード</label>
                <div class="form-input-wrap">
                    <input type="password" class="form-input {{ $errors->has('new_password') ? 'error' : '' }}" id="new_password" name="new_password" placeholder="8文字以上" oninput="checkStrength(this.value); checkMatch();">
                    <button type="button" class="toggle-password" onclick="toggleVisibility('new_password', this)">👁</button>
                </div>
                @error('new_password') <p class="form-error">{{ $message }}</p> @enderror
                <div class="strength-bar">
                    <div class="strength-segment" id="seg1"></div><div class="strength-segment" id="seg2"></div>
                    <div class="strength-segment" id="seg3"></div><div class="strength-segment" id="seg4"></div>
                    <div class="strength-segment" id="seg5"></div>
                </div>
                <p class="strength-label" id="strengthLabel"></p>
            </div>
            <div class="form-group">
                <label class="form-label">新しいパスワード（確認）</label>
                <div class="form-input-wrap">
                    <input type="password" class="form-input" id="new_password_confirmation" name="new_password_confirmation" placeholder="もう一度入力" oninput="checkMatch();">
                    <button type="button" class="toggle-password" onclick="toggleVisibility('new_password_confirmation', this)">👁</button>
                </div>
                <p class="match-indicator" id="matchIndicator"></p>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save" id="passwordSubmitBtn">パスワードを変更</button>
            </div>
        </form>
    </div>

    {{-- クレジットカード変更（組織があり、かつ契約がある場合のみ表示） --}}
    @if($organization && $contract)
    <div class="settings-card">
        <h2 class="settings-card-title">💳 クレジットカード変更</h2>
        @if($cardInfo)
        <div class="card-info-box">
            <span class="card-info-label">現在のカード</span>
            <span class="card-info-value">{{ $cardInfo['brand'] }} **** {{ $cardInfo['last4'] }}</span>
        </div>
        @endif
        <div class="form-group">
            <label class="form-label">新しいクレジットカード</label>
            <div id="account-card-element" class="payjp-element"></div>
        </div>
        <div class="error-msg" id="card-error"></div>
        <div class="form-actions">
            <button class="btn-card" id="cardUpdateBtn" onclick="updateCard()">カードを変更する</button>
        </div>
    </div>
    @endif

    {{-- 配送先プリセット（組織ありのみ表示） --}}
    @if($organization)
    <div class="settings-card">
        <h2 class="settings-card-title">📦 配送先プリセット</h2>
        <p style="font-size:12px;color:var(--gray-500);margin-bottom:16px;">デバイス追加時の配送先として自動入力されます。</p>
        <form method="POST" action="{{ route('partner.account-delivery') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">氏名</label>
                <input type="text" class="form-input plain" name="delivery_name" value="{{ old('delivery_name', $organization->delivery_name) }}" placeholder="山田 太郎">
                @error('delivery_name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">郵便番号</label>
                <input type="text" class="form-input plain" name="delivery_postal" value="{{ old('delivery_postal', $organization->delivery_postal) }}" placeholder="000-0000" maxlength="8">
                @error('delivery_postal') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">住所</label>
                <input type="text" class="form-input plain" name="delivery_address" value="{{ old('delivery_address', $organization->delivery_address) }}" placeholder="東京都千代田区〇〇 1-2-3">
                @error('delivery_address') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">電話番号</label>
                <input type="tel" class="form-input plain" name="delivery_phone" value="{{ old('delivery_phone', $organization->delivery_phone) }}" placeholder="090-0000-0000">
                @error('delivery_phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">配送先を保存</button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if($organization && $contract)
<script src="https://js.pay.jp/v2/pay.js"></script>
<script>
let payjp, elements, accountCardElement;
window.addEventListener('load', function() {
    payjp = Payjp('{{ config("services.payjp.public_key") }}');
    elements = payjp.elements();
    accountCardElement = elements.create('card', {
        style: { base: { color:'#3d3935', fontFamily:'"Noto Sans JP",sans-serif', fontSize:'14px', '::placeholder':{color:'#a8a29e'} } },
        hidePostalCode: true,
    });
    accountCardElement.mount('#account-card-element');
    accountCardElement.on('focus', () => document.getElementById('account-card-element').classList.add('focused'));
    accountCardElement.on('blur',  () => document.getElementById('account-card-element').classList.remove('focused'));
});

async function updateCard() {
    const btn = document.getElementById('cardUpdateBtn');
    const errEl = document.getElementById('card-error');
    errEl.style.display = 'none';
    btn.disabled = true;
    btn.textContent = '処理中...';

    try {
        const result = await payjp.createToken(accountCardElement);
        if (result.error) {
            errEl.textContent = result.error.message;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'カードを変更する';
            return;
        }

        const res = await fetch('{{ route("partner.account-card") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ payjp_token: result.id }),
        });
        const data = await res.json();

        if (data.ok) {
            location.reload();
        } else {
            errEl.textContent = data.message;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'カードを変更する';
        }
    } catch(e) {
        errEl.textContent = '通信エラーが発生しました';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'カードを変更する';
    }
}
</script>
@endif
<script>
function toggleVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁' : '🔒';
}

function checkStrength(pw) {
    var score = 0;
    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
    if (/\d/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;
    var colors = ['', '#ef5350', '#ff9800', '#ffc107', '#8bc34a', '#4caf50'];
    var labels = ['', '非常に弱い', '弱い', '普通', '強い', '非常に強い'];
    for (var i = 1; i <= 5; i++) {
        document.getElementById('seg' + i).style.background = i <= score ? colors[score] : 'var(--gray-200)';
    }
    var label = document.getElementById('strengthLabel');
    label.textContent = pw.length === 0 ? '' : (labels[score] || '非常に弱い');
    label.style.color = pw.length === 0 ? 'var(--gray-400)' : (colors[score] || '#ef5350');
}

function checkMatch() {
    var pw = document.getElementById('new_password').value;
    var confirm = document.getElementById('new_password_confirmation').value;
    var indicator = document.getElementById('matchIndicator');
    if (!confirm.length) { indicator.textContent = ''; indicator.className = 'match-indicator'; return; }
    if (pw === confirm) { indicator.textContent = '✓ パスワードが一致しています'; indicator.className = 'match-indicator match'; }
    else { indicator.textContent = '✗ パスワードが一致しません'; indicator.className = 'match-indicator mismatch'; }
}

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('passwordSubmitBtn');
    if (btn.disabled) { e.preventDefault(); return; }
    btn.disabled = true; btn.textContent = '変更中...';
});
document.getElementById('emailForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('emailSubmitBtn');
    if (btn.disabled) { e.preventDefault(); return; }
    btn.disabled = true; btn.textContent = '変更中...';
});
</script>
@endsection
