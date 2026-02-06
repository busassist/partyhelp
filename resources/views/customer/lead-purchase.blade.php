<x-layouts.customer title="Lead Opportunity">
    <div class="content-card">
        <h1 class="page-title">
            Lead Opportunity
        </h1>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="detail-card">
                <p class="card-label">Occasion</p>
                <p class="card-value">{{ $lead->occasion_type }}</p>
            </div>
            <div class="detail-card">
                <p class="card-label">Guests</p>
                <p class="card-value">{{ $lead->guest_count }}</p>
            </div>
            <div class="detail-card">
                <p class="card-label">Date</p>
                <p class="card-value">{{ $lead->preferred_date->format('d M Y') }}</p>
            </div>
            <div class="detail-card">
                <p class="card-label">Location</p>
                <p class="card-value">{{ $lead->suburb }}</p>
            </div>
        </div>

        <div class="detail-card-highlight">
            <p class="card-label mb-2">Lead Price</p>
            <p class="text-4xl font-bold text-ph-purple-400">${{ number_format($lead->current_price, 2) }}</p>
            @if($lead->discount_percent > 0)
                <p class="text-green-400 text-sm mt-1">
                    {{ $lead->discount_percent }}% discount applied
                </p>
            @endif
            <p class="muted-text-sm mt-4">
                Log in to your venue portal to purchase this lead.
            </p>
            <a href="/venue" class="primary-cta-button mt-4">
                Go to Venue Portal
            </a>
        </div>
    </div>
</x-layouts.customer>
