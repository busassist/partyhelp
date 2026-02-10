<div class="space-y-6">
    <p class="fi-section-header-description">Choose an amount to buy credits. You will be taken to Stripe Checkout to complete payment securely.</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($amounts as $amount)
            <div class="fi-section rounded-xl bg-white p-6 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="fi-section-header-heading text-2xl">${{ $amount }}</p>
                <p class="mt-1 fi-section-header-description">credits</p>
                <button
                    type="button"
                    wire:click="buyCredits({{ $amount }})"
                    class="fi-btn fi-color-primary mt-4 w-full rounded-lg px-4 py-2 text-sm font-medium text-white"
                >
                    Buy ${{ $amount }}
                </button>
            </div>
        @endforeach
    </div>

    <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <label class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                wire:model.live="saveForAutoTopup"
                class="fi-checkbox-input mt-1 rounded"
            />
            <span class="fi-section-header-description">
                <strong>Auto-charge my card in future.</strong> When my balance drops below the threshold, automatically top up using the card I use for this purchase. You can change or disable this later under Payment Methods.
            </span>
        </label>
    </div>
</div>
