<div id="iv-credits-modal" class="iv-modal" role="dialog" aria-modal="true" aria-labelledby="iv-credits-title" hidden>
    <div class="iv-modal-backdrop" data-credits-close></div>
    <div class="iv-modal-dialog iv-card">
        <header class="iv-modal-header">
            <div>
                <p class="iv-kicker">Kredit &amp; Atribusi</p>
                <h3 id="iv-credits-title" class="iv-display" style="font-size: 1.35rem; margin-top: 0.25rem;">
                    Ivory Emboss
                </h3>
            </div>
            <button type="button" class="iv-modal-close" id="iv-credits-close" data-credits-close aria-label="Tutup Informasi Kredit">
                &times;
            </button>
        </header>

        <div class="iv-modal-body">
            <div style="margin-bottom: 1rem;">
                <p class="iv-kicker" style="margin-bottom: 0.25rem;">Pengembangan Tema</p>
                <p style="margin: 0; font-size: 0.9rem; color: var(--iv-ink-soft);">
                    Dikembangkan oleh
                    <a href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}" target="_blank" rel="noopener noreferrer" style="color: var(--iv-ink);">
                        A2 Dev
                    </a>.
                </p>
            </div>
            <div style="margin-bottom: 1rem;">
                <p class="iv-kicker" style="margin-bottom: 0.25rem;">Tipografi Lokal</p>
                <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.88rem; color: var(--iv-ink-soft);">
                    <li><strong>Montserrat</strong> (SIL Open Font License)</li>
                    <li><strong>Work Sans</strong> (SIL Open Font License)</li>
                    <li><strong>Amiri</strong> (SIL Open Font License)</li>
                </ul>
            </div>
            <div>
                <p class="iv-kicker" style="margin-bottom: 0.25rem;">Ornamen &amp; Audio</p>
                <p style="margin: 0; font-size: 0.88rem; color: var(--iv-ink-soft);">
                    Ornamen melati timbul dibuat secara prosedural orisinal; musik instrumental berlisensi bebas.
                </p>
            </div>
        </div>
    </div>
</div>
