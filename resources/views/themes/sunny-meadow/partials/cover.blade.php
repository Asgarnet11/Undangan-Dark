@php
    $firstEvent = $invitation->events->first();
    $timezone = $invitation->resolvedTimezone();
    $coverDateText = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->translatedFormat('l, d F Y') : '';
    $showGuestName = data_get($themeConfig, 'cover.showGuestName', true);
    $coverUsePhoto = data_get($themeConfig, 'cover.usePhoto', false);
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $brideFirst = data_get($themeConfig, 'layout.brideFirst', true);
    $firstName = $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname;
    $secondName = $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname;
    $coverPhotoUrl = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo) ?: '';
@endphp

<header id="sm-cover" class="sm-cover sm-cover--{{ $coverStyle }}">
    @if(($coverUsePhoto || !empty($isPreview)) && $coverPhotoUrl !== '')
        <img
            src="{{ $coverPhotoUrl }}"
            alt="{{ $invitation->title }}"
            class="sm-cover-photo"
            width="960"
            height="1440"
            fetchpriority="high"
            decoding="async"
            @if(!$coverUsePhoto) hidden @endif
        >
    @endif

    <div class="sm-cover-overlay" aria-hidden="true"></div>

    <!-- Decorative Corner Doodle Flowers -->
    <div class="sm-cover-ornament sm-cover-ornament--tl" aria-hidden="true">
        <x-sunny-meadow.svg name="corner-tl" class="sm-svg-corner" />
    </div>
    <div class="sm-cover-ornament sm-cover-ornament--br" aria-hidden="true">
        <x-sunny-meadow.svg name="corner-br" class="sm-svg-corner" />
    </div>

    <!-- Floating Sun & Drifting Clouds -->
    <div class="sm-cover-sun-wrap" aria-hidden="true">
        <x-sunny-meadow.svg name="sun-doodle" class="sm-svg-sun" />
    </div>
    <div class="sm-cover-cloud-wrap sm-cover-cloud--left" aria-hidden="true">
        <x-sunny-meadow.svg name="cloud" class="sm-svg-cloud" />
    </div>
    <div class="sm-cover-cloud-wrap sm-cover-cloud--right" aria-hidden="true">
        <x-sunny-meadow.svg name="cloud" class="sm-svg-cloud" />
    </div>

    <!-- Bobbing Doodle Flowers -->
    <div class="sm-cover-bobbing sm-cover-bobbing--left sm-anim-bobbing" aria-hidden="true">
        <x-sunny-meadow.svg name="flower-big" class="sm-svg-flower-big" />
    </div>

    <!-- Confetti Set (Hidden until open button burst) -->
    <div id="sm-confetti-container" class="sm-confetti-container" aria-hidden="true">
        <x-sunny-meadow.svg name="confetti-set" class="sm-svg-confetti" />
    </div>

    <div class="sm-cover-inner">
        <div class="sm-cover-top">
            <div class="sm-cover-monogram" aria-hidden="true">
                <x-sunny-meadow.svg name="monogram-badge" class="sm-svg-monogram" />
                <span class="sm-monogram-initials">{{ $invitation->monogramText() }}</span>
            </div>

            <p class="sm-cover-kicker">{{ __('invitation.cover.subtitle') }}</p>

            <h1 class="sm-title-display sm-cover-names">
                <span class="sm-cover-name">{{ $firstName }}</span>
                <span class="sm-title-script sm-cover-amp">&amp;</span>
                <span class="sm-cover-name">{{ $secondName }}</span>
            </h1>

            @if($coverDateText !== '')
                <div class="sm-cover-date-pill">
                    <x-sunny-meadow.svg name="star-small" class="sm-svg-star-tiny" />
                    <span>{{ $coverDateText }}</span>
                    <x-sunny-meadow.svg name="star-small" class="sm-svg-star-tiny" />
                </div>
            @endif
        </div>

        <div class="sm-cover-bottom">
            @if($showGuestName || !empty($isPreview))
                <div class="sm-cover-guest" @if(!$showGuestName) hidden @endif>
                    <span class="sm-cover-guest-label">{{ __('invitation.cover.guest_prefix') }}</span>
                    <strong class="sm-cover-guest-name">{{ $guest ? $guest->name : __('invitation.cover.default_guest') }}</strong>
                </div>
            @endif

            <button type="button" id="sm-open-invitation" class="sm-btn sm-btn-primary sm-cover-open sm-anim-bobbing">
                <x-sunny-meadow.svg name="star-small" class="sm-btn-star" />
                <span>{{ __('invitation.cover.open_button') }}</span>
                <x-sunny-meadow.svg name="star-small" class="sm-btn-star" />
            </button>
        </div>
    </div>
</header>

