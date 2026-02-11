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

    @if(config('services.stripe.mode') !== 'live')
        <div class="rounded-xl border-2 border-amber-400 bg-amber-50 p-5 dark:border-amber-500 dark:bg-amber-950/30">
            <p class="font-semibold text-amber-800 dark:text-amber-200">Warning: STRIPE Test Mode is enabled</p>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Use these details for test transactions:</p>
            <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <li><strong class="text-gray-700 dark:text-gray-300">VISA</strong> — Card: 4242 4242 4242 4242 · Expiry: any future date · CVC: any number</li>
                <li><strong class="text-gray-700 dark:text-gray-300">MASTERCARD</strong> — Card: 5555 5555 5555 4444 · Expiry: any future date · CVC: any number</li>
            </ul>
        </div>
    @endif
</div>
