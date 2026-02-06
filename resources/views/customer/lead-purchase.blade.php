<x-layouts.customer title="Lead Opportunity">
    <div class="bg-gray-800 rounded-lg p-8 shadow-lg">
        <h1 class="text-2xl font-bold text-ph-purple-400 mb-6">
            Lead Opportunity
        </h1>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-700 rounded p-4">
                <p class="text-gray-400 text-sm">Occasion</p>
                <p class="font-semibold">{{ $lead->occasion_type }}</p>
            </div>
            <div class="bg-gray-700 rounded p-4">
                <p class="text-gray-400 text-sm">Guests</p>
                <p class="font-semibold">{{ $lead->guest_count }}</p>
            </div>
            <div class="bg-gray-700 rounded p-4">
                <p class="text-gray-400 text-sm">Date</p>
                <p class="font-semibold">{{ $lead->preferred_date->format('d M Y') }}</p>
            </div>
            <div class="bg-gray-700 rounded p-4">
                <p class="text-gray-400 text-sm">Location</p>
                <p class="font-semibold">{{ $lead->suburb }}</p>
            </div>
        </div>

        <div class="text-center bg-gray-700 rounded-lg p-6">
            <p class="text-gray-400 mb-2">Lead Price</p>
            <p class="text-4xl font-bold text-ph-purple-400">${{ number_format($lead->current_price, 2) }}</p>
            @if($lead->discount_percent > 0)
                <p class="text-green-400 text-sm mt-1">
                    {{ $lead->discount_percent }}% discount applied
                </p>
            @endif
            <p class="text-gray-500 text-sm mt-4">
                Log in to your venue portal to purchase this lead.
            </p>
            <a href="/venue" class="primary-cta-button mt-4 inline-flex items-center px-6 py-3 bg-ph-purple-600 hover:bg-ph-purple-700 text-white font-semibold rounded-lg transition-colors">
                Go to Venue Portal
            </a>
        </div>
    </div>
</x-layouts.customer>
