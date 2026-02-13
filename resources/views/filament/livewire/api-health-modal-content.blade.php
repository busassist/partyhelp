<div class="text-white">
    <div class="flex flex-wrap gap-2 mb-4">
        <button type="button" wire:click="testMailgun" wire:loading.attr="disabled" class="rounded-md bg-gray-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 disabled:opacity-50">
            Test Mailgun
        </button>
        <button type="button" wire:click="testStripe" wire:loading.attr="disabled" class="rounded-md bg-gray-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 disabled:opacity-50">
            Test Stripe
        </button>
    </div>
    @if ($testResult !== null)
        <p class="mb-4 text-sm rounded-lg bg-gray-800 px-3 py-2 text-gray-200" wire:key="test-result">{{ $testResult }}</p>
    @endif
    <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Recent API errors (last 20)</h5>
    @if ($errors->isEmpty())
        <p class="text-sm text-gray-400">No API errors recorded.</p>
    @else
        <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead class="bg-gray-800 sticky top-0">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-white w-24">Service</th>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-white">Message</th>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-white w-32">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-900/50">
                    @foreach ($errors as $entry)
                        <tr>
                            <td class="px-3 py-2 font-medium text-white">{{ $entry->service }}</td>
                            <td class="px-3 py-2 text-gray-200">{{ \Illuminate\Support\Str::limit($entry->message, 120) }}</td>
                            <td class="px-3 py-2 text-gray-400 tabular-nums text-xs">{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
