<x-layouts.customer title="Venue Rejected">
    <div class="bg-gray-800 rounded-lg p-8 shadow-lg text-center">
        <div class="text-6xl mb-4">❌</div>
        <h1 class="text-2xl font-bold text-red-400 mb-4">
            Venue Rejected
        </h1>
        <p class="text-gray-400">
            <strong>{{ $venue->business_name }}</strong> registration has been rejected.
        </p>
    </div>
</x-layouts.customer>
