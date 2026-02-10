<div class="space-y-6">
    @if(!$venue)
        <p class="fi-section-header-description">No venue found.</p>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="fi-section-header-description">Credit balance</p>
            <p class="mt-1 fi-section-header-heading text-2xl">${{ number_format($venue->credit_balance, 2) }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="fi-section-header-description">Spent this month</p>
            <p class="mt-1 fi-section-header-heading text-2xl">${{ number_format($spentThisMonth, 2) }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="fi-section-header-description">Leads purchased this month</p>
            <p class="mt-1 fi-section-header-heading text-2xl">{{ $purchasesThisMonth }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="fi-section-header-description">Auto top-up</p>
            <p class="mt-1 fi-section-header-heading text-lg">
                {{ $venue->auto_topup_enabled ? 'On' : 'Off' }}
                @if($venue->auto_topup_enabled)
                    <span class="fi-section-header-description text-sm">(below ${{ number_format($venue->auto_topup_threshold, 0) }} → ${{ number_format($venue->auto_topup_amount, 0) }})</span>
                @endif
            </p>
        </div>
    </div>
    @endif
</div>
