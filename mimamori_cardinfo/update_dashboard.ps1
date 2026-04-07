# update_dashboard.ps1
# dashboard.blade.phpの3箇所を変更するスクリプト
# 実行: .\update_dashboard.ps1

$file = "C:\dev\resources\views\partner\dashboard.blade.php"
$enc  = [System.Text.UTF8Encoding]::new($false)
$content = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)

# ============================================================
# 変更1: bulk-panel-4にカード情報表示エリアを追加
# ============================================================
$old1 = '                <p class="bulk-summary-note">※ 24ヶ月最低契約。解約時は¥8,400の違約金が発生します。<br>※「決済へ進む」を押すとデバイスが生成され、IDとPINのCSVが自動でダウンロードされます。</p>'
$new1 = '                <div id="bulk-card-info" style="margin-bottom:14px;padding:12px 14px;background:var(--white);border:1px solid var(--gray-200);border-radius:var(--radius);font-size:13px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:var(--gray-500);">💳 お支払いカード</span>
                    <span id="bulk-card-display" style="font-weight:600;">読み込み中...</span>
                </div>
                <p class="bulk-summary-note">※ 24ヶ月最低契約。解約時は¥8,400の違約金が発生します。<br>※「決済へ進む」を押すとデバイスが生成され、IDとPINのCSVが自動でダウンロードされます。</p>'

# ============================================================
# 変更2: bulkUpdateSummaryを非同期化 + カード情報取得
# ============================================================
$old2 = 'function bulkUpdateSummary() {
    var q = bulkGetQty();
    var add = (bulkOpts.ai ? 300 : 0) + (bulkOpts.sms ? 100 : 0);
    var subtotal = (700 + add) * q;
    var tax = Math.floor(subtotal * 0.1);
    var total = subtotal + tax;
    document.getElementById(''bulk-sum-qty'').textContent = q + ''台'';
    document.getElementById(''bulk-sum-ai-row'').style.display = bulkOpts.ai ? '''' : ''none'';
    document.getElementById(''bulk-sum-sms-row'').style.display = bulkOpts.sms ? '''' : ''none'';
    document.getElementById(''bulk-sum-subtotal'').textContent = ''¥'' + subtotal.toLocaleString() + '' / 月'';
    document.getElementById(''bulk-sum-tax'').textContent = ''¥'' + tax.toLocaleString() + '' / 月'';
    document.getElementById(''bulk-sum-total'').textContent = ''¥'' + total.toLocaleString() + '' / 月'';
}'
$new2 = 'async function bulkUpdateSummary() {
    var q = bulkGetQty();
    var add = (bulkOpts.ai ? 300 : 0) + (bulkOpts.sms ? 100 : 0);
    var subtotal = (700 + add) * q;
    var tax = Math.floor(subtotal * 0.1);
    var total = subtotal + tax;
    document.getElementById(''bulk-sum-qty'').textContent = q + ''台'';
    document.getElementById(''bulk-sum-ai-row'').style.display = bulkOpts.ai ? '''' : ''none'';
    document.getElementById(''bulk-sum-sms-row'').style.display = bulkOpts.sms ? '''' : ''none'';
    document.getElementById(''bulk-sum-subtotal'').textContent = ''¥'' + subtotal.toLocaleString() + '' / 月'';
    document.getElementById(''bulk-sum-tax'').textContent = ''¥'' + tax.toLocaleString() + '' / 月'';
    document.getElementById(''bulk-sum-total'').textContent = ''¥'' + total.toLocaleString() + '' / 月'';
    // ★ カード情報取得
    var cardDisplay = document.getElementById(''bulk-card-display'');
    var nextBtn = document.getElementById(''bulk-btn-next'');
    try {
        var res = await fetch(''/partner/org/card-info'', { headers: { ''Accept'': ''application/json'' } });
        var data = await res.json();
        if (data.found) {
            cardDisplay.textContent = data.brand + '' **** '' + data.last4;
            cardDisplay.style.color = ''var(--gray-800)'';
            nextBtn.disabled = false;
        } else {
            cardDisplay.innerHTML = ''<span style="color:var(--red);">未登録 — <a href="/partner/billing" style="color:var(--red);text-decoration:underline;">カードを登録する</a></span>'';
            nextBtn.disabled = true;
        }
    } catch(e) {
        cardDisplay.textContent = ''取得できませんでした'';
    }
}'

# ============================================================
# 変更3: bulkNextStepをasyncに + awaitを追加
# ============================================================
$old3 = 'function bulkNextStep() {'
$new3 = 'async function bulkNextStep() {'

$old4 = 'if (bulkStep < 4) { bulkStep++; bulkUpdateStepUI(); if (bulkStep === 4) bulkUpdateSummary(); }'
$new4 = 'if (bulkStep < 4) { bulkStep++; bulkUpdateStepUI(); if (bulkStep === 4) await bulkUpdateSummary(); }'

# ============================================================
# 変更4: bulkExecuteのカード未登録エラーメッセージを改善
# ============================================================
$old5 = "        else { showToast(data.message || '追加に失敗しました', 'error'); btn.disabled = false; btn.textContent = '決済へ進む'; document.getElementById('bulk-loading').classList.remove('show'); }"
$new5 = "        else {
            var msg = data.message || '追加に失敗しました';
            if (data.message && data.message.includes('カード')) {
                showToast('カードが未登録です。管理者に課金設定を依頼してください。', 'error');
            } else {
                showToast(msg, 'error');
            }
            btn.disabled = false; btn.textContent = '決済へ進む'; document.getElementById('bulk-loading').classList.remove('show');
        }"

# 変更を適用
$content = $content.Replace($old1, $new1)
$content = $content.Replace($old2, $new2)
$content = $content.Replace($old3, $new3)
$content = $content.Replace($old4, $new4)
$content = $content.Replace($old5, $new5)

[System.IO.File]::WriteAllText($file, $content, $enc)
Write-Host "dashboard.blade.php を更新しました"
