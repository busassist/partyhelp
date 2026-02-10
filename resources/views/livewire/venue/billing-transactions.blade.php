<div class="space-y-4">
    @if($venue)
        <p class="fi-section-header-description">Current balance: <strong class="fi-section-header-heading">${{ number_format($venue->credit_balance, 2) }}</strong></p>
    @endif
    <div class="overflow-x-auto">
        <table class="fi-ta-table">
            <thead>
                <tr class="fi-ta-row">
                    <th class="fi-ta-header-cell">Date</th>
                    <th class="fi-ta-header-cell">Type</th>
                    <th class="fi-ta-header-cell">Description</th>
                    <th class="fi-ta-header-cell fi-align-end">Amount</th>
                    <th class="fi-ta-header-cell fi-align-end">Balance after</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr class="fi-ta-row">
                        <td class="fi-ta-cell whitespace-nowrap">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="fi-ta-cell whitespace-nowrap">{{ $this->typeLabel($tx->type) }}</td>
                        <td class="fi-ta-cell">{{ $tx->description ?? '—' }}</td>
                        <td class="fi-ta-cell fi-align-end whitespace-nowrap {{ $tx->amount >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            {{ $tx->amount >= 0 ? '+' : '' }}${{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="fi-ta-cell fi-align-end whitespace-nowrap">${{ number_format($tx->balance_after, 2) }}</td>
                    </tr>
                @empty
                    <tr class="fi-ta-row">
                        <td colspan="5" class="fi-ta-cell py-8 text-center">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
