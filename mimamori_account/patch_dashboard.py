#!/usr/bin/env python3
# サーバー上で実行: python3 patch_dashboard.py
# 実行場所: ~/gud.co.jp/public_html/dev.gud.co.jp/

with open('resources/views/partner/dashboard.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# 変更1: ステップ3にプリセットエリアを追加
old1 = '            <div id="bulk-panel-3" class="bulk-panel modal-body">\n                <p class="bulk-section-label">デバイスの配送先をご入力ください</p>\n                <div class="bulk-form-group"><label>お名前<span class="bulk-form-required">*</span></label><input type="text" class="bulk-form-input" id="bulk-delivery-name" placeholder="山田 太郎"></div>'
new1 = '            <div id="bulk-panel-3" class="bulk-panel modal-body">\n                <p class="bulk-section-label">デバイスの配送先をご入力ください</p>\n                <div id="bulk-preset-area" style="margin-bottom:12px;"></div>\n                <div class="bulk-form-group"><label>お名前<span class="bulk-form-required">*</span></label><input type="text" class="bulk-form-input" id="bulk-delivery-name" placeholder="山田 太郎"></div>'

# 変更2: showAddDeviceModalにプリセットエリア初期化追加
old2 = "function showAddDeviceModal() {\n    bulkStep = 1;\n    bulkOpts = { ai: false, sms: false };\n    document.getElementById('bulk-qty-input').value = 10;\n    document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.remove('active'); });\n    ['ai', 'sms'].forEach(function(k) { document.getElementById('bulk-opt-' + k).classList.remove('selected'); });\n    ['bulk-delivery-name', 'bulk-delivery-postal', 'bulk-delivery-address', 'bulk-delivery-phone'].forEach(function(id) { document.getElementById(id).value = ''; });\n    document.getElementById('bulk-loading').classList.remove('show');\n    bulkUpdateStepUI();\n    showModal('addDeviceModal');\n}"
new2 = "function showAddDeviceModal() {\n    bulkStep = 1;\n    bulkOpts = { ai: false, sms: false };\n    document.getElementById('bulk-qty-input').value = 10;\n    document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.remove('active'); });\n    ['ai', 'sms'].forEach(function(k) { document.getElementById('bulk-opt-' + k).classList.remove('selected'); });\n    ['bulk-delivery-name', 'bulk-delivery-postal', 'bulk-delivery-address', 'bulk-delivery-phone'].forEach(function(id) { document.getElementById(id).value = ''; });\n    document.getElementById('bulk-loading').classList.remove('show');\n    // プリセットボタン表示\n    var presetArea = document.getElementById('bulk-preset-area');\n    if (presetArea && deliveryPreset.name) {\n        presetArea.innerHTML = '<button type=\"button\" onclick=\"applyPreset()\" style=\"width:100%;padding:10px 14px;font-size:13px;font-family:inherit;background:var(--beige);border:1px solid var(--gray-300);border-radius:var(--radius);cursor:pointer;text-align:left;color:var(--gray-700);\">📦 前回の配送先を使用: ' + escapeHtml(deliveryPreset.name) + ' / ' + escapeHtml(deliveryPreset.postal) + '</button>';\n    } else if (presetArea) {\n        presetArea.innerHTML = '';\n    }\n    bulkUpdateStepUI();\n    showModal('addDeviceModal');\n}"

# 変更3: bulkSyncPresetsの直前にdeliveryPresetとapplyPreset関数を追加
old3 = "function bulkSyncPresets() { var v = parseInt(document.getElementById('bulk-qty-input').value) || 0; document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.toggle('active', parseInt(b.dataset.val) === v); }); }\nbulkSyncPresets();"
new3 = "function bulkSyncPresets() { var v = parseInt(document.getElementById('bulk-qty-input').value) || 0; document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.toggle('active', parseInt(b.dataset.val) === v); }); }\nbulkSyncPresets();\n\n// ===== 配送先プリセット =====\nvar deliveryPreset = {\n    name:    '{{ $organization->delivery_name ?? \"\" }}',\n    postal:  '{{ $organization->delivery_postal ?? \"\" }}',\n    address: '{{ $organization->delivery_address ?? \"\" }}',\n    phone:   '{{ $organization->delivery_phone ?? \"\" }}',\n};\n\nfunction applyPreset() {\n    document.getElementById('bulk-delivery-name').value    = deliveryPreset.name;\n    document.getElementById('bulk-delivery-postal').value  = deliveryPreset.postal;\n    document.getElementById('bulk-delivery-address').value = deliveryPreset.address;\n    document.getElementById('bulk-delivery-phone').value   = deliveryPreset.phone;\n}"

changes = [(old1, new1), (old2, new2), (old3, new3)]
for i, (old, new) in enumerate(changes, 1):
    if old in c:
        c = c.replace(old, new)
        print(f"変更{i}: OK")
    else:
        print(f"変更{i}: NG（文字列が見つかりません）")

with open('resources/views/partner/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print("完了")
