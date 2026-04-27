<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ICCID不一致を検知しました</title>
</head>
<body style="margin:0;padding:0;background:#f5f1ea;font-family:'Hiragino Sans','Yu Gothic','Noto Sans JP',sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f1ea;padding:24px 0;">
<tr>
<td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
{{-- ヘッダー --}}
<tr>
<td style="background:#c62828;padding:20px 24px;text-align:center;">
<span style="font-size:24px;">🚨</span>
<br>
<span style="color:#ffffff;font-size:14px;font-weight:600;">みまもりデバイス・運営警告</span>
</td>
</tr>
{{-- タイトル --}}
<tr>
<td style="padding:28px 24px 0;">
<h1 style="margin:0;font-size:18px;font-weight:700;color:#292524;text-align:center;">
🔴 ICCID不一致を検知しました
</h1>
<p style="margin:12px 0 0;font-size:13px;color:#78716c;text-align:center;line-height:1.6;">
登録されたSIM以外からのデータ送信を検知しました。<br>
不正利用または交換ミスの可能性があります。
</p>
</td>
</tr>
{{-- 本文 --}}
<tr>
<td style="padding:20px 24px;">
<div style="background:#fef2f2;border-radius:8px;padding:20px;border-left:4px solid #c62828;">
<table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#44403c;line-height:1.7;">
<tr>
<td style="padding:4px 0;width:120px;color:#78716c;">検知日時</td>
<td style="padding:4px 0;">{{ $detectedAt }}</td>
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
<td style="padding:4px 0;color:#78716c;">期待ICCID</td>
<td style="padding:4px 0;font-family:monospace;font-size:13px;">{{ $expectedIccid }}</td>
</tr>
<tr>
<td style="padding:4px 0;color:#78716c;">受信ICCID</td>
<td style="padding:4px 0;font-family:monospace;font-size:13px;color:#c62828;font-weight:600;">{{ $receivedIccid }}</td>
</tr>
@if($clientIp)
<tr>
<td style="padding:4px 0;color:#78716c;">送信元IP</td>
<td style="padding:4px 0;font-family:monospace;font-size:13px;">{{ $clientIp }}</td>
</tr>
@endif
</table>
</div>
</td>
</tr>
{{-- 管理画面リンク --}}
<tr>
<td style="padding:0 24px 24px;text-align:center;">
<a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 28px;background:#c62828;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">
デバイス詳細を確認する
</a>
</td>
</tr>
{{-- 注意書き --}}
<tr>
<td style="padding:0 24px 28px;">
<p style="margin:0;font-size:12px;color:#a8a29e;line-height:1.6;text-align:center;">
※ 該当データの受信は自動的に拒否されています（HTTP 403）。<br>
※ 詳細は storage/logs/laravel.log の「ICCID mismatch」を参照してください。
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
