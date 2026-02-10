{{-- Failed Top-Up Notification – Venue, when auto top-up fails. Inline styles for email clients. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'Credit top-up unsuccessful';
    $introText = $intro_text ?? 'We tried to top up your credit balance but the payment could not be processed. Please update your payment method to continue receiving lead opportunities.';
    $ctaButtonLabel = $cta_button_label ?? 'Update payment method';
    $closingText = $closing_text ?? 'Your lead opportunities may be paused until your payment method is updated.';
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
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 12px;">Hi {{ $venueName ?? 'there' }},</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $introText !!}</p>
@if($failureReason ?? null)
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:12px;"><tr><td style="padding:16px 20px;">
<p style="font-size:14px;color:#fca5a5;margin:0;">{{ $failureReason }}</p>
<p style="font-size:14px;color:#94a3b8;margin:8px 0 0;">Attempted amount: ${{ $attemptedAmount ?? '—' }}</p>
</td></tr></table>
@endif
<p style="text-align:center;margin:24px 0;"><a href="{{ $updatePaymentUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">{!! $ctaButtonLabel !!}</a></p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $closingText !!}</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
