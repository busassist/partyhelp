{{-- Shortlist Check – +36h customer email. Inline styles for email clients. --}}
<style>.email-rich-content ul{margin:0;padding-left:20px;}.email-rich-content li{margin-bottom:8px;}</style>
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $config = config('partyhelp.form_confirmation_email', []);
    $supportEmail = $config['support_email'] ?? 'venues@partyhelp.com.au';
    $signOffName = $config['sign_off_name'] ?? 'Johnny';
    $signOffTitle = $config['sign_off_title'] ?? 'Manager, Party Venues';
    $businessAddress = $config['business_address'] ?? 'Party Help, 195 Little Collins Street, Melbourne Victoria 3000, Australia';
    $headerText = $header_text ?? "How's your shortlist going?";
    $introText = $intro_text ?? 'You should have received introductions from some of our partner venues by now.';
    $tipsText = $tips_text ?? '<ul><li>Compare the venues and create a shortlist</li><li>Contact the venues directly to arrange a visit</li><li>Book and let us know for your chance to win a Gold Class experience!</li></ul>';
    $ctaText = $cta_text ?? 'Need more venue options? Get in touch.';
    $closingText = $closing_text ?? 'Happy planning!';
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
<p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 16px;">{!! $introText !!}</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:linear-gradient(145deg,#1e0f3d,#160b2e);border:1px solid rgba(255,255,255,0.05);border-radius:16px;"><tr><td style="padding:20px 24px;">
<p style="font-size:14px;font-weight:700;color:#a855f7;margin:0 0 12px;text-transform:uppercase;letter-spacing:0.05em;">Quick tips</p>
<div class="email-rich-content" style="font-size:14px;color:#cbd5e1;line-height:1.8;margin:0;">{!! $tipsText !!}</div>
</td></tr></table>
<p style="font-size:15px;color:#a855f7;line-height:1.6;margin:16px 0 24px;">{!! $ctaText !!} <a href="mailto:{{ $supportEmail }}" style="color:#a855f7;text-decoration:none;">{{ $supportEmail }}</a></p>
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
