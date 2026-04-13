# ================================================================
# みまもりトーフ - 警告解除ロジック削除 ビューパッチスクリプト
# 実行場所: C:\dev からでも任意の場所でも可
# ================================================================
# 使い方: PowerShell で以下を実行
#   cd C:\dev
#   .\apply_view_patches.ps1
# ================================================================

$enc = [System.Text.UTF8Encoding]::new($false)
$baseDir = Split-Path -Parent $MyInvocation.MyCommand.Path

# Laravel ルート（C:\dev を起点）
$laravelRoot = "C:\dev"

# ================================================================
# 1. dashboard.blade.php パッチ
# ================================================================
$dashFile = "$laravelRoot\resources\views\partner\dashboard.blade.php"
Write-Host "Patching: $dashFile"
$c = [System.IO.File]::ReadAllText($dashFile, $enc)

# CSS削除: .clear-alert-btn x2 + .detail-clear-alert-btn x2
$c = $c.Replace(
    "    .clear-alert-btn { display: inline-flex; align-items: center; gap: 3px; padding: 2px 8px; font-size: 10px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 4px; cursor: pointer; transition: all 0.2s; margin-left: 6px; white-space: nowrap; }`r`n    .clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`r`n    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; vertical-align: middle; }`r`n    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`r`n",
    ""
)
# LF only 版も試す
$c = $c.Replace(
    "    .clear-alert-btn { display: inline-flex; align-items: center; gap: 3px; padding: 2px 8px; font-size: 10px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 4px; cursor: pointer; transition: all 0.2s; margin-left: 6px; white-space: nowrap; }`n    .clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`n    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; vertical-align: middle; }`n    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`n",
    ""
)

# テーブル alert ケース: ✕解除ボタン削除
$oldAlertCase = '@case(''alert'') <span class="device-status alert">' + [char]0x8B66 + [char]0x544A + '</span><button class="clear-alert-btn" onclick="confirmClearAlert(''{{ $device->device_id }}'', ''{{ $roomNumber }}'', ''{{ $tenantName }}'')">✕ ' + [char]0x89E3 + [char]0x9664 + '</button> @break'
$newAlertCase = '@case(''alert'') <span class="device-status alert">' + [char]0x8B66 + [char]0x544A + '</span> @break'
$c = $c.Replace($oldAlertCase, $newAlertCase)

# detailModal 内の detail-clear-alert-btn ボタン削除
$c = $c.Replace(
    '<div class="detail-status-row"><div class="detail-status-badge normal" id="detailStatusBadge">-</div><button class="detail-clear-alert-btn" id="detailClearAlertBtn" style="display:none;" onclick="confirmClearAlertFromDetail()">✕ ' + [char]0x8B66 + [char]0x544A + [char]0x89E3 + [char]0x9664 + '</button></div>',
    '<div class="detail-status-row"><div class="detail-status-badge normal" id="detailStatusBadge">-</div></div>'
)

# JS: detailClearAlertBtn.style.display 行削除
$c = $c.Replace(
    "        document.getElementById('detailClearAlertBtn').style.display = data.status === 'alert' ? 'inline-flex' : 'none';`r`n",
    ""
)
$c = $c.Replace(
    "        document.getElementById('detailClearAlertBtn').style.display = data.status === 'alert' ? 'inline-flex' : 'none';`n",
    ""
)

# clearAlertModal div ブロック削除（全体）- 正規表現で削除
Add-Type -AssemblyName System.Text.RegularExpressions
$pattern = "\r?\n    \{\{-- " + [char]0x8B66 + [char]0x544A + [char]0x89E3 + [char]0x9664 + " --\}\}\r?\n    <div id=""clearAlertModal""[\s\S]*?</div>\r?\n    </div>\r?\n"
$c = [System.Text.RegularExpressions.Regex]::Replace($c, $pattern, "`n")

