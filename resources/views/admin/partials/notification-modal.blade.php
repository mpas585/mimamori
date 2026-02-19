{{-- ===== モーダル: 組織通知設定 ===== --}}
<div id="notificationModal" class="modal-overlay" onclick="if(event.target===this)hideModal('notificationModal')">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <h3>🔔 通知設定</h3>
            <button class="modal-close" onclick="hideModal('notificationModal')">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:var(--gray-600);margin-bottom:16px;">
                アラート発生時に組織管理者へ通知するメールアドレスを設定します。<br>
                <span style="font-size:12px;color:var(--gray-400);">※ デバイス個別の通知設定とは別に送信されます</span>
            </p>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:12px;background:var(--beige);border-radius:var(--radius);">
                <span style="font-size:13px;font-weight:600;color:var(--gray-700);">通知を有効にする</span>
                <label class="watch-toggle">
                    <input type="checkbox" id="orgNotifEnabled" checked>
                    <span class="watch-slider"></span>
                </label>
            </div>

            <div id="orgNotifEmailFields">
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">通知メール①</label>
                    <input type="email" class="form-input" id="orgNotifEmail1" placeholder="admin@example.com">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">通知メール②（任意）</label>
                    <input type="email" class="form-input" id="orgNotifEmail2" placeholder="">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">通知メール③（任意）</label>
                    <input type="email" class="form-input" id="orgNotifEmail3" placeholder="">
                </div>
            </div>

            <div style="margin-top:16px;padding:12px;background:#f0fdf4;border-radius:var(--radius);border:1px solid #bbf7d0;">
                <p style="font-size:12px;color:#15803d;font-weight:600;margin-bottom:4px;">📌 通知の仕組み</p>
                <ul style="font-size:12px;color:#166534;margin:0;padding-left:16px;">
                    <li>未検知アラート・通信途絶が発生した時に送信されます</li>
                    <li>デバイス個別の通知メールとは独立して動作します</li>
                    <li>デバイス側に通知先が未設定でも、こちらには送信されます</li>
                    <li>同じメールアドレスが両方に設定されている場合、二重送信はされません</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('notificationModal')">キャンセル</button>
            <button class="btn btn-primary" onclick="saveOrgNotification()">保存</button>
        </div>
    </div>
</div>
