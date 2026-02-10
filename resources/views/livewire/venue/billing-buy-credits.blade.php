<div class="space-y-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">Choose an amount to buy credits. You will be taken to Stripe Checkout to complete payment securely.</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($amounts as $amount)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ $amount }}</p>
                <p class="mt-1 text-sm text-gray-500">credits</p>
                <button
                    type="button"
                    wire:click="buyCredits({{ $amount }})"
                    class="mt-4 w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-600"
                >
                    Buy ${{ $amount }}
                </button>
            </div>
        @endforeach
    </div>

    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <label class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                wire:model.live="saveForAutoTopup"
                class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">
                <strong>Auto-charge my card in future.</strong> When my balance drops below the threshold, automatically top up using the card I use for this purchase. You can change or disable this later under Payment Methods.
            </span>
        </label>
    </div>
</div>
