<div class="space-y-6">
    <div class="fi-section rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="fi-section-header-heading">Card on file</h3>
        @if($hasCard)
            <p class="mt-1 fi-section-header-description">A payment method is saved.</p>
        @else
            <p class="mt-1 fi-section-header-description">No card on file.</p>
        @endif
        <a href="{{ route('venue.billing.create-setup-session') }}" class="fi-btn fi-color-primary mt-3 inline-flex rounded-lg px-4 py-2 text-sm font-medium text-white">{{ $hasCard ? 'Add another card' : 'Add credit card' }}</a>
    </div>
    <form wire:submit="updateAutoTopup" class="fi-section space-y-4 rounded-xl bg-white p-4 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="fi-section-header-heading">Auto top-up</h3>
        <label class="flex cursor-pointer items-center gap-2">
            <input type="checkbox" wire:model.live="autoTopupEnabled" class="fi-checkbox-input rounded" />
            <span class="fi-section-header-description">Autocharge for top up</span>
        </label>
        <div>
            <label for="auto_topup_amount" class="fi-section-header-description block font-medium">Auto topup amount</label>
            <select id="auto_topup_amount" wire:model="autoTopupAmount" class="fi-input mt-1 rounded-lg">
                @foreach($amounts as $amt)
                    <option value="{{ $amt }}">${{ $amt }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="fi-btn fi-color-primary rounded-lg px-4 py-2 text-sm font-medium text-white">Save auto top-up settings</button>
    </form>
    @if($venue && $venue->auto_topup_enabled)
        <button type="button" wire:click="disableAutoTopup" wire:confirm="Disable auto top-up?" class="fi-btn fi-outlined fi-color-danger rounded-lg px-4 py-2 text-sm font-medium">Disable auto top-up</button>
    @endif
</div>
