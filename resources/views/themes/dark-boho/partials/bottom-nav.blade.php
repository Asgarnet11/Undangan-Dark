<nav class="db-bottom-nav" id="db-bottom-nav" aria-label="Navigasi bagian undangan" hidden>
    <div class="db-bottom-nav-track">
        <span class="db-nav-indicator" id="db-nav-indicator" aria-hidden="true"></span>
        <a href="#section-couple" class="db-nav-item" data-nav-target="section-couple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" aria-hidden="true">
                <circle cx="9" cy="8" r="3" />
                <circle cx="16" cy="9" r="2.5" />
                <path d="M4 19c0-3 2.5-5 5-5s5 2 5 5M14 19c0-2.2 1.8-4 4-4s3 1.4 3 4" stroke-linecap="round" />
            </svg>
            <span>{{ __('invitation.nav.couple') }}</span>
        </a>

        <a href="#section-gallery" class="db-nav-item" data-nav-target="section-gallery">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" aria-hidden="true">
                <rect x="4" y="5" width="16" height="14" rx="2" />
                <circle cx="9" cy="10" r="1.4" />
                <path d="M6 17l4.5-4.5L14 16l2.5-2.5L19 17" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>{{ __('invitation.nav.gallery') }}</span>
        </a>

        <a href="#db-cover" class="db-nav-monogram" data-nav-target="db-cover" aria-label="{{ __('invitation.nav.top') }}">
            <x-dark-boho.svg name="monogram-frame" class="db-nav-monogram-svg" />
            <span>{{ $invitation->monogramText() }}</span>
        </a>

        <a href="#section-events" class="db-nav-item" data-nav-target="section-events">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" aria-hidden="true">
                <path d="M12 21s6-5.2 6-10.5a6 6 0 1 0-12 0C6 15.8 12 21 12 21Z" />
                <circle cx="12" cy="10.5" r="2" />
            </svg>
            <span>{{ __('invitation.nav.events') }}</span>
        </a>

        <a href="#section-wishes" class="db-nav-item" data-nav-target="section-wishes">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" aria-hidden="true">
                <path d="M5 6h14a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H9l-4 3v-3H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" stroke-linejoin="round" />
            </svg>
            <span>{{ __('invitation.nav.wishes') }}</span>
        </a>
    </div>
</nav>
