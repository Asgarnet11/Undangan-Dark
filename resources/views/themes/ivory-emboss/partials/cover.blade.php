@php
    $guestName = $guest?->name ?: __('invitation.cover.default_guest');
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $coverUsePhoto = (bool) data_get($themeConfig, 'cover.usePhoto', false);
    $showGuestName = (bool) data_get($themeConfig, 'cover.showGuestName', true);
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $firstName = $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname;
    $secondName = $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname;
    $primaryEvent = $invitation->events->first();
    $eventDate = $primaryEvent
        ? $primaryEvent->startsAtInTimezone($invitation->resolvedTimezone())->translatedFormat('l, d F Y')
        : null;
    $coverPhoto = $coverUsePhoto
        ? ($invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo)
            ?: $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo))
        : null;
@endphp

<header id="iv-cover" class="iv-cover iv-cover--{{ $coverStyle }}" aria-label="Sampul Undangan">
    <div class="iv-relief-corner iv-relief-corner--tl" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-tl.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-tl.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-tl@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            decoding="async"
        >
    </div>
    <div class="iv-relief-corner iv-relief-corner--tr" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-tr.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-tr.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-tr@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            decoding="async"
        >
    </div>
    <div class="iv-relief-corner iv-relief-corner--bl" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-bl.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-bl.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-bl@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            decoding="async"
        >
    </div>
    <div class="iv-relief-corner iv-relief-corner--br" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-br.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-br.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-br@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            decoding="async"
        >
    </div>

    <div class="iv-cover-inner iv-safe-zone">
        <div class="iv-cover-top">
            <p class="iv-kicker">{{ __('invitation.cover.subtitle') }}</p>
            <h1 class="iv-cover-names">{{ $firstName }} &amp; {{ $secondName }}</h1>
            @if($eventDate)
                <p class="iv-cover-date">{{ $eventDate }}</p>
            @endif
        </div>

        @if(($coverStyle === 'frame' || $coverUsePhoto) && $coverPhoto)
            <div class="iv-cover-frame-wrap">
                <div class="iv-cover-frame-photo">
                    <img src="{{ $coverPhoto }}" alt="Foto {{ $invitation->title }}" width="400" height="480" fetchpriority="high">
                </div>
                <img
                    class="iv-cover-frame-wreath"
                    src="{{ asset('themes/ivory-emboss/relief/wreath-oval.webp') }}"
                    srcset="{{ asset('themes/ivory-emboss/relief/wreath-oval.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/wreath-oval@2x.webp') }} 2x"
                    alt=""
                    aria-hidden="true"
                    width="640"
                    height="760"
                >
            </div>
        @else
            <div class="iv-cover-emblem" aria-hidden="true">
                <img
                    src="{{ asset('themes/ivory-emboss/relief/emblem-melati.webp') }}"
                    srcset="{{ asset('themes/ivory-emboss/relief/emblem-melati.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/emblem-melati@2x.webp') }} 2x"
                    alt=""
                    width="420"
                    height="360"
                >
            </div>
        @endif

        <div class="iv-cover-bottom">
            @if($showGuestName)
                <div class="iv-card iv-cover-guest">
                    <p class="iv-kicker iv-cover-guest-label">{{ __('invitation.cover.recipient_prefix') }}</p>
                    <p class="iv-cover-guest-name">{{ $guestName }}</p>
                </div>
            @endif

            <button type="button" id="iv-open-invitation" class="iv-btn iv-cover-btn">
                {{ __('invitation.cover.open_button') }}
            </button>
        </div>
    </div>
</header>
