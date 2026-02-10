<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Card on file</h3>
        @if($hasCard)
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">A payment method is saved.</p>
        @else
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">No card on file.</p>
        @endif
        <a href="{{ route('venue.billing.create-setup-session') }}" class="mt-3 inline-flex rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">{{ $hasCard ? 'Add another card' : 'Add credit card' }}</a>
    </div>
    <form wire:submit="updateAutoTopup" class="space-y-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Auto top-up</h3>
        <label class="flex cursor-pointer items-center gap-2">
            <input type="checkbox" wire:model.live="autoTopupEnabled" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
            <span class="text-sm text-gray-700 dark:text-gray-300">Autocharge for top up</span>
        </label>
        <div>
            <label for="auto_topup_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auto topup amount</label>
            <select id="auto_topup_amount" wire:model="autoTopupAmount" class="mt-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                @foreach($amounts as $amt)
                    <option value="{{ $amt }}">${{ $amt }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Save auto top-up settings</button>
    </form>
    @if($venue && $venue->auto_topup_enabled)
        <button type="button" wire:click="disableAutoTopup" wire:confirm="Disable auto top-up?" class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400">Disable auto top-up</button>
    @endif
</div>
