@extends('layouts.app')

@section('title', 'プライバシーポリシー - みまもりデバイス')

@section('styles')
<style>
    .main-content {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-bottom: 40px;
    }
    .legal-container {
        max-width: 800px;
        margin: 0 auto;
        width: 100%;
        padding: 40px 20px;
    }
    .legal-header {
        margin-bottom: 32px;
    }
    .legal-header h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 8px;
    }
    .legal-header p {
        font-size: 14px;
        color: var(--gray-500);
    }
    .legal-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 32px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        color: var(--gray-500);
        text-decoration: none;
        margin-bottom: 24px;
        font-weight: 500;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: var(--gray-700);
    }

    .article {
        margin-bottom: 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid var(--gray-200);
    }
    .article:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .article-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 14px;
        padding-left: 12px;
        border-left: 4px solid var(--gray-800);
    }
    .article-content {
        font-size: 14px;
        color: var(--gray-700);
        line-height: 1.8;
    }
    .article-content p {
        margin-bottom: 12px;
    }
    .article-content p:last-child {
        margin-bottom: 0;
    }
    .article-content ol,
    .article-content ul {
        margin: 12px 0;
        padding-left: 24px;
    }
    .article-content li {
        margin-bottom: 8px;
    }
    .article-content li:last-child {
        margin-bottom: 0;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0;
    }
    .info-table th,
    .info-table td {
        padding: 10px 14px;
        font-size: 13px;
        border: 1px solid var(--gray-200);
        text-align: left;
        vertical-align: top;
        line-height: 1.7;
    }
    .info-table th {
        background: var(--cream);
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
        width: 180px;
    }
    .update-info {
        text-align: center;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
        font-size: 13px;
        color: var(--gray-500);
    }

    @media (max-width: 600px) {
        .legal-card { padding: 24px 20px; }
        .info-table th {
            display: block;
            width: 100%;
            border-bottom: none;
        }
        .info-table td {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<div class="legal-container">

    <a href="{{ url('/login') }}" class="back-link">&larr; ログインに戻る</a>

    <div class="legal-header">
        <h1>プライバシーポリシー</h1>
        <p>個人情報の取扱いについて</p>
    </div>

    <div class="legal-card">

        <article class="article">
            <div class="article-content">
                <p>ウエダ・トレーディング株式会社（以下「当社」）は、IoT見守りサービス「みまもりデバイス」（以下「本サービス」）の提供にあたり、利用者の個人情報を以下のとおり適切に取り扱います。</p>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">1. 収集する情報</h2>
            <div class="article-content">
                <p>当社は、本サービスの提供にあたり、以下の情報を収集します。</p>
                <table class="info-table">
                    <tr>
                        <th>利用者情報</th>
                        <td>氏名、メールアドレス、電話番号、住所（配送先）</td>
                    </tr>
                    <tr>
                        <th>決済情報</th>
                        <td>クレジットカード情報（決済代行会社PAY.JPが管理。当社はカード番号を保持しません）</td>
                    </tr>
                    <tr>
                        <th>デバイス情報</th>
                        <td>デバイスID、設置場所に関する情報</td>
                    </tr>
                    <tr>
                        <th>センサーデータ</th>
                        <td>人感センサーの検知履歴（検知日時・検知有無）、距離センサーの計測データ</td>
                    </tr>
                    <tr>
                        <th>通信情報</th>
                        <td>デバイスの通信状態、電池残量、最終通信日時</td>
                    </tr>
                    <tr>
                        <th>通知履歴</th>
                        <td>メール・SMS・AIコールの送信履歴、AIコールの通話内容</td>
                    </tr>
                </table>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">2. 利用目的</h2>
            <div class="article-content">
                <p>収集した情報は、以下の目的でのみ利用します。</p>
                <ol>
                    <li>見守り通知サービスの提供（未検知アラート、ステータス通知）</li>
                    <li>AIコール通知における安否確認と応答内容の判定</li>
                    <li>デバイスの動作状態の監視・保守</li>
                    <li>月額料金の請求・決済処理</li>
                    <li>デバイスの配送</li>
                    <li>お問い合わせ・故障報告への対応</li>
                    <li>サービスの改善・品質向上</li>
                </ol>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">3. 第三者への提供</h2>
            <div class="article-content">
                <p>当社は、利用者の個人情報を第三者に販売・貸与することはありません。ただし、以下の場合に限り、業務委託先に情報を提供することがあります。</p>
                <table class="info-table">
                    <tr>
                        <th>決済処理</th>
                        <td>PAY株式会社（PAY.JP）：クレジットカード決済の処理</td>
                    </tr>
                    <tr>
                        <th>SMS通知</th>
                        <td>Twilio Inc.：SMS通知の送信</td>
                    </tr>
                    <tr>
                        <th>AIコール通知</th>
                        <td>Twilio Inc.：電話発信、OpenAI：通話内容の音声認識・判定処理</td>
                    </tr>
                    <tr>
                        <th>SIM通信</th>
                        <td>1NCE GmbH：デバイスのモバイルデータ通信</td>
                    </tr>
                </table>
                <p>また、法令に基づく場合、または人の生命・身体・財産の保護のために必要な場合は、本人の同意なく情報を提供することがあります。</p>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">4. 安全管理措置</h2>
            <div class="article-content">
                <p>当社は、個人情報の漏洩、滅失、毀損を防止するため、以下の措置を講じています。</p>
                <ul>
                    <li>SSL/TLSによる通信の暗号化</li>
                    <li>パスワード・PINのハッシュ化保存</li>
                    <li>アクセス権限の制限（管理者のみデータにアクセス可能）</li>
                    <li>クレジットカード情報の非保持化（決済代行会社による管理）</li>
                </ul>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">5. データの保持期間</h2>
            <div class="article-content">
                <ul>
                    <li>センサーデータ（検知履歴）：最大90日間保持後、自動的に削除します。</li>
                    <li>通知履歴：最大90日間保持後、自動的に削除します。</li>
                    <li>AIコールの通話記録（音声認識テキスト・録音データ）：最大90日間保持後、自動的に削除します。</li>
                    <li>利用者のアカウント情報：契約終了後、合理的な期間内に削除します。</li>
                </ul>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">6. 利用者の権利</h2>
            <div class="article-content">
                <p>利用者は、当社が保有する自己の個人情報について、以下の権利を有します。</p>
                <ol>
                    <li><strong>開示請求：</strong>保有する個人情報の開示を請求できます。</li>
                    <li><strong>訂正・削除：</strong>内容の訂正または削除を請求できます。</li>
                    <li><strong>利用停止：</strong>利用目的に反する取扱いがある場合、利用停止を請求できます。</li>
                </ol>
                <p>上記の請求は、本サービスのお問い合わせフォームまたは下記連絡先までご連絡ください。</p>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">7. Cookieの使用</h2>
            <div class="article-content">
                <p>本サービスのウェブサイトでは、ログイン状態の維持およびセキュリティ目的でCookieを使用しています。広告目的のCookieは使用しません。</p>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">8. プライバシーポリシーの変更</h2>
            <div class="article-content">
                <p>当社は、必要に応じて本ポリシーを変更することがあります。重要な変更がある場合は、本サービスのウェブサイト上でお知らせいたします。</p>
            </div>
        </article>

        <article class="article">
            <h2 class="article-title">9. お問い合わせ</h2>
            <div class="article-content">
                <p>個人情報の取扱いに関するお問い合わせは、以下までご連絡ください。</p>
                <table class="info-table">
                    <tr>
                        <th>事業者名</th>
                        <td>ウエダ・トレーディング株式会社</td>
                    </tr>
                    <tr>
                        <th>メール</th>
                        <td>info@gud.co.jp</td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td>03-6775-3642</td>
                    </tr>
                </table>
            </div>
        </article>

        <div class="update-info">
            <p>制定日：{{ date('Y年n月j日') }}</p>
        </div>

    </div>

</div>
@endsection
