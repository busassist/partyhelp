{{-- Venue Introduction Email - Inline styles for email client compatibility --}}
{{-- Design reference: docs/email-sample-template.html --}}
{{-- Desktop: 2 room photos per row | Mobile: 1 per row --}}
<style>
@media only screen and (max-width: 600px) {
  .room-card-cell { display: block !important; width: 100% !important; max-width: 100% !important; }
}
</style>
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $headerText = $header_text ?? 'Some venue recommendations tailored for you...';
    $thankYouText = $thank_you_text ?? 'Thank you for your party enquiry.';
    $locationIntro = $location_intro ?? 'We can see from your enquiry that you expressed interest in venues in the following location:';
    $recommendationsIntro = $recommendations_intro ?? 'Please find our best venue recommendations for your party in this location. Please feel free to contact these venues directly.';
    $closingText = $closing_text ?? 'Please feel free to contact me if you would like some more venue options or further advice. Or, if you want to make a booking, we can also help you finalise the arrangements for your party.';
    $matchingRoomsLabel = $matching_rooms_label ?? 'Matching rooms for your party size:';
    $supportEmail = config('partyhelp.venue_intro_email.support_email', 'venues@partyhelp.com.au');
    $signOffName = config('partyhelp.venue_intro_email.sign_off_name', 'Johnny');
    $signOffTitle = config('partyhelp.venue_intro_email.sign_off_title', 'Manager, Party Venues');
    $businessAddress = config('partyhelp.venue_intro_email.business_address', 'Business Assist, 195 Little Collins Street, Melbourne VIC 3000, Australia');
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
{{-- View in browser --}}
@if($viewInBrowserUrl ?? null)
<tr><td align="center" style="padding-bottom:16px;"><a href="{{ $viewInBrowserUrl }}" style="font-size:12px;color:#94a3b8;text-decoration:underline;">Can't read this email? View in the browser</a></td></tr>
@endif
{{-- Header --}}
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td align="center">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
</td></tr>
</table>
<p style="font-size:16px;color:#cbd5e1;margin-top:16px;text-align:center;">{!! $headerText !!}</p>
</td></tr>
{{-- Intro --}}
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#f1f5f9;line-height:1.6;margin:0 0 12px;">Hi {{ $customerName }},</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 12px;">{!! $thankYouText !!}</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 12px;">{!! $locationIntro !!} <strong style="color:#a855f7;">{{ $location }}</strong>.</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $recommendationsIntro !!}</p>
</td></tr>
{{-- Venues --}}
@foreach($venues ?? [] as $venue)
<tr><td style="padding:0 24px 32px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:24px;">
<tr><td style="padding:24px;">
<h2 style="font-size:20px;font-weight:700;color:#fff;margin:0 0 4px;">{{ $venue['venue_name'] ?? 'Venue' }}@if(!empty($venue['venue_area'])) - {{ $venue['venue_area'] }}@endif</h2>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:16px;">
<tr><td style="font-size:14px;color:#cbd5e1;line-height:1.8;padding:4px 0;">Contact: {{ $venue['contact_name'] ?? 'Functions Coordinator' }} - {{ $venue['contact_phone'] ?? '' }}</td></tr>
<tr><td style="font-size:14px;color:#cbd5e1;line-height:1.8;padding:4px 0;">Email: <a href="mailto:{{ $venue['email'] ?? '' }}" style="color:#a855f7;text-decoration:none;">{{ $venue['email'] ?? '' }}</a></td></tr>
<tr><td style="font-size:14px;color:#cbd5e1;line-height:1.8;padding:4px 0;">Website: <a href="{{ $venue['website'] ?? '#' }}" style="color:#a855f7;text-decoration:none;">{{ $venue['website'] ?? '' }}</a></td></tr>
<tr><td style="font-size:14px;color:#cbd5e1;line-height:1.8;padding:4px 0;">Room Hire: {{ $venue['room_hire'] ?? '—' }}</td></tr>
<tr><td style="font-size:14px;color:#cbd5e1;line-height:1.8;padding:4px 0;">Minimum Spend: {{ $venue['minimum_spend'] ?? '—' }}</td></tr>
</table>
@if(!empty($venue['rooms']))
<p style="font-size:14px;font-weight:600;color:#a855f7;margin:24px 0 12px;">{!! $matchingRoomsLabel !!}</p>
@php $roomChunks = array_chunk($venue['rooms'], 2); @endphp
@foreach($roomChunks as $roomRow)
@php $isSingle = count($roomRow) === 1; @endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
@foreach($roomRow as $room)
<td class="room-card-cell" style="width:{{ $isSingle ? '100%' : '50%' }};vertical-align:top;padding-right:{{ $loop->first && !$isSingle ? '6px' : '0' }};padding-left:{{ $loop->last && !$loop->first ? '6px' : '0' }};padding-bottom:24px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td>
@if(!empty($room['image_url']))
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:12px;"><tr><td style="overflow:hidden;border-radius:12px;">
<img src="{{ $room['image_url'] }}" alt="{{ $room['room_name'] ?? 'Room' }}" width="270" height="203" style="width:100%;height:203px;object-fit:cover;display:block;border-radius:12px;" />
</td></tr></table>
@endif
<p style="font-size:15px;font-weight:600;color:#fff;margin:0 0 6px;">{{ $room['room_name'] ?? 'Room' }}</p>
<p style="font-size:14px;color:#cbd5e1;line-height:1.6;margin:0;">{{ $room['description'] ?? '' }}@php
if (!empty($room['capacity_min']) || !empty($room['capacity_max'])) {
    echo ' · Capacity: ' . ($room['capacity_min'] ?? '?') . '-' . ($room['capacity_max'] ?? '?') . ' guests';
}
@endphp</p>
</td></tr></table>
</td>
@endforeach
</tr></table>
@endforeach
@endif
</td></tr>
</table>
</td></tr>
@endforeach
{{-- Closing --}}
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $closingText !!} You can email me at <a href="mailto:{{ $supportEmail }}" style="color:#a855f7;text-decoration:none;">{{ $supportEmail }}</a>.</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;">Regards,</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;"><strong>{{ $signOffName }}</strong></p>
<p style="font-size:14px;color:#94a3b8;margin:0;">{{ $signOffTitle }}</p>
<p style="font-size:12px;color:#64748b;margin-top:16px;">{{ $businessAddress }}</p>
</td></tr>
{{-- Footer --}}
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);">
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
<tr><td align="center">
@if($unsubscribeUrl ?? null)
<p style="margin:0;"><a href="{{ $unsubscribeUrl }}" style="font-size:12px;color:#a855f7;text-decoration:underline;">Unsubscribe</a></p>
@endif
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
