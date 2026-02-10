{{-- Invoice / Receipt – Venue, after payment or top-up. Inline styles for email clients. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'Your payment confirmation';
    $introText = $intro_text ?? 'Thank you for your payment. Details are below.';
    $viewStatementLabel = $view_statement_label ?? 'View invoice / receipt';
    $closingText = $closing_text ?? 'If you have any questions, please contact us.';
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
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">Hi {{ $venueName ?? 'there' }},</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $introText !!}</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;"><tr><td style="padding:20px 24px;">
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">{{ $documentType ?? 'Receipt' }} #{{ $invoiceNumber ?? '—' }}</strong></p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;">Amount: ${{ $amount ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0;">{{ $description ?? 'Payment received' }}</p>
</td></tr></table>
<p style="text-align:center;margin:24px 0;"><a href="{{ $viewUrl ?? '#' }}" style="display:inline-block;background:#7c3aed;color:#fff!important;font-size:15px;font-weight:600;padding:12px 24px;text-decoration:none;border-radius:8px;">{!! $viewStatementLabel !!}</a></p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0;">{!! $closingText !!}</p>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp – Venue lead marketplace</p></td></tr>
</table>
</td></tr>
</table>
