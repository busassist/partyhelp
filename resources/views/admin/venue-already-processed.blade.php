<x-layouts.customer title="Already Processed">
    <div class="bg-gray-800 rounded-lg p-8 shadow-lg text-center">
        <div class="text-6xl mb-4">ℹ️</div>
        <h1 class="text-2xl font-bold text-ph-purple-400 mb-4">
            Already Processed
        </h1>
        <p class="text-gray-400">
            <strong>{{ $venue->business_name }}</strong> has already been processed.
            Current status: <strong>{{ ucfirst($venue->status) }}</strong>
        </p>
    </div>
</x-layouts.customer>
