#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
placeholder追加パッチスクリプト
使い方: cd C:\dev && python apply_placeholders.py
"""
import os
import sys

BASE = os.path.dirname(os.path.abspath(__file__))

PATCHES = [
    # =========================================================
    # resources/views/partner/dashboard.blade.php  (2箇所)
    # =========================================================
    {
        'file': os.path.join(BASE, 'resources', 'views', 'partner', 'dashboard.blade.php'),
        'replacements': [
            (
                'id="editTenantName">',
                'id="editTenantName" placeholder="山田 太郎">'
            ),
            (
                'id="editMemo">',
                'id="editMemo" placeholder="メモを入力...">'
            ),
        ],
    },

    # =========================================================
    # resources/views/partner/master.blade.php  (17箇所)
    # =========================================================
    {
        'file': os.path.join(BASE, 'resources', 'views', 'partner', 'master.blade.php'),
        'replacements': [
            # addOrgModal - 組織名
            (
                '<input type="text" name="name" class="form-input" required>',
                '<input type="text" name="name" class="form-input" placeholder="例: ABC管理株式会社" required>'
            ),
            # addOrgModal - 担当者名
            (
                '<input type="text" name="contact_name" class="form-input">',
                '<input type="text" name="contact_name" class="form-input" placeholder="例: 山田 太郎">'
            ),
            # addOrgModal - 連絡先メール
            (
                '<input type="email" name="contact_email" class="form-input">',
                '<input type="email" name="contact_email" class="form-input" placeholder="admin@example.com">'
            ),
            # addOrgModal - 電話番号
            (
                '<input type="text" name="contact_phone" class="form-input">',
                '<input type="text" name="contact_phone" class="form-input" placeholder="03-0000-0000">'
            ),
            # addOrgModal - 住所
            (
                '<input type="text" name="address" class="form-input">',
                '<input type="text" name="address" class="form-input" placeholder="東京都千代田区〇〇 1-2-3">'
            ),
            # editOrgModal - 組織名
            (
                '<input type="text" name="name" id="editOrgName" class="form-input" required>',
                '<input type="text" name="name" id="editOrgName" class="form-input" placeholder="例: ABC管理株式会社" required>'
            ),
            # editOrgModal - 担当者名
            (
                '<input type="text" name="contact_name" id="editOrgContactName" class="form-input">',
                '<input type="text" name="contact_name" id="editOrgContactName" class="form-input" placeholder="例: 山田 太郎">'
            ),
            # editOrgModal - 連絡先メール
            (
                '<input type="email" name="contact_email" id="editOrgContactEmail" class="form-input" required>',
                '<input type="email" name="contact_email" id="editOrgContactEmail" class="form-input" placeholder="admin@example.com" required>'
            ),
            # editOrgModal - 電話番号
            (
                '<input type="text" name="contact_phone" id="editOrgContactPhone" class="form-input">',
                '<input type="text" name="contact_phone" id="editOrgContactPhone" class="form-input" placeholder="03-0000-0000">'
            ),
            # editOrgModal - 住所
            (
                '<input type="text" name="address" id="editOrgAddress" class="form-input">',
                '<input type="text" name="address" id="editOrgAddress" class="form-input" placeholder="東京都千代田区〇〇 1-2-3">'
            ),
            # editOrgModal - 通知メール1
            (
                '<input type="email" name="notification_email_1" id="editOrgEmail1" class="form-input">',
                '<input type="email" name="notification_email_1" id="editOrgEmail1" class="form-input" placeholder="admin@example.com">'
            ),
            # editOrgModal - 通知メール2
            (
                '<input type="email" name="notification_email_2" id="editOrgEmail2" class="form-input">',
                '<input type="email" name="notification_email_2" id="editOrgEmail2" class="form-input" placeholder="admin@example.com（任意）">'
            ),
            # editOrgModal - 通知メール3
            (
                '<input type="email" name="notification_email_3" id="editOrgEmail3" class="form-input">',
                '<input type="email" name="notification_email_3" id="editOrgEmail3" class="form-input" placeholder="admin@example.com（任意）">'
            ),
            # editOrgModal - SMS1
            (
                '<input type="text" name="notification_sms_1" id="editOrgSms1" class="form-input">',
                '<input type="text" name="notification_sms_1" id="editOrgSms1" class="form-input" placeholder="09012345678">'
            ),
            # editOrgModal - SMS2
            (
                '<input type="text" name="notification_sms_2" id="editOrgSms2" class="form-input">',
                '<input type="text" name="notification_sms_2" id="editOrgSms2" class="form-input" placeholder="09012345678（任意）">'
            ),
            # orgEditUserModal - 名前
            (
                '<input type="text" id="orgEditUserName" class="form-input">',
                '<input type="text" id="orgEditUserName" class="form-input" placeholder="例: 田中 一郎">'
            ),
            # orgEditUserModal - メールアドレス
            (
                '<input type="email" id="orgEditUserEmail" class="form-input">',
                '<input type="email" id="orgEditUserEmail" class="form-input" placeholder="partner@example.com">'
            ),
        ],
    },
]


def apply_patch(patch):
    path = patch['file']
    if not os.path.exists(path):
        print(f'[SKIP] ファイルが見つかりません: {path}')
        return

    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    changed = 0
    for old, new in patch['replacements']:
        if old in content:
            content = content.replace(old, new, 1)
            changed += 1
        else:
            print(f'  [WARN] 対象文字列が見つかりません（既に適用済みか確認してください）: {old[:60]}...')

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

    rel = os.path.relpath(path, BASE)
    print(f'[OK] {rel} ({changed}/{len(patch["replacements"])} 箇所変更)')


if __name__ == '__main__':
    print('=== placeholder パッチ適用 ===')
    for patch in PATCHES:
        apply_patch(patch)
    print('=== 完了 ===')
