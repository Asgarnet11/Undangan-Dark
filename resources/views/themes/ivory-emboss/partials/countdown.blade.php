@php
    $primaryEvent = $invitation->events->first();
    $targetTimestamp = $primaryEvent ? ($primaryEvent->starts_at?->timestamp ?? 0) : 0;
@endphp

<section id="section-countdown" class="iv-section iv-countdown" aria-label="Hitung Mundur">
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-c.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-c.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-c@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.countdown.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.countdown.heading') }}</h2>

        <div id="iv-countdown" class="iv-countdown-grid" data-timestamp="{{ $targetTimestamp }}">
            <div class="iv-scallop-card">
                <img class="iv-scallop-border" src="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }}" srcset="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/scallop-mask@2x.webp') }} 2x" alt="" aria-hidden="true" width="160" height="160" loading="lazy">
                <span id="iv-days" class="iv-countdown-number" data-unit="days">00</span>
                <span class="iv-countdown-label">{{ __('invitation.countdown.days') }}</span>
            </div>
            <div class="iv-scallop-card">
                <img class="iv-scallop-border" src="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }}" srcset="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/scallop-mask@2x.webp') }} 2x" alt="" aria-hidden="true" width="160" height="160" loading="lazy">
                <span id="iv-hours" class="iv-countdown-number" data-unit="hours">00</span>
                <span class="iv-countdown-label">{{ __('invitation.countdown.hours') }}</span>
            </div>
            <div class="iv-scallop-card">
                <img class="iv-scallop-border" src="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }}" srcset="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/scallop-mask@2x.webp') }} 2x" alt="" aria-hidden="true" width="160" height="160" loading="lazy">
                <span id="iv-minutes" class="iv-countdown-number" data-unit="minutes">00</span>
                <span class="iv-countdown-label">{{ __('invitation.countdown.minutes') }}</span>
            </div>
            <div class="iv-scallop-card">
                <img class="iv-scallop-border" src="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }}" srcset="{{ asset('themes/ivory-emboss/relief/scallop-mask.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/scallop-mask@2x.webp') }} 2x" alt="" aria-hidden="true" width="160" height="160" loading="lazy">
                <span id="iv-seconds" class="iv-countdown-number" data-unit="seconds">00</span>
                <span class="iv-countdown-label">{{ __('invitation.countdown.seconds') }}</span>
            </div>
        </div>

        <div id="iv-countdown-elapsed" class="iv-card iv-countdown-elapsed" hidden>
            <p>{{ __('invitation.countdown.elapsed') }}</p>
        </div>

        <div class="iv-countdown-flower" data-density-layer="normal" aria-hidden="true">
            <img
                src="{{ asset('themes/ivory-emboss/relief/flower-open.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/flower-open.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/flower-open@2x.webp') }} 2x"
                alt=""
                width="240"
                height="240"
                loading="lazy"
                decoding="async"
            >
        </div>
    </div>
</section>
