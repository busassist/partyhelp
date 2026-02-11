{{-- Venue set password – sent when admin creates a venue and "New venues email password" is on. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $venueName = $venueName ?? 'your venue';
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">Set your venue portal password</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">Your venue <strong style="color:#a855f7;">{{ $venueName }}</strong> has been set up on Partyhelp.</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">Click the button below to set your password and sign in to the venue portal. You can then manage your profile, view leads and buy credits.</p>
<p style="text-align:center;margin:24px 0;">
<a href="{{ $setPasswordUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">Set password</a>
</p>
<p style="font-size:14px;color:#94a3b8;line-height:1.6;">If you did not expect this email, you can ignore it. The link will expire after 60 minutes.</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
