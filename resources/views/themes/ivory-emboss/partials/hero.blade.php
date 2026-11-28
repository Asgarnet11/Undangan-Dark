@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $firstName = $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname;
    $secondName = $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname;
    $photoFrame = data_get($themeConfig, 'layout.photoFrame', 'oval');
    $heroPhoto = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo)
        ?: $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo);
    $primaryEvent = $invitation->events->first();
    $eventDate = $primaryEvent
        ? $primaryEvent->startsAtInTimezone($invitation->resolvedTimezone())->translatedFormat('l, d F Y')
        : null;
    $monogram = $invitation->monogram_initial ?: (
        strtoupper(substr($firstName ?: 'A', 0, 1)) . '&' . strtoupper(substr($secondName ?: 'B', 0, 1))
    );
@endphp

<section id="section-hero" class="iv-section iv-hero" aria-label="Pembuka Undangan">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-a.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-a.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-a@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-a.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-a.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-a@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone iv-hero-inner">
        <p class="iv-kicker">{{ __('invitation.cover.subtitle') }}</p>

        @if($heroPhoto)
            <div class="iv-hero-frame iv-hero-frame--{{ $photoFrame }}">
                <div class="iv-hero-photo-box">
                    <img src="{{ $heroPhoto }}" alt="{{ $invitation->title }}" width="440" height="540" loading="lazy">
                </div>
                @if($photoFrame === 'oval')
                    <img
                        class="iv-hero-wreath"
                        src="{{ asset('themes/ivory-emboss/relief/wreath-oval.webp') }}"
                        srcset="{{ asset('themes/ivory-emboss/relief/wreath-oval.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/wreath-oval@2x.webp') }} 2x"
                        alt=""
                        aria-hidden="true"
                        width="640"
                        height="760"
                        loading="lazy"
                    >
                @endif
            </div>
        @else
            <div class="iv-hero-monogram-wrap">
                <img
                    class="iv-hero-monogram-ring"
                    src="{{ asset('themes/ivory-emboss/relief/monogram-ring.webp') }}"
                    srcset="{{ asset('themes/ivory-emboss/relief/monogram-ring.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/monogram-ring@2x.webp') }} 2x"
                    alt=""
                    aria-hidden="true"
                    width="360"
                    height="360"
                    loading="lazy"
                >
                <span class="iv-hero-monogram-text">{{ $monogram }}</span>
            </div>
        @endif

        <h2 class="iv-display iv-hero-names">{{ $firstName }} &amp; {{ $secondName }}</h2>
        @if($eventDate)
            <p class="iv-hero-date">{{ $eventDate }}</p>
        @endif
    </div>
</section>
