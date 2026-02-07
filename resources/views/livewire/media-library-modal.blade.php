<div style="display: flex; flex-direction: column; height: 100%; min-height: 0; background: white; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';" x-data="{ tab: 'library' }" x-on:media-modal-reset.window="tab = 'library'">
    <div style="padding: 2rem 2rem 1rem; flex-shrink: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: {{ $this->themeColors['textDark'] }}; margin: 0;">Select Image</h1>
                <p style="font-size: 0.875rem; font-weight: 300; color: {{ $this->themeColors['textMuted'] }}; margin: 0;">Select an asset to add to this room.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" @click="tab = 'upload'" style="display: flex; align-items: center; gap: 0.5rem; height: 2.5rem; padding: 0 1.25rem; background: {{ $this->themeColors['primary'] }}; color: white; font-size: 0.875rem; font-weight: 600; border: none; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.opacity='0.95'" onmouseout="this.style.opacity='1'">
                    <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload New
                </button>
                <button type="button" @click="$dispatch('close-media-modal')" style="padding: 0.25rem; background: transparent; border: none; cursor: pointer; color: {{ $this->themeColors['textMuted'] }}; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.color='{{ $this->themeColors['textDark'] }}'" onmouseout="this.style.color='{{ $this->themeColors['textMuted'] }}'" aria-label="Close">
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-top: 2rem; border-bottom: 1px solid {{ $this->themeColors['borderLight'] }}; gap: 1rem;">
            <div style="display: flex; gap: 2rem;">
                <button type="button" @click="tab = 'library'" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem 0.25rem calc(0.75rem + 5px) 0; border: none; border-bottom: 3px solid transparent; cursor: pointer; background: transparent; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; font-size: 0.875rem; font-weight: 600; transition: color 0.15s, border-color 0.15s;" :style="tab === 'library' ? 'color: {{ $this->themeColors['primary'] }}; border-bottom-color: {{ $this->themeColors['primary'] }};' : 'color: {{ $this->themeColors['textMuted'] }};'" onmouseover="if(tab !== 'library') { $el.style.color='{{ $this->themeColors['primary'] }}' }" onmouseout="if(tab !== 'library') { $el.style.color='{{ $this->themeColors['textMuted'] }}' }">Library</button>
                <button type="button" @click="tab = 'upload'" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem 0.25rem calc(0.75rem + 5px) 0; border: none; border-bottom: 3px solid transparent; cursor: pointer; background: transparent; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; font-size: 0.875rem; font-weight: 600; transition: color 0.15s, border-color 0.15s;" :style="tab === 'upload' ? 'color: {{ $this->themeColors['primary'] }}; border-bottom-color: {{ $this->themeColors['primary'] }};' : 'color: {{ $this->themeColors['textMuted'] }};'" onmouseover="if(tab !== 'upload') { $el.style.color='{{ $this->themeColors['primary'] }}' }" onmouseout="if(tab !== 'upload') { $el.style.color='{{ $this->themeColors['textMuted'] }}' }">Upload</button>
            </div>
            <div x-show="tab === 'library'" x-transition:enter.opacity.duration.150ms style="padding-bottom: 0.75rem; width: 100%; max-width: 320px;">
                <div style="display: flex; align-items: center; height: 2.5rem; padding: 0 0.75rem; background: {{ $this->themeColors['bgInput'] }}; border: 1px solid {{ $this->themeColors['borderMed'] }}; border-radius: 0.5rem; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: {{ $this->themeColors['textMuted'] }}; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Search images by name..." style="flex: 1; border: none; background: transparent; font-size: 0.875rem; color: {{ $this->themeColors['textDark'] }}; outline: none; min-width: 0;" />
                </div>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-height: 0; overflow-y: auto; padding: 2rem; background: white;" class="media-modal-scroll">
        <div x-show="tab === 'library'" x-transition:enter.opacity.duration.150ms>
            <div class="media-modal-grid">
                @forelse($this->mediaItems as $media)
                    @php
                        $sizeFormatted = $media->size < 1024 ? $media->size . ' B' : ($media->size < 1048576 ? round($media->size / 1024, 1) . ' KB' : round($media->size / 1048576, 1) . ' MB');
                        $typeLabel = strtoupper(Str::afterLast($media->mime_type ?? 'image', '/'));
                    @endphp
                    <button type="button" wire:click="selectImage('{{ $media->file_path }}')" class="media-modal-card" style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem; background: white; border: 1px solid {{ $this->themeColors['borderLight'] }}; border-radius: 0.75rem; cursor: pointer; text-align: left; transition: box-shadow 0.2s, border-color 0.2s;" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)'; this.style.borderColor='rgba(124,58,237,0.4)'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='{{ $this->themeColors['borderLight'] }}'" onfocus="this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)'; this.style.borderColor='rgba(124,58,237,0.4)'">
                        <div style="position: relative; width: 100%; aspect-ratio: 4/3; overflow: hidden; border-radius: 0.5rem; background: {{ $this->themeColors['bgInput'] }};">
                            <img src="{{ $media->url }}" alt="{{ $media->file_name }}" class="card-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;" />
                            <div style="position: absolute; inset: 0; background: rgba(124,58,237,0.08); opacity: 0; transition: opacity 0.2s; pointer-events: none;" class="card-hover-overlay"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; min-width: 0;">
                            <p style="font-size: 13px; font-weight: 500; color: {{ $this->themeColors['textDark'] }}; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $media->file_name }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                                <span style="font-size: 11px; font-weight: 400; color: {{ $this->themeColors['textLight'] }}; text-transform: uppercase; letter-spacing: 0.05em;">{{ $sizeFormatted }}</span>
                                <span style="font-size: 11px; font-weight: 400; color: {{ $this->themeColors['textLight'] }}; text-transform: uppercase;">{{ $typeLabel }}</span>
                            </div>
                        </div>
                    </button>
                @empty
                    <p style="grid-column: 1 / -1; color: {{ $this->themeColors['textMuted'] }}; padding: 3rem 0; text-align: center; margin: 0; font-size: 0.875rem;">No images yet. Upload some in the Upload tab.</p>
                @endforelse
            </div>
        </div>
        <div x-show="tab === 'upload'" x-transition:enter.opacity.duration.150ms x-cloak>
            <div x-data="{ dragging: false, drop(e) { this.dragging = false; const files = e.dataTransfer?.files; if (files?.length) $wire.upload('uploadedFile', files[0]); }, dragOver(e) { e.preventDefault(); this.dragging = true; }, dragLeave() { this.dragging = false; } }" x-on:dragover.prevent="dragOver($event)" x-on:dragleave.prevent="dragLeave()" x-on:drop.prevent="drop($event)" style="border: 2px dashed {{ $this->themeColors['borderDark'] }}; border-radius: 0.5rem; padding: 3rem 2rem; text-align: center; background: transparent; min-height: 280px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: border-color 0.2s, background 0.2s;" :style="dragging ? 'border-color: {{ $this->themeColors['primary'] }}; background: rgba(124,58,237,0.04);' : ''">
                <input type="file" wire:model="uploadedFile" accept="image/*" capture="environment" id="media-upload-input" style="display: none;" />
                <label for="media-upload-input" style="cursor: pointer; display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 4rem; height: 4rem; border-radius: 9999px; background: {{ $this->themeColors['bgInput'] }}; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <svg style="width: 2rem; height: 2rem; color: {{ $this->themeColors['textMuted'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <p style="font-size: 0.9375rem; font-weight: 600; color: {{ $this->themeColors['textDark'] }}; margin: 0 0 0.25rem;">Drag and drop an image here</p>
                    <p style="font-size: 0.875rem; color: {{ $this->themeColors['textMuted'] }}; margin: 0 0 1rem;">or click the button below to browse your files</p>
                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; font-size: 0.875rem; font-weight: 600; color: white; background: {{ $this->themeColors['primary'] }}; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Choose File
                    </span>
                    <p style="font-size: 0.75rem; color: {{ $this->themeColors['textLight'] }}; margin: 0.75rem 0 0;">Supported formats: JPEG, PNG, GIF, WEBP • Max size: 10MB</p>
                </label>
                <div wire:loading wire:target="uploadedFile" style="margin-top: 1rem; font-size: 0.875rem; color: {{ $this->themeColors['textMuted'] }};">Uploading...</div>
                @error('uploadedFile')
                    <p style="margin-top: 1rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 2rem; border-top: 1px solid {{ $this->themeColors['borderLight'] }}; background: white;">
        <p style="font-size: 0.75rem; font-weight: 400; color: {{ $this->themeColors['textLight'] }}; margin: 0;">{{ $this->mediaItems->count() }} image{{ $this->mediaItems->count() === 1 ? '' : 's' }} in library</p>
        <button type="button" @click="$dispatch('close-media-modal')" style="background: none; border: none; color: {{ $this->themeColors['textMuted'] }}; font-size: 0.875rem; font-weight: 500; cursor: pointer; padding: 0; transition: color 0.15s;" onmouseover="this.style.color='{{ $this->themeColors['textDark'] }}'" onmouseout="this.style.color='{{ $this->themeColors['textMuted'] }}'">Cancel</button>
    </div>
</div>
