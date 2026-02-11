{{-- Venue registration approved – sent when admin approves a self-registered venue. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'Your registration has been approved';
    $introText = $intro_text ?? 'Your venue registration has been approved. You may now sign in to the Partyhelp venue portal to begin receiving leads.';
    $ctaLabel = $cta_button_label ?? 'Sign in to venue portal';
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">{!! $headerText !!}</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $introText !!}</p>
<p style="text-align:center;margin:24px 0;">
<a href="{{ $loginUrl ?? url('/venue/login') }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">{!! $ctaLabel !!}</a>
</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
