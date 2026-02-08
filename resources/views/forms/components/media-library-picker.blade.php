@php
    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $maxFiles = $getMaxFiles();
    $autoSave = $getAutoSave();
    $modalId = 'media-library-modal-' . md5($id . $statePath);
    $mediaDisk = config('filesystems.media_disk', 'spaces');
    $mediaBaseUrl = $mediaDisk === 'public'
        ? rtrim(config('app.url'), '/') . '/storage'
        : rtrim(config('app.url'), '/') . '/media';
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    label-tag="div"
>
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            maxFiles: @js($maxFiles),
            mediaBaseUrl: @js($mediaBaseUrl),
            autoSave: @js($autoSave),
            getImageSrc(path) {
                if (!path) return '';
                return this.mediaBaseUrl ? (this.mediaBaseUrl + '/' + path) : ('/storage/' + path);
            },
            triggerAutoSave() {
                if (this.autoSave && typeof $wire.save === 'function') {
                    setTimeout(() => $wire.save(false, true), 200);
                }
            },
            addImage(path) {
                if (!Array.isArray(this.state)) this.state = [];
                if (this.state.length >= this.maxFiles) return;
                if (this.state.includes(path)) return;
                this.state = [...this.state, path];
                this.triggerAutoSave();
            },
            removeImage(path) {
                this.state = this.state.filter(p => p !== path);
                this.triggerAutoSave();
            }
        }"
        @media-selected.window="addImage($event.detail.path); (function(){var e=document.getElementById('{{ $modalId }}');if(e)e.style.display='none'})()"
        @close-media-modal.window="(function(){var e=document.getElementById('{{ $modalId }}');if(e)e.style.display='none'})()"
        {{ $getExtraAlpineAttributeBag() }}
        {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['space-y-3']) }}
    >
        @if(!$isDisabled)
        <style>
            .media-picker-image-grid { display: grid !important; grid-template-columns: repeat(2, 200px) !important; gap: 8px !important; overflow: visible !important; }
            .media-picker-image-wrapper { overflow: visible !important; position: relative !important; }
            .media-picker-remove-btn { position: absolute !important; top: 6px !important; right: 6px !important; width: 19px !important; height: 19px !important; padding: 2px !important; border-radius: 9999px !important; background-color: #ef4444 !important; color: white !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; z-index: 100 !important; opacity: 0 !important; pointer-events: none !important; transition: opacity 0.15s ease !important; }
            .media-picker-image-wrapper:hover .media-picker-remove-btn { opacity: 1 !important; pointer-events: auto !important; }
        </style>
            <div class="flex items-center gap-3 shrink-0" style="margin-bottom: 20px;">
                <button
                    type="button"
                    data-media-modal-open="{{ $modalId }}"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-color-primary fi-btn-size-md fi-btn-outlined"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Choose / Upload images</span>
                </button>
            </div>

        <div class="media-picker-image-grid" style="margin-top: 8px;" x-show="state?.length" x-cloak>
            <template x-for="path in (state || [])" :key="path">
                <div class="relative media-picker-image-wrapper" style="width: 200px;">
                    <img
                        :src="getImageSrc(path)"
                        alt=""
                        style="width: 200px; height: 150px; object-fit: cover; border-radius: 9px; border: 1px solid #e5e7eb; display: block;"
                    />
                    @if(!$isDisabled)
                        <button
                            type="button"
                            x-on:click="removeImage(path)"
                            class="media-picker-remove-btn"
                            aria-label="Remove image"
                        >
                            <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </template>
        </div>

            <div
                id="{{ $modalId }}"
                style="display: none; position: fixed; inset: 0; z-index: 2147483647; isolation: isolate; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; background: rgba(0,0,0,0.5);"
                role="dialog"
                aria-modal="true"
                aria-label="Media library"
                tabindex="-1"
            >
                <div
                    style="position: absolute; inset: 0; cursor: pointer;"
                    aria-label="Close"
                    data-media-modal-backdrop="{{ $modalId }}"
                ></div>
                <div style="position: relative; z-index: 1; width: 100%; max-width: 1000px; min-width: 20rem; height: 85vh; overflow: hidden; border-radius: 0.75rem; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #e5e7eb; pointer-events: auto; display: flex; flex-direction: column;">
                    @livewire(\App\Livewire\MediaLibraryModal::class, [
                        'venueId' => $getVenueId(),
                        'isAdmin' => $getIsAdmin(),
                    ])
                </div>
            </div>
        @endif
    </div>
</x-dynamic-component>

@if(!$isDisabled)
    @push('styles')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:300,400,500,600,700" rel="stylesheet">
    <style>
    .media-modal-scroll::-webkit-scrollbar { width: 6px; }
    .media-modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .media-modal-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .media-modal-card-overlay { opacity: 0 !important; transition: opacity 0.2s !important; }
    .media-modal-card-image-wrap:hover .media-modal-card-overlay { opacity: 1 !important; }
    .media-modal-card-image-wrap:hover .card-img { transform: scale(1.05); }
    .media-modal-card-btn-select:hover { opacity: 0.95; }
    .media-modal-card-btn-delete:hover { opacity: 0.95; }
    .media-modal-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 2rem; }
    @keyframes media-upload-spin { to { transform: rotate(360deg); } }
    </style>
    @endpush
    @push('scripts')
    <script>
        (function() {
            document.addEventListener('click', function(e) {
                var openBtn = e.target.closest('[data-media-modal-open]');
                if (openBtn) {
                    var id = openBtn.getAttribute('data-media-modal-open');
                    var modal = document.getElementById(id);
                    if (modal) {
                        if (modal.parentNode !== document.body) {
                            document.body.appendChild(modal);
                        }
                        modal.style.display = 'flex';
                        modal.focus();
                        window.dispatchEvent(new CustomEvent('media-modal-reset'));
                    }
                    return;
                }
                var backdrop = e.target.closest('[data-media-modal-backdrop]');
                if (backdrop) {
                    var id = backdrop.getAttribute('data-media-modal-backdrop');
                    var modal = document.getElementById(id);
                    if (modal) modal.style.display = 'none';
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('[id^="media-library-modal-"]').forEach(function(m) {
                        if (m.style.display === 'flex') m.style.display = 'none';
                    });
                }
            });

            document.addEventListener('media-selected', function() {
                document.querySelectorAll('[id^="media-library-modal-"]').forEach(function(m) {
                    m.style.display = 'none';
                });
            });

            document.addEventListener('close-media-modal', function() {
                document.querySelectorAll('[id^="media-library-modal-"]').forEach(function(m) {
                    m.style.display = 'none';
                });
            });

        })();
    </script>
    @endpush
@endif
