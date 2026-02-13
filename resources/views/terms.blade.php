@extends('layouts.app')

@section('title', '利用規約 - みまもりデバイス')

@section('header')
<header class="header">
    <div class="header-inner">
        <a href="javascript:history.back()" class="header-btn" style="font-size: 18px; padding: 8px 10px;">←</a>
        <span class="header-logo-text">利用規約</span>
        <div style="width: 36px;"></div>
    </div>
</header>
@endsection

@section('styles')
<style>
    .main-content {
        max-width: 800px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 8px;
    }
    .page-subtitle {
        font-size: 14px;
        color: var(--gray-500);
        text-align: center;
        margin-bottom: 32px;
    }
    .terms-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        padding: 32px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }

    /* 目次 */
    .toc {
        background: var(--beige);
        border-radius: var(--radius);
        padding: 20px 24px;
        margin-bottom: 32px;
    }
    .toc-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 12px;
    }
    .toc-list {
        list-style: none;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
    }
    .toc-list a {
        font-size: 13px;
        color: var(--gray-600);
        text-decoration: none;
        display: block;
        padding: 4px 0;
    }
    .toc-list a:hover {
        color: var(--gray-800);
        text-decoration: underline;
    }

    /* 条文 */
    .article {
        margin-bottom: 32px;
        padding-bottom: 32px;
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
        margin-bottom: 16px;
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

    /* ハイライトボックス */
    .highlight-box {
        border-radius: var(--radius);
        padding: 16px;
        margin: 16px 0;
        font-size: 13px;
        line-height: 1.8;
    }
    .highlight-box.warning {
        background: var(--red-light);
    }
    .highlight-box.info {
        background: var(--beige);
    }

    /* 更新情報 */
    .update-info {
        text-align: center;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
        font-size: 13px;
        color: var(--gray-500);
    }

    @media (max-width: 480px) {
        .terms-card {
            padding: 24px 20px;
        }
        .toc-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<h1 class="page-title">利用規約</h1>
<p class="page-subtitle">みまもりデバイス サービス利用規約</p>

<div class="terms-card">

    {{-- 目次 --}}
    <nav class="toc">
        <p class="toc-title">📋 目次</p>
        <ul class="toc-list">
            <li><a href="#article1">第1条 総則</a></li>
            <li><a href="#article2">第2条 定義</a></li>
            <li><a href="#article3">第3条 サービス内容</a></li>
            <li><a href="#article4">第4条 利用条件</a></li>
            <li><a href="#article5">第5条 禁止事項</a></li>
            <li><a href="#article6">第6条 通信・SIMに関する免責</a></li>
            <li><a href="#article7">第7条 検知精度に関する免責</a></li>
            <li><a href="#article8">第8条 製品に関する免責</a></li>
            <li><a href="#article9">第9条 保証・故障対応</a></li>
            <li><a href="#article10">第10条 譲渡・転売</a></li>
            <li><a href="#article11">第11条 サービスの終了</a></li>
            <li><a href="#article12">第12条 法人利用に関する特則</a></li>
            <li><a href="#article13">第13条 その他</a></li>
        </ul>
    </nav>

    {{-- 第1条 --}}
    <article class="article" id="article1">
        <h2 class="article-title">第1条（総則）</h2>
        <div class="article-content">
            <p>本規約は、[屋号/氏名]（以下「当方」）が提供する「みまもりデバイス」（以下「本サービス」）の利用に関する条件を定めるものです。本サービスをご利用いただく方（以下「利用者」）は、本規約に同意したものとみなします。</p>
        </div>
    </article>

    {{-- 第2条 --}}
    <article class="article" id="article2">
        <h2 class="article-title">第2条（定義）</h2>
        <div class="article-content">
            <p>本規約において使用する用語の定義は以下のとおりです。</p>
            <ol>
                <li><strong>本製品</strong>：当方が販売する見守りセンサーデバイス「みまもりデバイス」</li>
                <li><strong>本サービス</strong>：本製品を通じて提供される見守り通知サービス</li>
                <li><strong>利用者</strong>：本製品を購入し、本サービスを利用する個人または法人</li>
                <li><strong>見守り対象者</strong>：本製品が設置された場所に居住する方</li>
                <li><strong>ウォッチャー</strong>：利用者から見守り情報の共有を受ける第三者</li>
            </ol>
        </div>
    </article>

    {{-- 第3条 --}}
    <article class="article" id="article3">
        <h2 class="article-title">第3条（サービス内容）</h2>
        <div class="article-content">
            <p>本サービスは、本製品に搭載されたセンサーにより人の動きを検知し、一定時間検知がない場合に利用者へ通知を送信するサービスです。</p>
            <div class="highlight-box warning">
                <strong>重要：</strong>本サービスは見守り対象者の安否を補助的に確認するためのものであり、安否を100%保証するものではありません。緊急通報サービスや医療サービスの代替にはなりません。
            </div>
        </div>
    </article>

    {{-- 第4条 --}}
    <article class="article" id="article4">
        <h2 class="article-title">第4条（利用条件）</h2>
        <div class="article-content">
            <p>利用者は、以下の条件を満たした上で本サービスをご利用ください。</p>
            <ol>
                <li>本製品の設置場所がSoftBankまたはKDDI（au）のLTE電波が届く場所であること</li>
                <li>見守り対象者本人の同意を得ていること（利用者と見守り対象者が異なる場合）</li>
                <li>本規約およびプライバシーポリシーに同意していること</li>
            </ol>
        </div>
    </article>

    {{-- 第5条 --}}
    <article class="article" id="article5">
        <h2 class="article-title">第5条（禁止事項）</h2>
        <div class="article-content">
            <p>利用者は、以下の行為を行ってはなりません。</p>
            <ol>
                <li>見守り対象者の同意なく本製品を設置する行為</li>
                <li>ストーカー行為、DV、嫌がらせ等の違法または不当な目的での使用</li>
                <li>本製品の分解、改造、SIMカードの抜き取り・差し替え</li>
                <li>本製品を本来の用途以外に使用する行為</li>
                <li>当方または第三者の権利を侵害する行為</li>
                <li>その他、法令または公序良俗に反する行為</li>
            </ol>
            <div class="highlight-box warning">
                <strong>警告：</strong>不正利用が発覚した場合、サービスを停止し、必要に応じて警察等の関係機関に通報いたします。
            </div>
        </div>
    </article>

    {{-- 第6条 --}}
    <article class="article" id="article6">
        <h2 class="article-title">第6条（通信・SIMに関する免責）</h2>
        <div class="article-content">
            <p>本製品は第三者のSIM事業者および通信事業者のネットワークを利用しています。以下の事由により本サービスが利用できない場合、当方は一切の責任を負いません。</p>
            <ol>
                <li>SIM事業者の倒産、サービス終了、契約変更</li>
                <li>通信事業者の通信障害、メンテナンス、サービス変更</li>
                <li>設置場所の電波状況の変化</li>
                <li>その他当方の責に帰さない通信の不具合</li>
            </ol>
            <div class="highlight-box info">
                <strong>補足：</strong>上記事由によりサービスが利用できなくなった場合でも、製品代金の返金は行いません。
            </div>
        </div>
    </article>

    {{-- 第7条 --}}
    <article class="article" id="article7">
        <h2 class="article-title">第7条（検知精度に関する免責）</h2>
        <div class="article-content">
            <p>本製品のセンサーは、以下の要因により検知漏れや誤検知が発生する可能性があります。当方はこれらに起因する損害について責任を負いません。</p>
            <ol>
                <li>見守り対象者の動きが小さい場合（就寝中、静止状態等）</li>
                <li>センサーの検知範囲外での活動</li>
                <li>ペットや環境要因（温度変化等）による誤検知</li>
                <li>製品の設置位置や向きによる影響</li>
            </ol>
            <div class="highlight-box warning">
                <strong>重要：</strong>通知を受け取らなかったこと、または通知の確認が遅れたことにより生じた損害について、当方は責任を負いません。通知の確認は利用者の責任において行ってください。
            </div>
        </div>
    </article>

    {{-- 第8条 --}}
    <article class="article" id="article8">
        <h2 class="article-title">第8条（製品に関する免責）</h2>
        <div class="article-content">
            <p>以下の事由により生じた損害について、当方は責任を負いません。</p>
            <ol>
                <li>本製品の落下による怪我、物損</li>
                <li>電池の液漏れ、発熱による損害</li>
                <li>利用者による誤った設置、使用方法に起因する損害</li>
                <li>天災、火災、その他不可抗力による損害</li>
            </ol>
        </div>
    </article>

    {{-- 第9条 --}}
    <article class="article" id="article9">
        <h2 class="article-title">第9条（保証・故障対応）</h2>
        <div class="article-content">
            <ol>
                <li>本製品の保証期間は、購入日から1年間とします。</li>
                <li>保証期間内の故障（初期不良含む）は、無償で代替品と交換いたします。</li>
                <li>保証期間外の故障は、有償（¥9,800）で交換対応いたします。</li>
                <li>故障品の返送は不要です。各自で適切に廃棄してください。</li>
                <li>電池の消耗は保証対象外です。電池寿命は使用環境により異なります。</li>
            </ol>
        </div>
    </article>

    {{-- 第10条 --}}
    <article class="article" id="article10">
        <h2 class="article-title">第10条（譲渡・転売）</h2>
        <div class="article-content">
            <ol>
                <li>本製品の譲渡・転売は自由に行うことができます。</li>
                <li>譲渡・転売後のサポートは、元の購入者に対してのみ提供いたします。</li>
                <li>譲渡・転売を行う場合は、本規約およびプライバシーポリシーの内容を譲受人に説明してください。</li>
            </ol>
        </div>
    </article>

    {{-- 第11条 --}}
    <article class="article" id="article11">
        <h2 class="article-title">第11条（サービスの終了）</h2>
        <div class="article-content">
            <ol>
                <li>当方は、事業上の理由により本サービスを終了することがあります。</li>
                <li>サービス終了の場合、終了の6ヶ月前までに利用者へ通知いたします。</li>
                <li>サービス終了に伴う製品代金の返金は行いません。</li>
            </ol>
        </div>
    </article>

    {{-- 第12条 --}}
    <article class="article" id="article12">
        <h2 class="article-title">第12条（法人利用に関する特則）</h2>
        <div class="article-content">
            <p>賃貸物件の管理会社等の法人が本製品を利用する場合、以下の特則が適用されます。</p>
            <ol>
                <li><strong>入居者への説明義務：</strong>管理会社は、本製品の設置について入居者に対し十分な説明を行い、同意を得る義務を負います。</li>
                <li><strong>入居者とのトラブル：</strong>本製品の設置に関する入居者とのトラブルは、管理会社の責任において解決するものとし、当方は一切関与いたしません。</li>
                <li><strong>データの取扱い：</strong>退去時のデータ削除要望には対応いたします。削除をご希望の場合はお問い合わせください。</li>
                <li><strong>複数台契約：</strong>10台以上のご契約の場合、別途契約書を締結することがあります。</li>
            </ol>
        </div>
    </article>

    {{-- 第13条 --}}
    <article class="article" id="article13">
        <h2 class="article-title">第13条（その他）</h2>
        <div class="article-content">
            <ol>
                <li><strong>規約の変更：</strong>当方は、必要に応じて本規約を変更することがあります。変更後の規約は、本サービスのウェブサイトに掲載した時点で効力を生じます。</li>
                <li><strong>分離可能性：</strong>本規約の一部が無効となった場合でも、他の条項は引き続き有効とします。</li>
                <li><strong>準拠法：</strong>本規約は日本法に準拠します。</li>
                <li><strong>管轄裁判所：</strong>本規約に関する紛争は、[管轄地]地方裁判所を第一審の専属的合意管轄裁判所とします。</li>
            </ol>
        </div>
    </article>

    <div class="update-info">
        <p>制定日：2025年1月20日</p>
        <p>最終更新日：2026年2月13日</p>
    </div>
</div>
@endsection
