#!/usr/bin/env python3
# サーバー上で実行: python3 patch_billing_start.py
# 実行場所: ~/gud.co.jp/public_html/dev.gud.co.jp/

# ===== MasterController.php の変更 =====
with open('app/Http/Controllers/Partner/MasterController.php', 'r') as f:
    c = f.read()

c = c.replace(
    "            'registered_at'          => $device->created_at->format('Y/m/d'),\n            'schedules'              => $schedules,",
    "            'registered_at'          => $device->created_at->format('Y/m/d'),\n            'billing_start_at'       => $device->billing_start_at ? $device->billing_start_at->format('Y-m-d') : null,\n            'schedules'              => $schedules,"
)

c = c.replace(
    "            'pet_exclusion_enabled' => 'nullable|boolean',\n        ]);",
    "            'pet_exclusion_enabled' => 'nullable|boolean',\n            'billing_start_at'      => 'nullable|date',\n        ]);"
)

c = c.replace(
    "            'pet_exclusion_enabled' => $request->has('pet_exclusion_enabled') ? (int) $request->pet_exclusion_enabled : $device->pet_exclusion_enabled,\n        ]);",
    "            'pet_exclusion_enabled' => $request->has('pet_exclusion_enabled') ? (int) $request->pet_exclusion_enabled : $device->pet_exclusion_enabled,\n            'billing_start_at'      => $request->billing_start_at ?: null,\n        ]);"
)

with open('app/Http/Controllers/Partner/MasterController.php', 'w') as f:
    f.write(c)

check = open('app/Http/Controllers/Partner/MasterController.php').read()
print('MasterController billing_start_at:', 'billing_start_at' in check)

# ===== master.blade.php の変更 =====
with open('resources/views/partner/master.blade.php', 'r') as f:
    b = f.read()

# 登録情報グリッドに課金開始日を追加
b = b.replace(
    '<div class="modal-section">\n                <div class="modal-section-title">📝 登録情報</div>\n                <div class="detail-grid">\n                    <div class="detail-item"><p class="detail-item-label">登録日</p><p class="detail-item-value" id="masterDetailRegistered">-</p></div>\n                    <div class="detail-item"><p class="detail-item-label">メモ</p><input type="text" class="detail-form-input" id="masterDetailMemo" placeholder="メモを追加..." maxlength="200"></div>\n                </div>\n            </div>',
    '<div class="modal-section">\n                <div class="modal-section-title">📝 登録情報</div>\n                <div class="detail-grid">\n                    <div class="detail-item"><p class="detail-item-label">登録日</p><p class="detail-item-value" id="masterDetailRegistered">-</p></div>\n                    <div class="detail-item"><p class="detail-item-label">メモ</p><input type="text" class="detail-form-input" id="masterDetailMemo" placeholder="メモを追加..." maxlength="200"></div>\n                    <div class="detail-item" style="grid-column:span 2;"><p class="detail-item-label">💰 課金開始日</p><input type="date" class="detail-form-input" id="masterDetailBillingStart"><p style="font-size:11px;color:var(--gray-400);margin-top:4px;">未設定のデバイスは月次課金の対象外です</p></div>\n                </div>\n            </div>'
)

# showDeviceDetail に billing_start_at セットを追加
b = b.replace(
    "        document.getElementById('masterDetailRegistered').textContent = d.registered_at || '-';\n        document.getElementById('masterDetailMemo').value = d.memo || '';",
    "        document.getElementById('masterDetailRegistered').textContent = d.registered_at || '-';\n        document.getElementById('masterDetailMemo').value = d.memo || '';\n        // 課金開始日（未設定なら翌月1日をデフォルト表示）\n        if (d.billing_start_at) {\n            document.getElementById('masterDetailBillingStart').value = d.billing_start_at;\n        } else {\n            const nextMonth = new Date(); nextMonth.setDate(1); nextMonth.setMonth(nextMonth.getMonth() + 1);\n            document.getElementById('masterDetailBillingStart').value = nextMonth.toISOString().slice(0, 10);\n        }"
)

# masterSaveAssignment の payload に billing_start_at を追加
b = b.replace(
    "        pet_exclusion_enabled: document.getElementById('masterDetailPetExclusion').value === '1' ? 1 : 0,\n    };",
    "        pet_exclusion_enabled: document.getElementById('masterDetailPetExclusion').value === '1' ? 1 : 0,\n        billing_start_at: document.getElementById('masterDetailBillingStart').value || null,\n    };"
)

with open('resources/views/partner/master.blade.php', 'w') as f:
    f.write(b)

check_b = open('resources/views/partner/master.blade.php').read()
print('master.blade.php billing_start_at:', 'billing_start_at' in check_b)
print('master.blade.php 課金開始日:', '課金開始日' in check_b)
print('完了')
