<div class="space-y-8">
    <p class="text-sm text-gray-600 dark:text-gray-400 max-w-2xl">Choose an amount to buy credits. You will be taken to Stripe Checkout to complete payment securely.</p>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($amounts as $amount)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ $amount }}</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">credits</p>
                <button
                    type="button"
                    wire:click="buyCredits({{ $amount }})"
                    class="mt-6 w-full rounded-lg bg-[#7c3aed] px-4 py-3 text-sm font-medium text-white shadow-sm hover:bg-[#6d28d9] focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                >
                    Buy ${{ $amount }}
                </button>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800/50">
        <label class="flex cursor-pointer items-start gap-4">
            <input
                type="checkbox"
                wire:model.live="saveForAutoTopup"
                class="mt-1 h-4 w-4 rounded border-gray-300 text-[#7c3aed] focus:ring-[#7c3aed] dark:border-gray-600 dark:bg-gray-700"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                <strong class="font-medium text-gray-900 dark:text-white">Auto-charge my card in future.</strong>
                When my balance drops below the threshold, automatically top up using the card I use for this purchase.
                You can change or disable this later under Payment Methods.
            </span>
        </label>
    </div>
</div>
