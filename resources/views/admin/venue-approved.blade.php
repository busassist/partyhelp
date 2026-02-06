<x-layouts.customer title="Venue Approved">
    <div class="bg-gray-800 rounded-lg p-8 shadow-lg text-center">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-green-400 mb-4">
            Venue Approved
        </h1>
        <p class="text-gray-400">
            <strong>{{ $venue->business_name }}</strong> has been approved and can now receive lead opportunities.
        </p>
    </div>
</x-layouts.customer>
