<section id="section-rsvp" class="bh-section bh-rsvp-section" aria-label="Konfirmasi Kehadiran">
    <div class="bh-divider" aria-hidden="true">
        <x-bugis-heritage.svg name="divider-diamond" />
    </div>

    <p class="bh-kicker">Konfirmasi Kehadiran</p>
    <h2 class="bh-display bh-section-title">RSVP</h2>

    <div class="bh-card bh-card--gold-border">
        <p class="bh-body-serif" style="margin-bottom: 1rem;">
            Mohon konfirmasikan kehadiran Bapak/Ibu/Saudara/i untuk kenyamanan penyelenggaraan acara.
        </p>
        @if(!empty($guest))
            <p class="bh-kicker" style="color: var(--bh-accent);">Tamu: {{ $guest->name }}</p>
        @endif
    </div>
</section>
