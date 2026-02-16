<x-filament-panels::page>
    {{ $this->content }}

    @if ($showWhatsappWarningModal)
        <div
            x-data="{ open: @entangle('showWhatsappWarningModal') }"
            x-show="open"
            x-cloak
            x-bind:class="{ 'fi-modal-open': open }"
            class="fi-modal fi-absolute-positioning-context"
            role="dialog"
            aria-modal="true"
        >
            <div class="fi-modal-close-overlay" aria-hidden="true"></div>
            <div class="fi-modal-window-ctn">
                <div class="fi-modal-window fi-width-sm fi-align-start">
                    <div class="fi-modal-header">
                        <h2 class="fi-modal-heading">WhatsApp template update</h2>
                        <p class="fi-modal-description">
                            Updating WhatsApp message details may require re-approval by Twilio prior to the update taking effect. Please check on Twilio in case of manual submission of new template being required.
                        </p>
                    </div>
                    <div class="fi-modal-footer fi-align-end">
                        <div class="fi-modal-footer-actions">
                            <x-filament::button color="gray" wire:click="closeWhatsappWarningModal">
                                Cancel
                            </x-filament::button>
                            <x-filament::button wire:click="confirmSaveWithWhatsappWarning">
                                Save anyway
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
