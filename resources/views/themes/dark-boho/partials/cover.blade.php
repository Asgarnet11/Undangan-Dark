@php
    $firstEvent = $invitation->events->first();
    $timezone = $invitation->resolvedTimezone();
    $coverDateText = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->translatedFormat('l, d F Y') : '';
    $showGuestName = data_get($themeConfig, 'cover.showGuestName', true);
    $coverUsePhoto = data_get($themeConfig, 'cover.usephoto', true);
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $illustrationMode = data_get($themeConfig, 'layout.illustrationMode', 'illustrated');
    $coverPhotoUrl = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo) ?: asset('themes/dark-boho/img/cover-boho.webp');
@endphp

<header id="db-cover" class="db-cover db-cover--{{ $coverStyle }}">
    @if($coverUsePhoto || !empty($isPreview))
        <img
            src="{{ $coverPhotoUrl }}"
            alt="{{ $invitation->title }}"
            class="db-cover-photo"
            width="960"
            height="1440"
            fetchpriority="high"
            decoding="async"
            @if(!$coverUsePhoto) hidden @endif
        >
    @endif

    <div class="db-cover-overlay" aria-hidden="true"></div>

    <div class="db-arch-doors" aria-hidden="true">
        <div class="db-arch-door db-arch-door--left">
            <div class="db-arch-door-trim"></div>
        </div>
        <div class="db-arch-door db-arch-door--right">
            <div class="db-arch-door-trim"></div>
        </div>
    </div>

    @if($illustrationMode !== 'minimal' || !empty($isPreview))
        <div class="db-cover-pampas db-cover-pampas--left" aria-hidden="true" @if($illustrationMode === 'minimal') hidden @endif>
            <x-dark-boho.svg name="pampas-left" class="db-svg-pampas" />
        </div>
        <div class="db-cover-pampas db-cover-pampas--right" aria-hidden="true" @if($illustrationMode === 'minimal') hidden @endif>
            <x-dark-boho.svg name="pampas-right" class="db-svg-pampas" />
        </div>
    @endif

    <div class="db-particles" aria-hidden="true">
        @for($i = 0; $i < 16; $i++)
            <span class="db-particle" style="--p-x: {{ ($i * 23 + 7) % 94 }}%; --p-y: {{ ($i * 31 + 11) % 88 }}%; --p-size: {{ 2 + ($i % 3) }}px; --p-dur: {{ 8 + ($i % 6) * 1.4 }}s; --p-delay: -{{ ($i * 1.1) }}s;"></span>
        @endfor
    </div>

    <div class="db-cover-inner">
        <div class="db-cover-top">
            <div class="db-cover-celestial" aria-hidden="true">
                <x-dark-boho.svg name="sun-moon" class="db-svg-celestial" />
            </div>

            <div class="db-cover-monogram" aria-hidden="true">
                <x-dark-boho.svg name="monogram-frame" class="db-svg-monogram" />
                <span class="db-monogram-initials">{{ $invitation->monogramText() }}</span>
            </div>

            <p class="db-kicker db-cover-subtitle">{{ __('invitation.cover.subtitle') }}</p>

            <h1 class="db-display db-cover-names">
                <span class="db-cover-name">{{ $invitation->bride_nickname }}</span>
                <span class="db-script db-cover-amp">&amp;</span>
                <span class="db-cover-name">{{ $invitation->groom_nickname }}</span>
            </h1>

            @if($coverDateText !== '')
                <p class="db-cover-date">{{ $coverDateText }}</p>
            @endif
        </div>

        <div class="db-cover-bottom">
            @if($showGuestName || !empty($isPreview))
                <div class="db-cover-guest" @if(!$showGuestName) hidden @endif>
                    <span class="db-cover-guest-label">{{ __('invitation.cover.guest_prefix') }}</span>
                    <strong class="db-cover-guest-name">{{ $guest ? $guest->name : __('invitation.cover.default_guest') }}</strong>
                </div>
            @endif

            <button type="button" id="db-open-invitation" class="db-btn db-cover-open">
                {{ __('invitation.cover.open_button') }}
            </button>
        </div>
    </div>
</header>
