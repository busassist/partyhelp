{{-- Form Confirmation Email - Inline styles for email client compatibility --}}
{{-- Design reference: docs/email-sample-template.html --}}
<style>.email-rich-content ul{margin:0;padding-left:20px;}.email-rich-content li{margin-bottom:8px;}.email-rich-content li:last-child{margin-bottom:0;}</style>
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $config = config('partyhelp.form_confirmation_email', []);
    $supportEmail = $config['support_email'] ?? 'venues@partyhelp.com.au';
    $signOffName = $config['sign_off_name'] ?? 'Johnny';
    $signOffTitle = $config['sign_off_title'] ?? 'Manager, Party Venues';
    $businessAddress = $config['business_address'] ?? 'Party Help, 195 Little Collins Street, Melbourne Victoria 3000, Australia';
    $headerText = $header_text ?? 'Your tailored list of party venues is on the way!';
    $thankYouIntro = $thank_you_intro ?? 'Thank you for leaving your party details with our website. We will email you a tailored list of venues soon.';
    $whatHappensNowText = $what_happens_now_text ?? '<ul><li>One of our customer service team members is analysing your party requirements and matching them to the best party venues for you</li><li>You will then receive an email with the tailored list of party venues</li></ul>';
    $whatToDoText = $what_to_do_text ?? '<ul><li>Go through the list of venues and their function packages and create a short list</li><li>Speak with the functions managers that we introduce you to in our email and identify the venues that you want to visit</li><li>Visit with the venues and book your perfect party venue!</li></ul>';
    $contactIntro = $contact_intro ?? 'If you require any information, please contact me at';
    $psMessage = $ps_message ?? $config['ps_message'] ?? "Don't forget to let us know which venue you have booked so we can put you in the draw to win a $159 Gold Class experience. And if you book a Partyhelp venue, you will also receive a $50 drink card!";
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
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">{!! $headerText !!}</p>
</td></tr>
{{-- Intro --}}
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#f1f5f9;line-height:1.6;margin:0 0 12px;">Hi {{ $customerName }},</p>
@php
    $thankYouHtml = str_replace(
        '{{websiteUrl}}',
        '<a href="'.e($websiteUrl).'" style="color:#a855f7;text-decoration:none;">'.e($websiteUrl).'</a>',
        $thankYouIntro
    );
@endphp
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $thankYouHtml !!}</p>
</td></tr>
{{-- What happens now --}}
<tr><td style="padding:0 24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;">
<tr><td style="padding:20px 24px;">
<p style="font-size:14px;font-weight:700;color:#a855f7;margin:0 0 12px;text-transform:uppercase;letter-spacing:0.05em;">What happens now?</p>
<div class="email-rich-content" style="font-size:14px;color:#cbd5e1;line-height:1.8;margin:0;">{!! $whatHappensNowText !!}</div>
</td></tr>
</table>
</td></tr>
{{-- What do I need to do --}}
<tr><td style="padding:0 24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;">
<tr><td style="padding:20px 24px;">
<p style="font-size:14px;font-weight:700;color:#a855f7;margin:0 0 12px;text-transform:uppercase;letter-spacing:0.05em;">What do I need to do?</p>
<div class="email-rich-content" style="font-size:14px;color:#cbd5e1;line-height:1.8;margin:0;">{!! $whatToDoText !!}</div>
</td></tr>
</table>
</td></tr>
{{-- Closing --}}
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $contactIntro !!} <a href="mailto:{{ $supportEmail }}" style="color:#a855f7;text-decoration:none;">{{ $supportEmail }}</a>.</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;">Regards,</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;"><strong>{{ $signOffName }}</strong></p>
<p style="font-size:14px;color:#94a3b8;margin:0 0 24px;">{{ $signOffTitle }}</p>
<p style="font-size:14px;color:#cbd5e1;line-height:1.6;margin:0;font-style:italic;">PS: {!! $psMessage !!}</p>
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
