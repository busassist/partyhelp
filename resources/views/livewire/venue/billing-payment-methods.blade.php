<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Card on file</h3>
            @if($hasCard)
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">A payment method is saved. You can add a new card to replace it.</p>
            @else
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No card on file. Add a card to enable one-off purchases and auto top-up.</p>
            @endif
            <form action="{{ route('venue.billing.create-setup-session') }}" method="GET" class="mt-4 inline-block">
                <button
                    type="submit"
                    class="inline-flex rounded-lg bg-[#7c3aed] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#6d28d9] focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                >
                    {{ $hasCard ? 'Add another card' : 'Add credit card' }}
                </button>
            </form>
        </div>

        <form wire:submit="updateAutoTopup" class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Auto top-up</h3>
            <label class="flex cursor-pointer items-center gap-3">
                <input
                    type="checkbox"
                    wire:model.live="autoTopupEnabled"
                    class="h-4 w-4 rounded border-gray-300 text-[#7c3aed] focus:ring-[#7c3aed] dark:border-gray-600 dark:bg-gray-700"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">Autocharge for top up</span>
            </label>
            <div class="space-y-2">
                <label for="auto_topup_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auto topup amount</label>
                <select
                    id="auto_topup_amount"
                    wire:model="autoTopupAmount"
                    class="block w-full max-w-xs rounded-lg border-gray-300 px-3 py-2.5 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                @foreach($amounts as $amt)
                    <option value="{{ $amt }}">${{ $amt }}</option>
                @endforeach
            </select>
        </div>
        <button
            type="submit"
            class="rounded-lg bg-[#7c3aed] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#6d28d9] focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:ring-offset-2 dark:focus:ring-offset-gray-900"
        >
            Save auto top-up settings
            </button>
        </form>
    </div>

    @if($venue && $venue->auto_topup_enabled)
        <div class="pt-2">
            <button
                type="button"
                wire:click="disableAutoTopup"
                wire:confirm="Disable auto top-up?"
                class="rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
            >
                Disable auto top-up
            </button>
        </div>
    @endif
</div>
