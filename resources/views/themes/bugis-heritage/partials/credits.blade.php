{{-- Modal Kredit dan Atribusi --}}
<div id="bh-credits-modal" class="bh-modal" role="dialog" aria-modal="true" aria-labelledby="bh-credits-title" hidden>
    <div class="bh-modal-backdrop"></div>
    <div class="bh-modal-container bh-card bh-card--gold-border">
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tl" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tr" />

        <header class="bh-modal-header" style="position: relative; margin-bottom: 1.5rem; padding-right: 2rem;">
            <p class="bh-kicker">Kredit &amp; Atribusi</p>
            <h3 id="bh-credits-title" class="bh-display" style="font-size: 1.5rem; color: var(--bh-accent); margin-top: 0.25rem;">
                Bugis Heritage
            </h3>

            <button type="button" class="bh-modal-close" id="bh-credits-close" aria-label="Tutup Informasi Kredit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </header>

        <div class="bh-modal-body" style="max-height: 60vh; overflow-y: auto; text-align: left; font-size: 0.88rem; line-height: 1.7; color: var(--bh-ink-soft);">
            <div style="margin-bottom: 1.25rem;">
                <h4 class="bh-kicker" style="font-size: 0.75rem; color: var(--bh-gold-soft); margin-bottom: 0.25rem;">Pengembangan Tema</h4>
                <p class="bh-body-serif">
                    Didesain dan dikembangkan secara eksklusif oleh
                    <a href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}" target="_blank" rel="noopener noreferrer" style="color: var(--bh-accent); text-decoration: underline;">
                        A2 Dev ({{ config('app.vendor_name', 'a2dev.my.id') }})
                    </a>.
                </p>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <h4 class="bh-kicker" style="font-size: 0.75rem; color: var(--bh-gold-soft); margin-bottom: 0.25rem;">Tipografi Lokal</h4>
                <ul class="bh-body-serif" style="padding-left: 1.25rem;">
                    <li><strong>Cormorant Garamond</strong> &mdash; Christian Schwartz (SIL Open Font License)</li>
                    <li><strong>Jost</strong> &mdash; indestructible type* (SIL Open Font License)</li>
                    <li><strong>Amiri</strong> &mdash; Khaled Hosny (SIL Open Font License)</li>
                    <li><strong>Allura</strong> &mdash; TypeSETit (SIL Open Font License)</li>
                    <li><strong>Noto Sans Buginese</strong> &mdash; Google Noto Project (SIL Open Font License)</li>
                </ul>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <h4 class="bh-kicker" style="font-size: 0.75rem; color: var(--bh-gold-soft); margin-bottom: 0.25rem;">Audio Instrumental</h4>
                <p class="bh-body-serif">
                    Musik latar instrumental berlisensi bebas (Royalty-Free / Creative Commons).
                </p>
            </div>

            <div>
                <h4 class="bh-kicker" style="font-size: 0.75rem; color: var(--bh-gold-soft); margin-bottom: 0.25rem;">Akurasi Budaya</h4>
                <p class="bh-body-serif">
                    Seluruh istilah dan prosesi adat yang tampil pada tema ini telah melalui proses telaah dan verifikasi sesuai Kamus Konten Adat Bugis.
                </p>
            </div>
        </div>

        <footer class="bh-modal-footer" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--bh-line); text-align: center;">
            <p class="bh-kicker" style="font-size: 0.7rem; opacity: 0.75;">
                &copy; {{ date('Y') }} {{ config('app.name', 'Undangan Pernikahan Digital') }}. Seluruh hak cipta dilindungi.
            </p>
        </footer>
    </div>
</div>