# JS 警告解除セクション削除（確実な文字列マッチ）
$clearAlertJs = "// ===== " + [char]0x8B66 + [char]0x544A + [char]0x89E3 + [char]0x9664 + " =====" 
$idx = $c.IndexOf($clearAlertJs)
if ($idx -ge 0) {
    # executeClearAlert 関数の終わり "})" を探す
    $endMarker = "`n}"
    $startIdx = $idx
    # 次の "// ====" を探す
    $nextSection = $c.IndexOf("// =====", $idx + 10)
    if ($nextSection -ge 0) {
        $c = $c.Remove($startIdx, $nextSection - $startIdx)
        Write-Host "  JS clearAlert section removed"
    }
}

[System.IO.File]::WriteAllText($dashFile, $c, $enc)
Write-Host "  dashboard.blade.php patched OK"

# ================================================================
# 2. master.blade.php パッチ
# ================================================================
$masterFile = "$laravelRoot\resources\views\partner\master.blade.php"
Write-Host "Patching: $masterFile"
$c = [System.IO.File]::ReadAllText($masterFile, $enc)

# CSS削除: .detail-clear-alert-btn x2
$c = $c.Replace(
    "    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; }`r`n    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`r`n",
    ""
)
$c = $c.Replace(
    "    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; }`n    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }`n",
    ""
)

# HTML: masterDetailClearAlertBtn ボタン行削除
$c = $c.Replace(
    "                <button class=""detail-clear-alert-btn"" id=""masterDetailClearAlertBtn"" style=""display:none;"" onclick=""masterClearAlert()"">✓ " + [char]0x8B66 + [char]0x544A + [char]0x3092 + [char]0x89E3 + [char]0x9664 + [char]0x3057 + [char]0x3066 + [char]0x9000 + [char]0x53BB + [char]0x51E6 + [char]0x7406 + "</button>`r`n",
    ""
)
$c = $c.Replace(
    "                <button class=""detail-clear-alert-btn"" id=""masterDetailClearAlertBtn"" style=""display:none;"" onclick=""masterClearAlert()"">✓ " + [char]0x8B66 + [char]0x544A + [char]0x3092 + [char]0x89E3 + [char]0x9664 + [char]0x3057 + [char]0x3066 + [char]0x9000 + [char]0x53BB + [char]0x51E6 + [char]0x7406 + "</button>`n",
    ""
)

# JS: masterDetailClearAlertBtn.style.display 行削除
$c = $c.Replace(
    "        document.getElementById('masterDetailClearAlertBtn').style.display = d.status === 'alert' ? 'inline-flex' : 'none';`r`n",
    ""
)
$c = $c.Replace(
    "        document.getElementById('masterDetailClearAlertBtn').style.display = d.status === 'alert' ? 'inline-flex' : 'none';`n",
    ""
)

# JS: masterClearAlert() 関数削除
$masterAlertFn = "async function masterClearAlert()"
$idx = $c.IndexOf($masterAlertFn)
if ($idx -ge 0) {
    # 関数の終わりを見つける（}の後の空行）
    $depth = 0
    $pos = $idx
    $inFunc = $false
    for ($i = $idx; $i -lt $c.Length; $i++) {
        if ($c[$i] -eq '{') { $depth++; $inFunc = $true }
        elseif ($c[$i] -eq '}') {
            $depth--
            if ($inFunc -and $depth -eq 0) {
                # 次の改行まで含める
                $endPos = $i + 1
                while ($endPos -lt $c.Length -and ($c[$endPos] -eq "`r" -or $c[$endPos] -eq "`n")) { $endPos++ }
                $c = $c.Remove($idx, $endPos - $idx)
                Write-Host "  masterClearAlert() function removed"
                break
            }
        }
    }
}

[System.IO.File]::WriteAllText($masterFile, $c, $enc)
Write-Host "  master.blade.php patched OK"

Write-Host ""
Write-Host "All patches applied successfully!"
Write-Host "Run: php artisan view:clear"
