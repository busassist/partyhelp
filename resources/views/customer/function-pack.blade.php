<x-layouts.customer title="Function Pack - {{ $venue->business_name }}">
    <div class="bg-gray-800 rounded-lg p-8 shadow-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-ph-purple-400 mb-2">
                {{ $venue->business_name }}
            </h1>
            <p class="text-gray-400">Function Pack</p>
        </div>

        <div class="bg-gray-700 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-3">{{ $pack->title }}</h2>
            <div class="flex items-center justify-between">
                <div class="text-gray-400">
                    <span class="uppercase text-xs">{{ pathinfo($pack->file_name, PATHINFO_EXTENSION) }}</span>
                    &middot;
                    {{ number_format($pack->file_size / 1024, 0) }} KB
                </div>
                <a href="{{ route('function-pack.download', $pack->download_token) }}"
                   class="primary-cta-button inline-flex items-center px-6 py-3 bg-ph-purple-600 hover:bg-ph-purple-700 text-white font-semibold rounded-lg transition-colors">
                    Download
                </a>
            </div>
        </div>

        @if($venue->contact_phone || $venue->contact_email)
            <div class="text-center text-gray-400 text-sm">
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
