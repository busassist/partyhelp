{{-- Lead Opportunity – Venue, immediate. PRD 6.1. Inline styles for email clients. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $introText = $intro_text ?? 'A new lead matches your venue. Purchase it to receive full customer details and the function pack.';
    $ctaButtonLabel = $cta_button_label ?? ('Purchase This Lead - $' . ($price ?? '—'));
    $footerBalanceText = $footer_balance_text ?? ('Your credit balance: $' . ($creditBalance ?? '0.00'));
    $topupLinkText = $topup_link_text ?? 'Top up credits';
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
@if($viewInBrowserUrl ?? null)
<tr><td align="center" style="padding-bottom:16px;"><a href="{{ $viewInBrowserUrl }}" style="font-size:12px;color:#94a3b8;text-decoration:underline;">View in browser</a></td></tr>
@endif
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">New lead for you</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $introText !!}</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;">
<tr><td style="padding:20px 24px;">
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Occasion:</strong> {{ $occasion ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Location:</strong> {{ $suburb ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Guests:</strong> {{ $guestCount ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Preferred date:</strong> {{ $preferredDate ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Room style:</strong> {{ $roomStyles ?? '—' }}</p>
<p style="font-size:16px;font-weight:700;color:#fff;margin:16px 0 0;">Price: ${{ $price ?? '—' }}</p>
</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{{ $purchaseUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">{!! $ctaButtonLabel !!}</a>
</p>
<p style="font-size:14px;color:#94a3b8;text-align:center;margin:0 0 8px;">{!! $footerBalanceText !!}</p>
<p style="font-size:14px;text-align:center;margin:0;"><a href="{{ $topUpUrl ?? '#' }}" style="color:#a855f7;text-decoration:underline;">{!! $topupLinkText !!}</a></p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
