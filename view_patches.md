## dashboard.blade.php パッチ内容

### 削除1: CSS（.clear-alert-btn / .detail-clear-alert-btn）
以下の4行を削除:
```
    .clear-alert-btn { display: inline-flex; ... }
    .clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
    .detail-clear-alert-btn { display: inline-flex; ... }
    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
```

### 削除2: テーブル内 alert ケース ✕解除ボタン
変更前:
```
@case('alert') <span class="device-status alert">警告</span><button class="clear-alert-btn" onclick="confirmClearAlert('{{ $device->device_id }}', '{{ $roomNumber }}', '{{ $tenantName }}')">✕ 解除</button> @break
```
変更後:
```
@case('alert') <span class="device-status alert">警告</span> @break
```

### 削除3: clearAlertModal div ブロック（計7行）
```html
{{-- モーダル: 警告解除 --}}
<div id="clearAlertModal" ...>
  ...
</div>
```

### 削除4: detailModal 内の detail-clear-alert-btn
変更前:
```html
<div class="detail-status-row"><div class="detail-status-badge normal" id="detailStatusBadge">-</div><button class="detail-clear-alert-btn" id="detailClearAlertBtn" style="display:none;" onclick="confirmClearAlertFromDetail()">✕ 警告解除</button></div>
```
変更後:
```html
<div class="detail-status-row"><div class="detail-status-badge normal" id="detailStatusBadge">-</div></div>
```

### 削除5: JS 1行（showDeviceDetail内）
削除:
```js
        document.getElementById('detailClearAlertBtn').style.display = data.status === 'alert' ? 'inline-flex' : 'none';
```

### 削除6: JS 警告解除セクション（約15行）
削除: `// ===== 警告解除 =====` から `executeClearAlert()` 関数の閉じ括弧まで

---

## master.blade.php パッチ内容

### 削除1: CSS（.detail-clear-alert-btn）
以下の2行を削除:
```
    .detail-clear-alert-btn { display: inline-flex; ... }
    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
```

### 削除2: HTML ボタン（masterDetailClearAlertBtn）
変更前:
```html
<button class="detail-clear-alert-btn" id="masterDetailClearAlertBtn" style="display:none;" onclick="masterClearAlert()">✓ 警告を解除して退去処理</button>
```
変更後: 行ごと削除

### 削除3: JS 1行（showDeviceDetail内）
削除:
```js
        document.getElementById('masterDetailClearAlertBtn').style.display = d.status === 'alert' ? 'inline-flex' : 'none';
```

### 削除4: JS masterClearAlert() 関数（約10行）
削除: `async function masterClearAlert()` から関数の閉じ括弧まで
