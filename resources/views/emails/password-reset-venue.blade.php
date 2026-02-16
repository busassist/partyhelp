{{-- Venue password reset – Partyhelp branded. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">Reset your password</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">You are receiving this email because we received a password reset request for your venue account.</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">Click the button below to choose a new password and sign in to the venue portal.</p>
<p style="text-align:center;margin:24px 0;">
<a href="{{ $resetUrl }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">Reset password</a>
</p>
<p style="font-size:14px;color:#94a3b8;line-height:1.6;">This link will expire in {{ $expireMinutes }} minutes.</p>
<p style="font-size:14px;color:#94a3b8;line-height:1.6;margin-top:12px;">If you did not request a password reset, you can ignore this email. No further action is required.</p>
<p style="font-size:13px;color:#64748b;line-height:1.6;margin-top:24px;">If the button does not work, copy and paste this link into your browser:</p>
<p style="font-size:13px;color:#94a3b8;word-break:break-all;margin:8px 0 0;">{{ $resetUrl }}</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Regards, Partyhelp</p></td></tr>
</table>
</td></tr>
</table>
