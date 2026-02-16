{{-- Additional Services – +72h customer email. Tagline, visual grid of services, CTA to landing page. --}}
@php
    $logoUrl = asset('images/brand/ph-logo-white.png');
    $config = config('partyhelp.form_confirmation_email', []);
    $signOffName = $config['sign_off_name'] ?? 'Johnny';
    $signOffTitle = $config['sign_off_title'] ?? 'Manager, Party Venues';
    $businessAddress = $config['business_address'] ?? 'Party Help, 195 Little Collins Street, Melbourne Victoria 3000, Australia';
    $tagline = $tagline ?? 'Make your {{occasion}} unforgettable with these extras';
    $ctaText = $cta_text ?? 'Choose additional services';
    $closingText = $closing_text ?? "We'll be in touch with recommendations soon.";
    $services = $additionalServices ?? [];
@endphp
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0a031a;font-family:Inter,Arial,sans-serif;color:#f1f5f9;">
<tr><td align="center" style="padding:24px 16px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;">
@if($viewInBrowserUrl ?? null)
<tr><td align="center" style="padding-bottom:16px;"><a href="{{ $viewInBrowserUrl }}" style="font-size:12px;color:#94a3b8;text-decoration:underline;">View in browser</a></td></tr>
@endif
<tr><td align="center" style="padding:24px 16px;">
<img src="{{ $logoUrl }}" alt="Partyhelp" width="160" height="40" style="height:40px;width:auto;display:inline-block;vertical-align:middle;" />
</td></tr>
<tr><td style="padding:0 24px 16px;">
<p style="font-size:15px;color:#f1f5f9;line-height:1.6;margin:0 0 12px;">Hi {{ $customerName }},</p>
<p style="font-size:18px;font-weight:600;color:#fff;line-height:1.4;margin:0 0 24px;">{!! $tagline !!}</p>
</td></tr>
@if(count($services) > 0)
<tr><td style="padding:0 24px 24px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
<tr>
@foreach($services as $svc)
<td align="center" style="padding:8px;vertical-align:top;width:{{ 100 / min(4, count($services)) }}%;">
@if($svc['thumbnail_url'] ?? null)
<img src="{{ $svc['thumbnail_url'] }}" alt="{{ $svc['name'] ?? '' }}" width="80" height="80" style="width:80px;height:80px;object-fit:cover;border-radius:8px;display:block;margin:0 auto 8px;" />
@endif
<span style="font-size:13px;color:#cbd5e1;display:block;">{{ $svc['name'] ?? '' }}</span>
</td>
@if($loop->iteration % 4 === 0 && !$loop->last)
</tr><tr>
@endif
@endforeach
</tr>
</table>
</td></tr>
@endif
<tr><td style="padding:0 24px 24px;" align="center">
<a href="{{ $additionalServicesUrl ?? '#' }}" style="display:inline-block;background:#a855f7;color:#fff;font-size:16px;font-weight:600;padding:14px 28px;text-decoration:none;border-radius:8px;">{{ $ctaText }}</a>
</td></tr>
<tr><td style="padding:0 24px 24px;">
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
