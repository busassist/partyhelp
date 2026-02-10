@if($showBanner)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-transition
        class="mb-6 flex items-center justify-between gap-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-800 dark:bg-green-950/40"
        role="alert"
    >
        <p class="text-sm font-medium text-green-800 dark:text-green-200">
            @if($isSetup)
                Card added successfully. Your payment method has been saved.
            @else
                Payment successful. Your credits have been added.
            @endif
        </p>
        <button
            type="button"
            wire:click="dismiss"
            @click="visible = false; if (window.history.replaceState) { const u = new URL(window.location.href); u.searchParams.delete('success'); u.searchParams.delete('setup'); u.searchParams.delete('session_id'); window.history.replaceState({}, '', u.toString()); }"
            class="shrink-0 rounded-lg p-1 text-green-600 hover:bg-green-200/50 dark:text-green-300 dark:hover:bg-green-800/50"
            aria-label="Dismiss"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
