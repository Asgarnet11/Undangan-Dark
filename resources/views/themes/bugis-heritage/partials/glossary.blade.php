@php
    $showGlossary = (bool) data_get($themeConfig, 'adat.showGlossary', false);
    $showLontara = (bool) data_get($themeConfig, 'adat.showLontara', false);
    $previewMode = !empty($isPreview);

    $verifiedGlossaryEntries = ($showGlossary || $previewMode)
        ? \App\Models\AdatGlossaryEntry::where('status', 'diverifikasi')
            ->where('is_published', true)
            ->orderBy('term')
            ->get()
        : collect();
@endphp

@if($verifiedGlossaryEntries->isNotEmpty())
    <div class="bh-glossary-trigger-wrap" style="text-align: center; margin: 2rem 0;">
        <button
            type="button"
            id="bh-glossary-btn"
            class="bh-btn bh-btn--glossary"
            aria-haspopup="dialog"
            aria-controls="bh-glossary-modal"
        >
            <span class="bh-glossary-btn-icon" aria-hidden="true" style="width: 18px; height: 18px; display: inline-flex;">
                <x-bugis-heritage.svg name="glossary-icon" />
            </span>
            <span>Mengenal Istilah Adat</span>
        </button>
    </div>

    {{-- Modal Glosarium Adat --}}
    <div id="bh-glossary-modal" class="bh-modal" role="dialog" aria-modal="true" aria-labelledby="bh-glossary-title" hidden>
        <div class="bh-modal-backdrop"></div>
        <div class="bh-modal-container bh-card bh-card--gold-border">
            <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tl" />
            <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tr" />

            <header class="bh-modal-header" style="position: relative; margin-bottom: 1.5rem; padding-right: 2rem;">
                <p class="bh-kicker">Tradisi &amp; Budaya</p>
                <h3 id="bh-glossary-title" class="bh-display" style="font-size: 1.6rem; color: var(--bh-accent); margin-top: 0.25rem;">
                    Glosarium Adat Bugis
                </h3>
                <p class="bh-body-serif" style="font-size: 0.82rem; color: var(--bh-ink-soft); margin-top: 0.25rem;">
                    Mengenal makna istilah yang menyertai rangkaian prosesi adat.
                </p>

                <button type="button" class="bh-modal-close" id="bh-glossary-close" aria-label="Tutup Glosarium">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </header>

            <div class="bh-modal-body" style="max-height: 60vh; overflow-y: auto; padding-right: 0.5rem; text-align: left;">
                <div class="bh-glossary-list" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @foreach($verifiedGlossaryEntries as $entry)
                        <article class="bh-glossary-item" style="padding-bottom: 1rem; border-bottom: 1px solid var(--bh-line);">
                            <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                                <h4 class="bh-display" style="font-size: 1.15rem; color: var(--bh-ink);">
                                    {{ $entry->term }}
                                </h4>
                                @if($entry->latin_reading && $entry->latin_reading !== $entry->term)
                                    <span class="bh-body-serif" style="font-size: 0.85rem; font-style: italic; color: var(--bh-gold-soft);">
                                        [{{ $entry->latin_reading }}]
                                    </span>
                                @endif
                            </div>

                            @if($showLontara && !empty($entry->lontara_text))
                                <p class="bh-lontara" lang="bug-Bugi" aria-label="{{ $entry->latin_reading ?: $entry->term }}" style="font-size: 1.35rem; margin: 0.25rem 0; color: var(--bh-accent);">
                                    {{ $entry->lontara_text }}
                                </p>
                            @endif

                            <p class="bh-body-serif" style="font-size: 0.88rem; line-height: 1.6; color: var(--bh-ink-soft); margin-top: 0.35rem;">
                                {{ $entry->meaning_id }}
                            </p>

                            @if($entry->regional_note)
                                <p class="bh-kicker" style="font-size: 0.68rem; margin-top: 0.5rem; color: var(--bh-ink-faint); text-transform: none; letter-spacing: normal;">
                                    Catatan variasi: {{ $entry->regional_note }}
                                </p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>

            <footer class="bh-modal-footer" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--bh-line); text-align: center;">
                <p class="bh-kicker" style="font-size: 0.68rem; opacity: 0.7;">
                    Istilah adat telah diverifikasi sesuai Kamus Konten Adat Bugis.
                </p>
            </footer>
        </div>
    </div>
@endif
