@extends('layouts.app')

@section('title', 'お問い合わせ - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner">
        <a href="javascript:history.back()" class="header-btn" style="font-size: 18px; padding: 8px 10px;">←</a>
        <span class="header-logo-text">お問い合わせ</span>
        <div style="width: 36px;"></div>
    </div>
</header>
@endsection

@section('styles')
<style>
    .page-title {
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 24px;
    }

    /* クイックリンク */
    .quick-links {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    .quick-link {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 16px;
        text-decoration: none;
        color: var(--gray-800);
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
    }
    .quick-link:hover {
        border-color: var(--gray-400);
        background: var(--gray-100);
    }
    .quick-link-icon {
        font-size: 24px;
    }
    .quick-link-title {
        font-size: 13px;
        font-weight: 600;
    }
    .quick-link-desc {
        font-size: 11px;
        color: var(--gray-500);
    }

    /* カード */
    .contact-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 28px 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        margin-bottom: 20px;
    }
    .contact-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gray-200);
    }
    .contact-card-title span {
        font-size: 20px;
    }

    /* 返信時間 */
    .response-time {
        background: var(--blue-light);
        border-radius: var(--radius);
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .response-time-icon {
        font-size: 16px;
    }

    /* フォーム */
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    .form-label .required {
        color: var(--red);
        margin-left: 4px;
        font-size: 11px;
    }
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 14px 16px;
        font-size: 15px;
        font-family: inherit;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        background: var(--cream);
        color: var(--gray-800);
        transition: all 0.2s;
    }
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--gray-500);
        background: var(--white);
        box-shadow: 0 0 0 3px rgba(168, 162, 158, 0.15);
    }
    .form-input::placeholder,
    .form-textarea::placeholder {
        color: var(--gray-400);
    }
    .form-textarea {
        min-height: 150px;
        resize: vertical;
    }
    .form-hint {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 6px;
    }
    .form-error {
        color: var(--red);
        font-size: 12px;
        margin-top: 6px;
    }
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2357534e' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        cursor: pointer;
    }

    /* デバイスID入力 */
    .device-id-input {
        display: flex;
        gap: 8px;
    }
    .device-id-input input {
        flex: 1;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .optional-badge {
        padding: 14px 12px;
        font-size: 12px;
        color: var(--gray-500);
        background: var(--beige);
        border-radius: var(--radius);
        white-space: nowrap;
    }

    /* 同意チェック */
    .privacy-agree {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 24px;
        padding: 16px;
        background: var(--beige);
        border-radius: var(--radius);
    }
    .privacy-agree input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        cursor: pointer;
        accent-color: var(--gray-700);
    }
    .privacy-agree label {
        font-size: 13px;
        color: var(--gray-700);
        cursor: pointer;
    }
    .privacy-agree a {
        color: var(--blue);
    }

    /* 送信ボタン */
    .btn-primary {
        display: block;
        width: 100%;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 600;
        font-family: inherit;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--gray-800);
        color: var(--white);
        text-align: center;
    }
    .btn-primary:hover:not(:disabled) {
        background: var(--gray-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }
    .btn-primary:disabled {
        background: var(--gray-300);
        cursor: not-allowed;
        transform: none;
    }

    /* FAQ */
    .faq-section {
        margin-top: 32px;
    }
    .faq-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 16px;
    }
    .faq-list {
        list-style: none;
    }
    .faq-item {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        margin-bottom: 8px;
    }
    .faq-question {
        padding: 14px 16px;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .faq-question::after {
        content: '+';
        font-size: 18px;
        color: var(--gray-400);
        transition: transform 0.2s;
    }
    .faq-item.open .faq-question::after {
        transform: rotate(45deg);
    }
    .faq-answer {
        display: none;
        padding: 0 16px 14px;
        font-size: 13px;
        color: var(--gray-600);
        line-height: 1.8;
    }
    .faq-item.open .faq-answer {
        display: block;
    }

    /* 送信完了画面 */
    .success-screen {
        display: none;
        text-align: center;
        padding: 40px 20px;
    }
    .success-screen.show {
        display: block;
    }
    .success-icon {
        width: 80px;
        height: 80px;
        background: var(--green-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 40px;
    }
    .success-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .success-text {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.8;
        margin-bottom: 32px;
    }
    .btn-secondary {
        display: inline-block;
        padding: 14px 32px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        background: var(--beige);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-secondary:hover {
        background: var(--gray-200);
    }

    @media (max-width: 480px) {
        .quick-links {
            grid-template-columns: 1fr;
        }
        .device-id-input {
            flex-direction: column;
        }
        .device-id-input .optional-badge {
            text-align: center;
        }
        .contact-card {
            padding: 20px 16px;
        }
    }
</style>
@endsection

@section('content')
<h1 class="page-title">お問い合わせ</h1>

{{-- クイックリンク --}}
<div class="quick-links">
    <a href="/guide" class="quick-link">
        <span class="quick-link-icon">📖</span>
        <span class="quick-link-title">使い方ガイド</span>
        <span class="quick-link-desc">設置・操作方法</span>
    </a>
    <a href="/trouble" class="quick-link">
        <span class="quick-link-icon">🔧</span>
        <span class="quick-link-title">故障・交換申請</span>
        <span class="quick-link-desc">不具合の報告</span>
    </a>
    <a href="/terms" class="quick-link">
        <span class="quick-link-icon">📋</span>
        <span class="quick-link-title">利用規約</span>
        <span class="quick-link-desc">サービス規約</span>
    </a>
    <a href="/privacy" class="quick-link">
        <span class="quick-link-icon">🛡️</span>
        <span class="quick-link-title">プライバシー</span>
        <span class="quick-link-desc">個人情報保護</span>
    </a>
</div>

{{-- お問い合わせフォーム --}}
<div id="formSection">
    <div class="contact-card">
        <h2 class="contact-card-title"><span>✉️</span>お問い合わせフォーム</h2>

        <div class="response-time">
            <span class="response-time-icon">⏱️</span>
            通常2〜3営業日以内にご返信いたします
        </div>

        <form method="POST" action="/contact" id="contactForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="category">お問い合わせ種別<span class="required">必須</span></label>
                <select id="category" name="category" class="form-select" required>
                    <option value="" disabled selected>選択してください</option>
                    <option value="purchase" {{ old('category') == 'purchase' ? 'selected' : '' }}>購入前のご質問</option>
                    <option value="usage" {{ old('category') == 'usage' ? 'selected' : '' }}>使い方・設定について</option>
                    <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>料金・お支払いについて</option>
                    <option value="bulk" {{ old('category') == 'bulk' ? 'selected' : '' }}>法人・大量購入のご相談</option>
                    <option value="data" {{ old('category') == 'data' ? 'selected' : '' }}>データの開示・削除請求</option>
                    <option value="report" {{ old('category') == 'report' ? 'selected' : '' }}>不正利用の通報</option>
                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>その他</option>
                </select>
                @error('category')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="device_id">デバイスID</label>
                <div class="device-id-input">
                    <input type="text" id="device_id" name="device_id" class="form-input" placeholder="A3K9X2" value="{{ old('device_id') }}" maxlength="6">
                    <span class="optional-badge">任意</span>
                </div>
                <p class="form-hint">お持ちの場合はご入力ください（製品ラベルに記載）</p>
                @error('device_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">メールアドレス<span class="required">必須</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="example@email.com" value="{{ old('email') }}" required>
                <p class="form-hint">返信はこちらのアドレスにお送りします</p>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">お名前<span class="required">必須</span></label>
                <input type="text" id="name" name="name" class="form-input" placeholder="山田 太郎" value="{{ old('name') }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="message">お問い合わせ内容<span class="required">必須</span></label>
                <textarea id="message" name="message" class="form-textarea" placeholder="お問い合わせ内容をご記入ください" required>{{ old('message') }}</textarea>
                @error('message')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="privacy-agree">
                <input type="checkbox" id="privacyAgree" required>
                <label for="privacyAgree">
                    <a href="/privacy" target="_blank">プライバシーポリシー</a>に同意の上、送信します
                </label>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">送信する</button>
        </form>
    </div>

    {{-- よくある質問 --}}
    <div class="faq-section">
        <h2 class="faq-title">💡 よくあるご質問</h2>
        <ul class="faq-list">
            <li class="faq-item">
                <div class="faq-question">電波が届くか事前に確認できますか？</div>
                <div class="faq-answer">
                    SoftBankまたはau（KDDI）のLTE電波が届く場所であれば使用可能です。スマートフォンの電波状況でおおよその確認ができますが、建物の構造によって異なる場合があります。
                </div>
            </li>
            <li class="faq-item">
                <div class="faq-question">ペットがいても使えますか？</div>
                <div class="faq-answer">
                    はい、ペット除外機能があります。センサーの距離測定により、小型〜中型のペット（体高約50cm以下）は除外できます。設定画面で調整可能です。
                </div>
            </li>
            <li class="faq-item">
                <div class="faq-question">月額料金はかかりますか？</div>
                <div class="faq-answer">
                    基本機能（メール・Webプッシュ通知）は無料でご利用いただけます。SMS通知や電話通知をご希望の場合は、プレミアムプラン（月額¥500）へのアップグレードが必要です。
                </div>
            </li>
            <li class="faq-item">
                <div class="faq-question">電池はどのくらい持ちますか？</div>
                <div class="faq-answer">
                    単3電池2本で約2年間使用できる設計です。電池残量が少なくなると通知でお知らせします。
                </div>
            </li>
            <li class="faq-item">
                <div class="faq-question">複数の人で見守りを共有できますか？</div>
                <div class="faq-answer">
                    はい、ウォッチャー機能で見守り情報を共有できます。品番を知っている方が見守り申請を送ることで、承認後にステータスを共有できます。
                </div>
            </li>
        </ul>
    </div>
</div>

{{-- 送信完了画面（フラッシュメッセージ時に表示） --}}
@if(session('contact_success'))
<div id="successSection" class="contact-card success-screen show">
    <div class="success-icon">✓</div>
    <h2 class="success-title">送信完了</h2>
    <p class="success-text">
        お問い合わせありがとうございます。<br><br>
        ご入力いただいたメールアドレス宛に<br>
        確認メールをお送りしました。<br><br>
        2〜3営業日以内にご返信いたします。<br>
        しばらくお待ちください。
    </p>
    <a href="/mypage" class="btn-secondary">マイページに戻る</a>
</div>
@endif
@endsection

@section('scripts')
<script>
    // デバイスID入力のフォーマット
    const deviceIdInput = document.getElementById('device_id');
    if (deviceIdInput) {
        deviceIdInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (this.value.length > 6) this.value = this.value.slice(0, 6);
        });
    }

    // FAQ開閉
    document.querySelectorAll('.faq-question').forEach(q => {
        q.addEventListener('click', () => {
            q.parentElement.classList.toggle('open');
        });
    });

    // 送信完了時はフォーム非表示
    @if(session('contact_success'))
        document.getElementById('formSection').style.display = 'none';
    @endif
</script>
@endsection


