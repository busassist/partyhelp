{{-- Lead No Longer Available – Venue, when fulfilled or expired. Inline styles for email clients. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'Lead no longer available';
    $bodyText = $body_text ?? 'This lead has been fulfilled or has expired. You can view other available leads in your dashboard.';
    $ctaText = $cta_text ?? 'View available leads';
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
@if($viewInBrowserUrl ?? null)
<tr><td align="center" style="padding-bottom:16px;"><a href="{{ $viewInBrowserUrl }}" style="font-size:12px;color:#94a3b8;text-decoration:underline;">View in browser</a></td></tr>
@endif
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">{!! $headerText !!}</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 12px;">Lead in {{ $suburb ?? 'this area' }} ({{ $occasion ?? '—' }}) is no longer available – {{ $reason ?? 'fulfilled or expired' }}.</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $bodyText !!}</p>
<p style="text-align:center;margin:0 0 24px;"><a href="{{ $dashboardUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:15px;font-weight:600;padding:12px 24px;text-decoration:none;border-radius:8px;">{!! $ctaText !!}</a></p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
