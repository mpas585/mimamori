@extends('layouts.partner')

@section('title', 'アカウント設定')

@section('styles')
<style>
    .settings-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 24px 20px;
    }
    .settings-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 24px;
        text-align: center;
    }
    .settings-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        padding: 24px;
        margin-bottom: 20px;
    }
    .settings-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
    }
    .form-input-wrap {
        position: relative;
    }
    .form-input {
        width: 100%;
        padding: 10px 40px 10px 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--white);
        color: var(--gray-800);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--gray-500);
        box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
    }
    .form-input.error {
        border-color: var(--red);
    }
    .form-input.readonly {
        background: var(--gray-100);
        color: var(--gray-500);
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: var(--gray-400);
        padding: 4px;
    }
    .toggle-password:hover {
        color: var(--gray-600);
    }
    .form-error {
        font-size: 12px;
        color: var(--red);
        margin-top: 4px;
    }
    .form-hint {
        font-size: 11px;
        color: var(--gray-400);
        margin-top: 4px;
    }
    .form-current-value {
        font-size: 13px;
        color: var(--gray-500);
        margin-bottom: 12px;
        padding: 8px 12px;
        background: var(--beige);
        border-radius: var(--radius);
    }
    .form-current-value strong {
        color: var(--gray-800);
    }
    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
    }
    .btn-save {
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        color: var(--white);
        background: var(--gray-800);
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover {
        background: var(--gray-700);
    }
    .btn-save:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* パスワード強度メーター */
    .strength-bar {
        display: flex;
        gap: 4px;
        margin-top: 8px;
    }
    .strength-segment {
        flex: 1;
        height: 4px;
        background: var(--gray-200);
        border-radius: 2px;
        transition: background 0.3s;
    }
    .strength-label {
        font-size: 11px;
        margin-top: 4px;
        color: var(--gray-400);
        transition: color 0.3s;
    }

    /* 一致チェック */
    .match-indicator {
        font-size: 11px;
        margin-top: 4px;
    }
    .match-indicator.match {
        color: var(--green-dark, #2e7d32);
    }
    .match-indicator.mismatch {
        color: var(--red);
    }

    @media (max-width: 480px) {
        .settings-container {
            padding: 16px 12px;
        }
        .settings-card {
            padding: 16px;
        }
    }
</style>
@endsection

@section('content')
    <div class="settings-container">
        <h1 class="settings-title">アカウント設定</h1>

        {{-- メールアドレス変更 --}}
        <div class="settings-card">
            <h2 class="settings-card-title">📧 メールアドレス変更</h2>

            <div class="form-current-value">
                現在のメールアドレス: <strong>{{ $admin->email }}</strong>
            </div>

            <form method="POST" action="{{ route('partner.email-change') }}" id="emailForm">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">新しいメールアドレス</label>
                    <input type="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="example@company.com" autocomplete="email">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email_password">確認用パスワード</label>
                    <div class="form-input-wrap">
                        <input type="password" class="form-input {{ $errors->has('email_password') ? 'error' : '' }}"
                               id="email_password" name="email_password"
                               placeholder="現在のパスワード" autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('email_password', this)">👁</button>
                    </div>
                    @error('email_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">セキュリティのため、パスワードを入力してください</p>
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
                    <label class="form-label" for="current_password">現在のパスワード</label>
                    <div class="form-input-wrap">
                        <input type="password" class="form-input {{ $errors->has('current_password') ? 'error' : '' }}"
                               id="current_password" name="current_password"
                               placeholder="現在のパスワード" autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('current_password', this)">👁</button>
                    </div>
                    @error('current_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">新しいパスワード</label>
                    <div class="form-input-wrap">
                        <input type="password" class="form-input {{ $errors->has('new_password') ? 'error' : '' }}"
                               id="new_password" name="new_password"
                               placeholder="8文字以上" autocomplete="new-password"
                               oninput="checkStrength(this.value); checkMatch();">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('new_password', this)">👁</button>
                    </div>
                    @error('new_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <div class="strength-bar">
                        <div class="strength-segment" id="seg1"></div>
                        <div class="strength-segment" id="seg2"></div>
                        <div class="strength-segment" id="seg3"></div>
                        <div class="strength-segment" id="seg4"></div>
                        <div class="strength-segment" id="seg5"></div>
                    </div>
                    <p class="strength-label" id="strengthLabel"></p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password_confirmation">新しいパスワード（確認）</label>
                    <div class="form-input-wrap">
                        <input type="password" class="form-input"
                               id="new_password_confirmation" name="new_password_confirmation"
                               placeholder="もう一度入力" autocomplete="new-password"
                               oninput="checkMatch();">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('new_password_confirmation', this)">👁</button>
                    </div>
                    <p class="match-indicator" id="matchIndicator"></p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save" id="passwordSubmitBtn">パスワードを変更</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// パスワード表示/非表示
function toggleVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🔒';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
    }
}

// パスワード強度メーター
function checkStrength(pw) {
    var score = 0;
    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
    if (/\d/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;

    var colors = ['', '#ef5350', '#ff9800', '#ffc107', '#8bc34a', '#4caf50'];
    var labels = ['', '非常に弱い', '弱い', '普通', '強い', '非常に強い'];
    var labelColors = ['', 'var(--red)', '#ff9800', '#ffc107', '#8bc34a', '#4caf50'];

    for (var i = 1; i <= 5; i++) {
        var seg = document.getElementById('seg' + i);
        seg.style.background = i <= score ? colors[score] : 'var(--gray-200)';
    }

    var label = document.getElementById('strengthLabel');
    if (pw.length === 0) {
        label.textContent = '';
        label.style.color = 'var(--gray-400)';
    } else {
        label.textContent = labels[score] || '非常に弱い';
        label.style.color = labelColors[score] || 'var(--red)';
    }
}

// パスワード一致チェック
function checkMatch() {
    var pw = document.getElementById('new_password').value;
    var confirm = document.getElementById('new_password_confirmation').value;
    var indicator = document.getElementById('matchIndicator');

    if (confirm.length === 0) {
        indicator.textContent = '';
        indicator.className = 'match-indicator';
        return;
    }

    if (pw === confirm) {
        indicator.textContent = '✓ パスワードが一致しています';
        indicator.className = 'match-indicator match';
    } else {
        indicator.textContent = '✗ パスワードが一致しません';
        indicator.className = 'match-indicator mismatch';
    }
}

// 二重送信防止
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('passwordSubmitBtn');
    if (btn.disabled) {
        e.preventDefault();
        return;
    }
    btn.disabled = true;
    btn.textContent = '変更中...';
});

document.getElementById('emailForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('emailSubmitBtn');
    if (btn.disabled) {
        e.preventDefault();
        return;
    }
    btn.disabled = true;
    btn.textContent = '変更中...';
});
</script>
@endsection
