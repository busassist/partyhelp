{{-- New Venue For Approval – Admin, when venue registers. PRD 8.1. Inline styles for email clients. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'New venue pending approval';
    $introText = $intro_text ?? 'A new venue has registered and is awaiting approval. Review the details and approve or reject below.';
    $reviewButtonLabel = $review_button_label ?? 'Review vendor';
    $approveButtonLabel = $approve_button_label ?? 'Approve';
    $rejectButtonLabel = $reject_button_label ?? 'Reject';
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
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $introText !!}</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;"><tr><td style="padding:20px 24px;">
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Venue:</strong> {{ $venueName ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Business:</strong> {{ $businessName ?? '—' }}</p>
<p style="font-size:14px;color:#cbd5e1;margin:0 0 8px;"><strong style="color:#a855f7;">Contact:</strong> {{ $contactName ?? '—' }}</p>
</td></tr></table>
{{-- Table-based buttons: padding and bg on td for Outlook; white text + standard button font --}}
@php
    $btnStyle = 'color:#ffffff !important; font-family:Arial,Helvetica,sans-serif; font-size:15px; font-weight:600; text-decoration:none;';
    $tdPadding = '16px 28px';
@endphp
<table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:24px auto;">
<tr>
<td style="padding:0 8px;">
<table cellpadding="0" cellspacing="0" border="0"><tr><td align="center" bgcolor="#475569" style="background:#475569; padding:{{ $tdPadding }}; border-radius:8px;"><a href="{{ $reviewUrl ?? '#' }}" target="_blank" style="{{ $btnStyle }}">{!! $reviewButtonLabel !!}</a></td></tr></table>
</td>
<td style="padding:0 8px;">
<table cellpadding="0" cellspacing="0" border="0"><tr><td align="center" bgcolor="#16a34a" style="background:#16a34a; padding:{{ $tdPadding }}; border-radius:8px;"><a href="{{ $approveUrl ?? '#' }}" target="_blank" style="{{ $btnStyle }}">{!! $approveButtonLabel !!}</a></td></tr></table>
</td>
<td style="padding:0 8px;">
<table cellpadding="0" cellspacing="0" border="0"><tr><td align="center" bgcolor="#dc2626" style="background:#dc2626; padding:{{ $tdPadding }}; border-radius:8px;"><a href="{{ $rejectUrl ?? '#' }}" target="_blank" style="{{ $btnStyle }}">{!! $rejectButtonLabel !!}</a></td></tr></table>
</td>
</tr>
</table>
</td></tr>
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;font-size:12px;color:#64748b;text-align:center;">Partyhelp Admin</p></td></tr>
</table>
</td></tr>
</table>
