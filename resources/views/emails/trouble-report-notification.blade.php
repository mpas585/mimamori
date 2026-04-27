<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>{{ $typeLabel }}を受け付けました</title>
</head>
<body style="margin:0;padding:0;background:#f5f1ea;font-family:'Hiragino Sans','Yu Gothic','Noto Sans JP',sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f1ea;padding:24px 0;">
<tr>
<td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
{{-- ヘッダー --}}
<tr>
<td style="background:{{ $report->type === 'abuse_report' ? '#c62828' : '#0f766e' }};padding:20px 24px;text-align:center;">
<span style="font-size:24px;">🧈</span>
<br>
<span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス・運営通知</span>
</td>
</tr>
{{-- タイトル --}}
<tr>
<td style="padding:28px 24px 0;">
<h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
@if($report->type === 'abuse_report')
🚨 不正利用通報を受け付けました
@else
🔧 故障・交換申請を受け付けました
@endif
</h1>
</td>
</tr>
{{-- 本文 --}}
<tr>
<td style="padding:20px 24px;">
<div style="background:#faf8f4;border-radius:8px;padding:20px;border-left:4px solid {{ $report->type === 'abuse_report' ? '#c62828' : '#0f766e' }};">
<table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#44403c;line-height:1.7;">
<tr>
<td style="padding:4px 0;width:120px;color:#78716c;">受付日時</td>
<td style="padding:4px 0;">{{ $submittedAt }}</td>
</tr>
<tr>
<td style="padding:4px 0;color:#78716c;">申請種別</td>
<td style="padding:4px 0;">{{ $typeLabel }}</td>
</tr>
<tr>
<td style="padding:4px 0;color:#78716c;">デバイスID</td>
<td style="padding:4px 0;">{{ $deviceIdentifier }}</td>
</tr>
@if($organizationName)
<tr>
<td style="padding:4px 0;color:#78716c;">所属組織</td>
<td style="padding:4px 0;">{{ $organizationName }}</td>
</tr>
@endif
<tr>
<td style="padding:4px 0;color:#78716c;">申請元</td>
<td style="padding:4px 0;">
@if($reporterRole === 'partner')
パートナー管理画面（{{ $reporterName ?: '管理者' }}）
@else
ユーザー本人
@endif
</td>
</tr>
<tr>
<td style="padding:4px 0;color:#78716c;vertical-align:top;">症状</td>
<td style="padding:4px 0;">{{ $symptomLabel }}</td>
</tr>
@if($description !== '')
<tr>
<td style="padding:8px 0 4px;color:#78716c;vertical-align:top;" colspan="2">詳細</td>
</tr>
<tr>
<td colspan="2" style="padding:4px 0 0;">
<div style="background:#ffffff;border:1px solid #e7e5e4;border-radius:6px;padding:12px;white-space:pre-wrap;font-size:13px;line-height:1.7;color:#292524;">{{ $description }}</div>
</td>
</tr>
@endif
</table>
</div>
</td>
</tr>
{{-- 管理画面リンク --}}
<tr>
<td style="padding:0 24px 24px;text-align:center;">
<a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 28px;background:#0f766e;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">
管理画面で確認する
</a>
</td>
</tr>
{{-- 注意書き --}}
<tr>
<td style="padding:0 24px 28px;">
<p style="margin:0;font-size:12px;color:#a8a29e;line-height:1.6;text-align:center;">
※ このメールは運営宛の自動通知です。<br>
※ 申請内容は管理画面からステータス更新できます。
</p>
</td>
</tr>
{{-- フッター --}}
<tr>
<td style="background:#faf8f4;padding:16px 24px;text-align:center;border-top:1px solid #e7e5e4;">
<p style="margin:0;font-size:11px;color:#a8a29e;">
みまもりデバイス — 安心の見守りサービス
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
