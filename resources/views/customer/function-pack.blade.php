<x-layouts.customer title="Function Pack - {{ $venue->business_name }}">
    <div class="content-card">
        <div class="text-center mb-8">
            <h1 class="page-title-lg">
                {{ $venue->business_name }}
            </h1>
            <p class="muted-text">Function Pack</p>
        </div>

        <div class="detail-card-panel mb-6">
            <h2 class="text-xl font-semibold mb-3">{{ $pack->title }}</h2>
            <div class="flex items-center justify-between">
                <div class="muted-text">
                    <span class="uppercase text-xs">{{ pathinfo($pack->file_name, PATHINFO_EXTENSION) }}</span>
                    &middot;
                    {{ number_format($pack->file_size / 1024, 0) }} KB
                </div>
                <a href="{{ route('function-pack.download', $pack->download_token) }}" class="primary-cta-button">
                    Download
                </a>
            </div>
        </div>

        @if($venue->contact_phone || $venue->contact_email)
            <div class="text-center muted-text-sm">
                <p>Questions? Contact {{ $venue->business_name }}:</p>
                @if($venue->contact_phone)
                    <p>{{ $venue->contact_phone }}</p>
                @endif
                @if($venue->contact_email)
                    <p>{{ $venue->contact_email }}</p>
                @endif
            </div>
        @endif
    </div>
</x-layouts.customer>
