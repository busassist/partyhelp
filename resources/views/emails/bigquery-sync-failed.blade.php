{{-- BigQuery sync failure – Admin alert. Branded, inline styles. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">BigQuery sync failed</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">The daily sync of platform data to BigQuery did not complete successfully. Please check the error below and the Server Health page in Admin.</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:rgba(220,38,38,0.15);border:1px solid rgba(220,38,38,0.4);border-radius:12px;"><tr><td style="padding:16px 20px;">
<p style="font-size:14px;font-weight:600;color:#fca5a5;margin:0 0 8px;">Error</p>
<p style="font-size:14px;color:#cbd5e1;margin:0;word-break:break-word;">{{ $errorMessage ?? 'Unknown error' }}</p>
</td></tr></table>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;margin-top:16px;"><tr><td style="padding:20px 24px;">
<p style="font-size:12px;color:#94a3b8;margin:0 0 6px;">Time (server): {{ $startedAt ?? '—' }}</p>
<p style="font-size:12px;color:#64748b;margin:0;white-space:pre-wrap;font-family:monospace;word-break:break-all;">{{ $errorDetail ?? '' }}</p>
</td></tr></table>
<p style="text-align:center;margin:24px 0;">
<a href="{{ $settingsUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">Open Server Health</a>
</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp Admin</p></td></tr>
</table>
</td></tr>
</table>
