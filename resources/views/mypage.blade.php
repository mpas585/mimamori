@extends('layouts.app')

@section('title', 'マイページ - みまもりデバイス')

@section('content')
<div class="card">
    <div class="card-title">デバイス情報</div>
    <table style="width:100%;font-size:14px;border-collapse:collapse;">
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px 0;color:#999;width:120px;">品番</td>
            <td style="padding:10px 0;font-family:monospace;">{{ $device->device_id }}</td>
        </tr>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px 0;color:#999;">ステータス</td>
            <td style="padding:10px 0;">
                @switch($device->status)
                    @case('normal')
                        <span style="color:#2e7d32;">🟢 正常</span>
                        @break
                    @case('warning')
                        <span style="color:#f57c00;">🟡 注意</span>
                        @break
                    @case('alert')
                        <span style="color:#c62828;">🔴 未検知アラート</span>
                        @break
                    @case('offline')
                        <span style="color:#666;">⚫ 離線</span>
                        @break
                    @case('inactive')
                        <span style="color:#999;">⚪ 未稼働</span>
                        @break
                @endswitch
            </td>
        </tr>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px 0;color:#999;">電池残量</td>
            <td style="padding:10px 0;">{{ $device->battery_pct !== null ? $device->battery_pct . '%' : '---' }}</td>
        </tr>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px 0;color:#999;">電波強度</td>
            <td style="padding:10px 0;">{{ $device->rssi !== null ? $device->rssi . 'dBm' : '---' }}</td>
        </tr>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px 0;color:#999;">最終受信</td>
            <td style="padding:10px 0;">{{ $device->last_received_at ? $device->last_received_at->format('Y/m/d H:i') : '---' }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0;color:#999;">最終検知</td>
            <td style="padding:10px 0;">{{ $device->last_human_detected_at ? $device->last_human_detected_at->format('Y/m/d H:i') : '---' }}</td>
        </tr>
    </table>
</div>

<div class="card">
    <div class="card-title">最近の検知ログ</div>
    @if($logs->isEmpty())
        <p style="color:#aaa;font-size:13px;text-align:center;padding:20px 0;">まだ検知データがありません</p>
    @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #eee;">
                    <th style="padding:8px 0;text-align:left;color:#999;font-weight:400;">期間</th>
                    <th style="padding:8px 0;text-align:center;color:#999;font-weight:400;">人</th>
                    <th style="padding:8px 0;text-align:center;color:#999;font-weight:400;">ペット</th>
                    <th style="padding:8px 0;text-align:right;color:#999;font-weight:400;">電池</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:8px 0;">{{ $log->period_start->format('m/d H:i') }}</td>
                    <td style="padding:8px 0;text-align:center;">{{ $log->human_count }}</td>
                    <td style="padding:8px 0;text-align:center;">{{ $log->pet_count }}</td>
                    <td style="padding:8px 0;text-align:right;">{{ $log->battery_pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div style="text-align:center;margin-top:12px;">
        <a href="/logs" style="color:#8b7e6a;font-size:13px;text-decoration:none;">すべてのログを見る →</a>
    </div>
</div>
@endsection
