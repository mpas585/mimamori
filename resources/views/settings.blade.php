@extends('layouts.app')

@section('title', '設定 - みまもりデバイス')

@section('styles')
<style>
    .section-title {
        font-size: 13px;
        font-weight: 500;
        color: #8b7e6a;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e8e2d8;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #6b6358;
        margin-bottom: 6px;
    }
    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d8d0c4;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        color: #4a4a4a;
    }
    .form-input:focus {
        outline: none;
        border-color: #8b7e6a;
        background: #fff;
    }
    .form-hint {
        font-size: 11px;
        color: #aaa;
        margin-top: 4px;
    }
    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d8d0c4;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Noto Sans JP', sans-serif;
        background: #faf8f4;
        color: #4a4a4a;
        appearance: auto;
    }
    .form-select:focus {
        outline: none;
        border-color: #8b7e6a;
    }
    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
    }
    .toggle-label {
        font-size: 14px;
        color: #4a4a4a;
    }
    .toggle-sub {
        font-size: 11px;
        color: #aaa;
        margin-top: 2px;
    }
    .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #ccc;
        border-radius: 26px;
        transition: 0.3s;
    }
    .toggle-slider:before {
        content: "";
        position: absolute;
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider {
        background: #8b7e6a;
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }
    .save-btn {
        width: 100%;
        padding: 12px;
        background: #8b7e6a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }
    .save-btn:hover {
        background: #7a6e5b;
    }
    .test-btn {
        width: 100%;
        padding: 12px;
        background: #fff;
        color: #8b7e6a;
        border: 1px solid #8b7e6a;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Noto Sans JP', sans-serif;
        cursor: pointer;
        transition: background 0.2s;
    }
    .test-btn:hover {
        background: #f5f0e8;
    }
    .premium-badge {
        display: inline-block;
        background: #e0d8cc;
        color: #8b7e6a;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
    }
    .disabled-row {
        opacity: 0.5;
    }
</style>
@endsection

@section('content')

{{-- 通知設定 --}}
<div class="card">
    <div class="section-title">通知設定</div>
    <form method="POST" action="/settings/notification">
        @csrf

        <div class="form-group">
            <label class="form-label">通知先メールアドレス</label>
            <input type="email" name="email_1" class="form-input" value="{{ old('email_1', $notif->email_1) }}" placeholder="example@mail.com">
            @error('email_1')
                <div style="color:#c62828;font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="toggle-row">
            <div>
                <div class="toggle-label">メール通知</div>
                <div class="toggle-sub">未検知アラート・電池低下をメールで受信</div>
            </div>
            <label class="toggle-switch">
                <input type="hidden" name="email_enabled" value="0">
                <input type="checkbox" name="email_enabled" value="1" {{ old('email_enabled', $notif->email_enabled) ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <button type="submit" class="save-btn">通知設定を保存</button>
    </form>

    <div style="margin-top:16px;">
        <form method="POST" action="/settings/test-notification">
            @csrf
            <button type="submit" class="test-btn">📧 テスト通知を送信</button>
        </form>
    </div>

    {{-- TODO: Phase2で実装 - SMS・電話（プレミアム）
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #e8e2d8;">
        <div class="toggle-row disabled-row">
            <div>
                <div class="toggle-label">SMS通知 <span class="premium-badge">Premium 🔒</span></div>
                <div class="toggle-sub">携帯電話にSMSで通知</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" disabled>
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="toggle-row disabled-row">
            <div>
                <div class="toggle-label">自動音声電話 <span class="premium-badge">Premium 🔒</span></div>
                <div class="toggle-sub">固定電話にも対応</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" disabled>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>
    --}}
</div>

{{-- デバイス設定 --}}
<div class="card">
    <div class="section-title">デバイス設定</div>
    <form method="POST" action="/settings/device">
        @csrf

        <div class="form-group">
            <label class="form-label">未検知アラート</label>
            <select name="alert_threshold_hours" class="form-select">
                @foreach([12 => '12時間（早期発見）', 24 => '24時間（標準）', 36 => '36時間（ゆるめ）', 48 => '48時間（外出多い人向け）', 72 => '72時間（最長）'] as $val => $label)
                    <option value="{{ $val }}" {{ old('alert_threshold_hours', $device->alert_threshold_hours) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-hint">最後の検知からこの時間が経過すると通知します</div>
        </div>

        <div class="toggle-row">
            <div>
                <div class="toggle-label">ペット除外</div>
                <div class="toggle-sub">身長が低い検知をペットとして除外</div>
            </div>
            <label class="toggle-switch">
                <input type="hidden" name="pet_exclusion_enabled" value="0">
                <input type="checkbox" name="pet_exclusion_enabled" value="1" {{ old('pet_exclusion_enabled', $device->pet_exclusion_enabled) ? 'checked' : '' }} id="petToggle">
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" id="petThresholdGroup">
            <label class="form-label">ペット除外閾値</label>
            <select name="pet_exclusion_threshold_cm" class="form-select">
                @foreach([80 => '80cm（小型犬）', 100 => '100cm（標準）', 120 => '120cm（大型犬）', 150 => '150cm（子供も除外）'] as $val => $label)
                    <option value="{{ $val }}" {{ old('pet_exclusion_threshold_cm', $device->pet_exclusion_threshold_cm) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-hint">設置高さからこの距離以内を人間と判定</div>
        </div>

        <button type="submit" class="save-btn">デバイス設定を保存</button>
    </form>
</div>

{{-- デバイス情報（読み取り専用） --}}
<div class="card">
    <div class="section-title">デバイス情報</div>
    <table style="width:100%;font-size:13px;">
        <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:8px 0;color:#999;width:120px;">品番</td>
            <td style="padding:8px 0;font-family:monospace;">{{ $device->device_id }}</td>
        </tr>
        <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:8px 0;color:#999;">設置高さ</td>
            <td style="padding:8px 0;">{{ $device->install_height_cm }}cm</td>
        </tr>
        <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:8px 0;color:#999;">保証期限</td>
            <td style="padding:8px 0;">{{ $device->warranty_expires_at ? $device->warranty_expires_at->format('Y/m/d') : '---' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#999;">初回起動</td>
            <td style="padding:8px 0;">{{ $device->activated_at ? $device->activated_at->format('Y/m/d H:i') : '---' }}</td>
        </tr>
    </table>
</div>

<script>
    // ペット除外トグルで閾値の表示/非表示
    const petToggle = document.getElementById('petToggle');
    const petGroup = document.getElementById('petThresholdGroup');
    function updatePetGroup() {
        petGroup.style.display = petToggle.checked ? 'block' : 'none';
    }
    petToggle.addEventListener('change', updatePetGroup);
    updatePetGroup();
</script>
@endsection
