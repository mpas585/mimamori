{{-- ===== モーダル: 組織通知設定 ===== --}}
<div id="notificationModal" class="modal-overlay" onclick="if(event.target===this)hideModal('notificationModal')">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <h3>🔔 通知設定</h3>
            <button class="modal-close" onclick="hideModal('notificationModal')">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:var(--gray-600);margin-bottom:16px;">
                アラート発生時に組織管理者へ通知する連絡先を設定します。<br>
                <span style="font-size:12px;color:var(--gray-400);">※ デバイス個別の通知設定とは別に送信されます</span>
            </p>

            {{-- メール通知 --}}
            <div style="margin-bottom:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:12px;background:var(--beige);border-radius:var(--radius);">
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">📧 メール通知</p>
                    </div>
                    <label class="watch-toggle">
                        <input type="checkbox" id="orgNotifEnabled" checked>
                        <span class="watch-slider"></span>
                    </label>
                </div>
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

            {{-- SMS通知 --}}
            <div style="margin-bottom:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:12px;background:var(--beige);border-radius:var(--radius);">
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">💬 SMS通知</p>
                        <p style="font-size:11px;color:var(--gray-500);margin-top:2px;">プレミアムプラン</p>
                    </div>
                    <label class="watch-toggle">
                        <input type="checkbox" id="orgNotifSmsEnabled">
                        <span class="watch-slider"></span>
                    </label>
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">SMS通知先①</label>
                    <input type="tel" class="form-input" id="orgNotifSms1" placeholder="09012345678">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">SMS通知先②（任意）</label>
                    <input type="tel" class="form-input" id="orgNotifSms2" placeholder="09012345678">
                </div>
            </div>

            <div style="margin-top:16px;padding:12px;background:#f0fdf4;border-radius:var(--radius);border:1px solid #bbf7d0;">
                <p style="font-size:12px;color:#15803d;font-weight:600;margin-bottom:4px;">📌 通知の仕組み</p>
                <ul style="font-size:12px;color:#166534;margin:0;padding-left:16px;">
                    <li>未検知アラート・通信途絶が発生した時に送信されます</li>
                    <li>デバイス個別の通知先とは独立して動作します</li>
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
