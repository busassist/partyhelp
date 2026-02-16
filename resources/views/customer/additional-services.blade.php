<x-layouts.customer title="Choose additional services">
    <div class="content-card">
        <h1 class="page-title">
            Make your event unforgettable with these extras
        </h1>
        <p class="muted-text mb-6">
            Select the additional services you're interested in. We'll be in touch with recommendations soon.
        </p>

        <form action="{{ $submitUrl }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($services as $svc)
                    <label class="detail-card cursor-pointer hover:border-ph-purple-500/50 flex items-start gap-3 p-4 rounded-lg border border-white/10">
                        <input type="checkbox" name="services[]" value="{{ $svc['id'] }}" class="mt-1 rounded border-gray-600 bg-gray-800 text-ph-purple-500 focus:ring-ph-purple-500">
                        @if($svc['thumbnail_url'] ?? null)
                            <img src="{{ $svc['thumbnail_url'] }}" alt="" class="w-14 h-14 object-cover rounded" />
                        @endif
                        <span class="card-value">{{ $svc['name'] }}</span>
                    </label>
                @endforeach
            </div>
            @if(count($services) === 0)
                <p class="muted-text">No additional services are currently available. Check back later or contact us.</p>
            @else
                <button type="submit" class="primary-cta-button">
                    Submit my selections
                </button>
            @endif
        </form>
    </div>
</x-layouts.customer>
