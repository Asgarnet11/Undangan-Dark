@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $firstName = $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname;
    $secondName = $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname;
    $monogram = $invitation->monogram_initial ?: (
        strtoupper(substr($firstName ?: 'A', 0, 1)) . '&' . strtoupper(substr($secondName ?: 'B', 0, 1))
    );
@endphp

<nav class="iv-bottom-nav" id="iv-bottom-nav" aria-label="Navigasi bagian undangan" hidden>
    <a href="#section-couple" class="iv-nav-item" data-section="section-couple">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <circle cx="9" cy="8" r="3" />
            <circle cx="16" cy="9" r="2.5" />
            <path d="M4 19c0-3 2.5-5 5-5s5 2 5 5M14 19c0-2.2 1.8-4 4-4s3 1.4 3 4" stroke-linecap="round" />
        </svg>
        <span>Mempelai</span>
    </a>

    <a href="#section-events" class="iv-nav-item" data-section="section-events">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M12 21s6-5.2 6-10.5a6 6 0 1 0-12 0C6 15.8 12 21 12 21Z" />
            <circle cx="12" cy="10.5" r="2" />
        </svg>
        <span>Acara</span>
    </a>

    <a href="#iv-cover" class="iv-nav-item iv-nav-item--center" data-section="iv-cover" aria-label="Kembali ke atas">
        <span class="iv-nav-monogram">
            <img
                src="{{ asset('themes/ivory-emboss/relief/monogram-ring.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/monogram-ring.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/monogram-ring@2x.webp') }} 2x"
                alt=""
                aria-hidden="true"
                width="360"
                height="360"
            >
            <span>{{ $monogram }}</span>
        </span>
    </a>

    <a href="#section-gallery" class="iv-nav-item" data-section="section-gallery">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <rect x="4" y="5" width="16" height="14" rx="2" />
            <circle cx="9" cy="10" r="1.4" />
            <path d="M6 17l4.5-4.5L14 16l2.5-2.5L19 17" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span>Galeri</span>
    </a>

    <a href="#section-wishes" class="iv-nav-item" data-section="section-wishes">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M5 6h14a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H9l-4 3v-3H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" stroke-linejoin="round" />
        </svg>
        <span>Ucapan</span>
    </a>
</nav>
