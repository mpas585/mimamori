@extends('layouts.app')

@section('title', '特定商取引法に基づく表記 - みまもりトーフ')

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
    .legal-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .legal-table tr {
        border-bottom: 1px solid var(--gray-200);
    }
    .legal-table tr:last-child {
        border-bottom: none;
    }
    .legal-table th {
        width: 200px;
        padding: 16px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        background: var(--cream);
        text-align: left;
        vertical-align: top;
        white-space: nowrap;
    }
    .legal-table td {
        padding: 16px 20px;
        font-size: 14px;
        color: var(--gray-800);
        line-height: 1.7;
        vertical-align: top;
    }
    .legal-table td ul {
        margin: 0;
        padding-left: 20px;
    }
    .legal-table td ul li {
        margin-bottom: 4px;
    }
    .legal-table td ul li:last-child {
        margin-bottom: 0;
    }
    .price-table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
    }
    .price-table th,
    .price-table td {
        padding: 8px 12px;
        font-size: 13px;
        border: 1px solid var(--gray-200);
        text-align: left;
        white-space: normal;
    }
    .price-table th {
        background: var(--gray-100);
        font-weight: 600;
        width: auto;
    }
    .legal-footer {
        margin-top: 24px;
        text-align: right;
        font-size: 13px;
        color: var(--gray-500);
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

    @media (max-width: 600px) {
        .legal-table th {
            display: block;
            width: 100%;
            padding: 12px 16px 4px;
            border-bottom: none;
        }
        .legal-table td {
            display: block;
            padding: 4px 16px 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="legal-container">

    <a href="{{ url('/login') }}" class="back-link">&larr; ログインに戻る</a>

    <div class="legal-header">
        <h1>特定商取引法に基づく表記</h1>
        <p>通信販売に関する表示事項</p>
    </div>

    <table class="legal-table">
        <tr>
            <th>事業者名</th>
            <td>ウエダ・トレーディング株式会社</td>
        </tr>
        <tr>
            <th>運営統括責任者</th>
            <td>上田 洋子</td>
        </tr>
        <tr>
            <th>所在地</th>
            <td>東京都千代田区丸の内1-11-1 パシフィックセンチュリープレイス丸の内 13階<br>※請求があった場合は遅滞なく開示いたします</td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>03-6775-3642<br>※請求があった場合は遅滞なく開示いたします</td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>info@gud.co.jp</td>
        </tr>
        <tr>
            <th>URL</th>
            <td><a href="https://dev.gud.co.jp">https://dev.gud.co.jp</a></td>
        </tr>
        <tr>
            <th>サービス内容</th>
            <td>IoT見守りデバイスのレンタルおよび見守り通知サービスの提供</td>
        </tr>
        <tr>
            <th>料金</th>
            <td>
                <table class="price-table">
                    <tr>
                        <th>プラン</th>
                        <th>月額料金（税込）</th>
                    </tr>
                    <tr>
                        <td>基本プラン<br><span style="font-size:11px; color:var(--gray-500);">デバイスレンタル＋見守り通知</span></td>
                        <td>¥1,100/台</td>
                    </tr>
                </table>
                <p style="font-size:12px; color:var(--gray-500); margin-top:8px;">※初期費用・事務手数料はかかりません</p>
            </td>
        </tr>
        <tr>
            <th>オプション料金</th>
            <td>
                <table class="price-table">
                    <tr>
                        <th>オプション</th>
                        <th>月額料金（税込）</th>
                    </tr>
                    <tr>
                        <td>SMS通知</td>
                        <td>¥100/台</td>
                    </tr>
                    <tr>
                        <td>AIコール通知</td>
                        <td>¥300/台</td>
                    </tr>
                </table>
                <p style="font-size:12px; color:var(--gray-500); margin-top:8px;">※オプション料金は今後改定される場合があります</p>
            </td>
        </tr>
        <tr>
            <th>契約期間</th>
            <td>2年間（契約満了後は同条件で自動更新）</td>
        </tr>
        <tr>
            <th>支払方法</th>
            <td>クレジットカード（VISA、Mastercard、JCB、AMEX）</td>
        </tr>
        <tr>
            <th>支払時期</th>
            <td>毎月自動決済</td>
        </tr>
        <tr>
            <th>デバイスの配送</th>
            <td>お申込み後、7営業日以内に発送いたします。<br>配送方法：宅配便（ヤマト運輸または日本郵便）<br>送料：無料<br>※在庫状況により、お届けまでにお時間をいただく場合があります。</td>
        </tr>
        <tr>
            <th>解約・デバイス返却</th>
            <td>
                <strong>中途解約</strong><br>
                契約期間中の解約はできません。契約満了月に解約のお申し出がない場合は自動更新となります。<br><br>
                <strong>デバイスの返却</strong><br>
                解約時はデバイスをご返却いただきます。返却先・手順は解約受付時にご案内いたします。返送料はお客様のご負担となります。
            </td>
        </tr>
        <tr>
            <th>故障時の対応</th>
            <td>契約期間中にデバイスが故障した場合、無償で交換いたします。<br>※お客様の故意・過失による破損は有償交換となる場合があります。<br>※電池の消耗は故障に含まれません（お客様にて交換）。</td>
        </tr>
        <tr>
            <th>動作環境</th>
            <td>
                <ul>
                    <li>対応エリア：SoftBankまたはKDDI（au）のLTE電波が届く場所</li>
                    <li>使用温度：0℃〜40℃</li>
                    <li>屋内専用（防水非対応）</li>
                </ul>
            </td>
        </tr>
        <tr>
            <th>申込みの撤回</th>
            <td>デバイス発送前であればキャンセルが可能です。<br>発送後のキャンセルについては、デバイス到着後8日以内にご連絡ください。</td>
        </tr>
        <tr>
            <th>特別な販売条件</th>
            <td>
                <ul>
                    <li>設置場所の電波状況によりサービスをご利用いただけない場合があります。</li>
                    <li>デバイスの所有権は当社に帰属します。</li>
                </ul>
            </td>
        </tr>
    </table>

    <div class="legal-footer">
        最終更新日：{{ date('Y年n月j日') }}
    </div>

</div>
@endsection
