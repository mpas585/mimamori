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

    <a href="{{ url('/login') }}" class="back-link">← ログインに戻る</a>

    <div class="legal-header">
        <h1>特定商取引法に基づく表記</h1>
        <p>通信販売に関する表示事項</p>
    </div>

    <table class="legal-table">
        <tr>
            <th>販売業者</th>
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
            <th>販売価格</th>
            <td>
                <table class="price-table">
                    <tr>
                        <th>台数</th>
                        <th>単価（税込）</th>
                    </tr>
                    <tr>
                        <td>1台</td>
                        <td>¥14,800</td>
                    </tr>
                    <tr>
                        <td>2〜4台</td>
                        <td>¥14,300（3%オフ）</td>
                    </tr>
                    <tr>
                        <td>5〜9台</td>
                        <td>¥13,800（7%オフ）</td>
                    </tr>
                    <tr>
                        <td>10〜29台</td>
                        <td>¥13,300（10%オフ）</td>
                    </tr>
                    <tr>
                        <td>30〜49台</td>
                        <td>¥12,800（14%オフ）</td>
                    </tr>
                    <tr>
                        <td>50〜99台</td>
                        <td>¥12,300（17%オフ）</td>
                    </tr>
                    <tr>
                        <td>100台以上</td>
                        <td>¥11,800（20%オフ）</td>
                    </tr>
                </table>
                <p style="font-size:12px; color:var(--gray-500); margin-top:8px;">※価格はすべて税込表示です</p>
            </td>
        </tr>
        <tr>
            <th>販売価格以外の必要料金</th>
            <td>
                <ul>
                    <li>送料：全国一律無料</li>
                    <li>月額オプション（任意）：
                        <br>SMS通知 ¥100/台/月
                        <br>AIコール通知 ¥300/台/月
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <th>支払方法</th>
            <td>
                <ul>
                    <li>クレジットカード（VISA、Mastercard、JCB、AMEX）</li>
                    <li>銀行振込（法人・10台以上のご注文）</li>
                </ul>
            </td>
        </tr>
        <tr>
            <th>支払時期</th>
            <td>
                <ul>
                    <li>クレジットカード：ご注文時に決済</li>
                    <li>銀行振込：ご注文後7日以内</li>
                </ul>
            </td>
        </tr>
        <tr>
            <th>商品の引渡時期</th>
            <td>ご注文・ご入金確認後、7営業日以内に発送いたします。<br>※在庫状況により、お届けまでにお時間をいただく場合があります。</td>
        </tr>
        <tr>
            <th>商品の引渡方法</th>
            <td>宅配便（ヤマト運輸または日本郵便）</td>
        </tr>
        <tr>
            <th>返品・交換</th>
            <td>
                <strong>初期不良の場合</strong><br>
                商品到着後14日以内にご連絡いただいた場合、無償で交換いたします。<br><br>
                <strong>お客様都合の返品</strong><br>
                商品の性質上、開封後の返品はお受けできません。未開封・未使用の場合のみ、商品到着後7日以内に限り返品を承ります（送料はお客様負担）。<br><br>
                <strong>電波状況による返品</strong><br>
                設置場所の電波状況による返品・返金はお受けできません。ご購入前に必ず電波状況をご確認ください。
            </td>
        </tr>
        <tr>
            <th>保証期間</th>
            <td>購入日より1年間<br>※電池の消耗は保証対象外です</td>
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
            <td>ご注文後、発送前であればキャンセルが可能です。<br>発送後のキャンセルは、返品扱いとなります。</td>
        </tr>
        <tr>
            <th>特別な販売条件</th>
            <td>なし</td>
        </tr>
    </table>

    <div class="legal-footer">
        最終更新日：{{ date('Y年n月j日') }}
    </div>

</div>
@endsection
