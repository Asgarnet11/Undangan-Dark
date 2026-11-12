@php
    $record = $getRecord();
    $previewUrl = $record ? route('invitation.preview', ['invitation' => $record]) : null;
@endphp

<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-gray-900">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pratinjau Langsung</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Perubahan opsi di kiri langsung terlihat tanpa memuat ulang halaman.
            </p>
        </div>

        @if($previewUrl)
            <div class="flex flex-wrap gap-1.5">
                <a
                    href="{{ route('invitation.show', ['slug' => $record->slug]) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-white/15 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Buka Halaman Publik
                </a>
            </div>
        @endif
    </div>

    @if($previewUrl)
        <div
            x-data="{
                iframeEl: null,
                syncPreview() {
                    const frame = this.$refs.previewFrame;
                    if (!frame || !frame.contentWindow) {
                        return;
                    }
                    const raw = this.$wire.get('data.theme_config');
                    const payload = raw ? JSON.parse(JSON.stringify(raw)) : {};
                    frame.contentWindow.postMessage(
                        { type: 'dark-boho:preview-config', config: payload },
                        window.location.origin
                    );
                },
                sendAction(type, extra = {}) {
                    const frame = this.$refs.previewFrame;
                    if (!frame || !frame.contentWindow) {
                        return;
                    }
                    frame.contentWindow.postMessage(
                        { type, ...extra },
                        window.location.origin
                    );
                },
                reloadFrame() {
                    const frame = this.$refs.previewFrame;
                    if (frame) {
                        frame.src = '{{ $previewUrl }}?t=' + Date.now();
                    }
                }
            }"
            x-init="
                window.addEventListener('message', (event) => {
                    if (event.origin === window.location.origin && event.data?.type === 'dark-boho:preview-ready') {
                        syncPreview();
                    }
                });
            "
            x-effect="syncPreview()"
            class="flex flex-col items-center gap-3"
        >
            <div class="flex flex-wrap justify-center gap-1.5" wire:ignore>
                <button
                    type="button"
                    x-on:click="sendAction('dark-boho:preview-toggle-cover')"
                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-700 hover:bg-amber-500/20 dark:text-amber-300"
                >
                    Buka / Tutup Cover
                </button>
                <button
                    type="button"
                    x-on:click="sendAction('dark-boho:preview-scroll', { target: 'section-couple' })"
                    class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-white/15 dark:bg-gray-800 dark:text-gray-200"
                >
                    Mempelai
                </button>
                <button
                    type="button"
                    x-on:click="sendAction('dark-boho:preview-scroll', { target: 'section-events' })"
                    class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-white/15 dark:bg-gray-800 dark:text-gray-200"
                >
                    Acara
                </button>
                <button
                    type="button"
                    x-on:click="sendAction('dark-boho:preview-scroll', { target: 'section-wishes' })"
                    class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-white/15 dark:bg-gray-800 dark:text-gray-200"
                >
                    Ucapan
                </button>
                <button
                    type="button"
                    x-on:click="reloadFrame()"
                    class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-white/15 dark:bg-gray-800 dark:text-gray-200"
                >
                    Muat Ulang
                </button>
            </div>

            <div
                wire:ignore
                class="w-full max-w-[390px] overflow-hidden rounded-[26px] border-4 border-gray-800 bg-[#151513] shadow-xl dark:border-gray-700"
            >
                <iframe
                    x-ref="previewFrame"
                    id="db-admin-preview-frame"
                    src="{{ $previewUrl }}"
                    title="Pratinjau Langsung Undangan"
                    class="h-[660px] w-full border-0 bg-[#151513]"
                ></iframe>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-xs text-gray-500 dark:border-white/15 dark:text-gray-400">
            Simpan undangan terlebih dahulu untuk mengaktifkan panel pratinjau langsung.
        </div>
    @endif
</div>

