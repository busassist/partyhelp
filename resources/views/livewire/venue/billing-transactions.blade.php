<div class="space-y-4">
    @if($venue)
        <p class="text-sm text-gray-600 dark:text-gray-400">Current balance: <strong class="text-gray-900 dark:text-white">${{ number_format($venue->credit_balance, 2) }}</strong></p>
    @endif
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Balance after</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800/50">
                @forelse($transactions as $tx)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $this->typeLabel($tx->type) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $tx->description ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm {{ $tx->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $tx->amount >= 0 ? '+' : '' }}${{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900 dark:text-white">${{ number_format($tx->balance_after, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
