{{-- No/Few Responses Prompt – +8h customer email. Inline styles for email clients. --}}
<style>.email-rich-content ul{margin:0;padding-left:20px;}.email-rich-content li{margin-bottom:8px;}</style>
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $config = config('partyhelp.form_confirmation_email', []);
    $supportEmail = $config['support_email'] ?? 'venues@partyhelp.com.au';
    $signOffName = $config['sign_off_name'] ?? 'Johnny';
    $signOffTitle = $config['sign_off_title'] ?? 'Manager, Party Venues';
    $businessAddress = $config['business_address'] ?? 'Party Help, 195 Little Collins Street, Melbourne Victoria 3000, Australia';
    $headerText = $header_text ?? "Still looking for your perfect venue? We're on it";
    $introText = $intro_text ?? "We're still matching your enquiry to the best venues.";
    $reassuranceText = $reassurance_text ?? "If you haven't heard from many venues yet, don't worry – we've sent your details to venues that match your requirements.";
    $ctaText = $cta_text ?? 'Need help or more options? Reply to this email or contact us.';
    $closingText = $closing_text ?? 'We look forward to helping you find your perfect party venue.';
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
@if($viewInBrowserUrl ?? null)
<tr><td align="center" style="padding-bottom:16px;"><a href="{{ $viewInBrowserUrl }}" style="font-size:12px;color:#94a3b8;text-decoration:underline;">Can't read this email? View in the browser</a></td></tr>
@endif
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
<p style="font-size:18px;font-weight:600;color:#fff;margin-top:16px;text-align:center;">{!! $headerText !!}</p>
</td></tr>
<tr><td style="padding:0 24px 24px;">
<p style="font-size:15px;color:#f1f5f9;line-height:1.6;margin:0 0 12px;">Hi {{ $customerName }},</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 12px;">{!! $introText !!}</p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $reassuranceText !!}</p>
<p style="font-size:15px;color:#a855f7;line-height:1.6;margin:0 0 24px;">{!! $ctaText !!} <a href="mailto:{{ $supportEmail }}" style="color:#a855f7;text-decoration:none;">{{ $supportEmail }}</a></p>
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">{!! $closingText !!}</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;">Regards,</p>
<p style="font-size:15px;color:#f1f5f9;margin:0 0 4px;"><strong>{{ $signOffName }}</strong></p>
<p style="font-size:14px;color:#94a3b8;margin:0 0 24px;">{{ $signOffTitle }}</p>
<p style="font-size:12px;color:#64748b;margin-top:16px;">{{ $businessAddress }}</p>
</td></tr>
@if($unsubscribeUrl ?? null)
<tr><td style="background:#160b2e;padding:24px;border-top:1px solid rgba(255,255,255,0.05);"><p style="margin:0;text-align:center;"><a href="{{ $unsubscribeUrl }}" style="font-size:12px;color:#a855f7;text-decoration:underline;">Unsubscribe</a></p></td></tr>
@endif
</table>
</td></tr>
</table>
