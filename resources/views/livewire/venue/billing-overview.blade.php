<div class="space-y-6">
    @if(!$venue)
        <p class="text-gray-500 dark:text-gray-400">No venue found.</p>
    @else
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Credit balance</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($venue->credit_balance, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Spent this month</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($spentThisMonth, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Leads purchased this month</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $purchasesThisMonth }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Auto top-up</p>
            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $venue->auto_topup_enabled ? 'On' : 'Off' }}
                @if($venue->auto_topup_enabled)
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(below ${{ number_format($venue->auto_topup_threshold, 0) }} → ${{ number_format($venue->auto_topup_amount, 0) }})</span>
                @endif
            </p>
        </div>
    </div>
    @endif
</div>
