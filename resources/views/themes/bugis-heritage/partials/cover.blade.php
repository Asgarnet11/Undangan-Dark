@php
    $coverPhoto = data_get($themeConfig, 'cover.usePhoto', false)
        ? ($invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo)
            ?: $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo))
        : null;

    $guestName = $guest?->name ?: __('invitation.cover.default_guest');
    $guestCode = $guest?->code ?: '';
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $primaryEvent = $invitation->events->first();
    $eventDate = $primaryEvent ? $primaryEvent->startsAtInTimezone($invitation->resolvedTimezone())->translatedFormat('l, d F Y') : null;
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $monogram = $invitation->monogram_initial ?: (
        $brideFirst
            ? substr($invitation->bride_nickname ?: 'B', 0, 1) . '&' . substr($invitation->groom_nickname ?: 'G', 0, 1)
            : substr($invitation->groom_nickname ?: 'G', 0, 1) . '&' . substr($invitation->bride_nickname ?: 'B', 0, 1)
    );
@endphp

<section id="bh-cover" class="bh-cover bh-cover--{{ $coverStyle }}" aria-label="Sampul Undangan">
    <div class="bh-cover-door bh-cover-door--left bh-pattern-walasuji" aria-hidden="true"></div>
    <div class="bh-cover-door bh-cover-door--right bh-pattern-walasuji" aria-hidden="true"></div>

    @if($coverPhoto)
        <img src="{{ $coverPhoto }}" alt="Foto Sampul {{ $invitation->title }}" class="bh-cover-bg" loading="eager">
    @endif
    <div class="bh-cover-overlay" aria-hidden="true"></div>

    <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tl" />
    <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tr" />
    <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--bl" />
    <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--br" />

    @if($coverStyle === 'frame')
        <div class="bh-cover-frame-border" aria-hidden="true"></div>
    @endif

    <div class="bh-cover-arch" aria-hidden="true"></div>

    <div class="bh-cover-inner">
        <header class="bh-cover-header">
            <div class="bh-cover-silhouette" aria-hidden="true">
                <x-bugis-heritage.svg name="rumah-panggung" />
            </div>

            <div class="bh-cover-badge" aria-hidden="true">
                <x-bugis-heritage.svg name="monogram-badge" />
                <span class="bh-cover-monogram-text">{{ $monogram }}</span>
            </div>

            <p class="bh-cover-subtitle">
                {{ __('invitation.cover.wedding_invitation') }}
            </p>

            <h1 class="bh-display bh-cover-names">
                @if($brideFirst)
                    <span class="bh-name-part">{{ $invitation->bride_nickname }}</span>
                    <span class="bh-script">&amp;</span>
                    <span class="bh-name-part">{{ $invitation->groom_nickname }}</span>
                @else
                    <span class="bh-name-part">{{ $invitation->groom_nickname }}</span>
                    <span class="bh-script">&amp;</span>
                    <span class="bh-name-part">{{ $invitation->bride_nickname }}</span>
                @endif
            </h1>

            <div class="bh-card-border-gold" aria-hidden="true" style="max-width: 200px; margin: 0.5rem auto 0.75rem; color: var(--bh-gold);">
                <x-bugis-heritage.svg name="border-gold" />
            </div>

            @if($eventDate)
                <p class="bh-cover-date" style="font-family: var(--bh-font-serif); font-size: 1.05rem; font-style: italic; color: var(--bh-ink-soft);">
                    {{ $eventDate }}
                </p>
            @endif
        </header>

        <div class="bh-cover-footer">
            @if(data_get($themeConfig, 'cover.showGuestName', true))
                <div class="bh-cover-guest-card">
                    <p class="bh-guest-prefix">{{ __('invitation.cover.recipient_prefix') }}</p>
                    <h2 class="bh-cover-guest-name">{{ $guestName }}</h2>
                    @if($guest && $guest->group)
                        <p class="bh-cover-guest-group">{{ $guest->group }}</p>
                    @endif
                    @if($guestCode)
                        <span class="bh-guest-code" style="display: none;">{{ $guestCode }}</span>
                    @endif
                </div>
            @endif

            <button type="button" id="bh-open-invitation" class="bh-btn bh-btn--primary bh-cover-open-btn" aria-label="{{ __('invitation.cover.open_button') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="18" height="18" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span>{{ __('invitation.cover.open_button') }}</span>
            </button>
        </div>
    </div>
</section>
