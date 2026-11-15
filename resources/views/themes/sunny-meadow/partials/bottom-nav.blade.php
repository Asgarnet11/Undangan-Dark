<nav class="sm-bottom-nav" id="sm-bottom-nav" aria-label="Navigasi bagian undangan" hidden>
    <div class="sm-bottom-nav-track">
        <span class="sm-nav-indicator" id="sm-nav-indicator" aria-hidden="true"></span>

        <a href="#section-couple" class="sm-nav-item" data-nav-target="section-couple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                <circle cx="8" cy="8" r="3" />
                <circle cx="16" cy="9" r="2.5" />
                <path d="M3 18c0-3 2.5-5 5-5s5 2 5 5M13 18c0-2.2 1.8-4 4-4s3 1.4 3 4" stroke-linecap="round" />
            </svg>
            <span>{{ __('invitation.nav.couple') }}</span>
        </a>

        <a href="#section-events" class="sm-nav-item" data-nav-target="section-events">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                <rect x="3" y="5" width="18" height="15" rx="3" />
                <line x1="8" y1="3" x2="8" y2="7" stroke-linecap="round" />
                <line x1="16" y1="3" x2="16" y2="7" stroke-linecap="round" />
                <line x1="3" y1="10" x2="21" y2="10" />
                <circle cx="8" cy="14" r="1" fill="currentColor" />
                <circle cx="12" cy="14" r="1" fill="currentColor" />
                <circle cx="16" cy="14" r="1" fill="currentColor" />
            </svg>
            <span>{{ __('invitation.nav.events') }}</span>
        </a>

        <a href="#sm-cover" class="sm-nav-monogram" data-nav-target="sm-cover" aria-label="{{ __('invitation.nav.top') }}">
            <x-sunny-meadow.svg name="monogram-badge" class="sm-nav-monogram-svg" />
            <span class="sm-nav-monogram-initials">{{ $invitation->monogramText() }}</span>
        </a>

        <a href="#section-gallery" class="sm-nav-item" data-nav-target="section-gallery">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="3" />
                <circle cx="8" cy="9" r="2" />
                <path d="M21 16l-5-5-8 8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>{{ __('invitation.nav.gallery') }}</span>
        </a>

        <a href="#section-wishes" class="sm-nav-item" data-nav-target="section-wishes">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                <path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H8l-5 4v-4H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" stroke-linejoin="round" />
            </svg>
            <span>{{ __('invitation.nav.wishes') }}</span>
        </a>
    </div>
</nav>
