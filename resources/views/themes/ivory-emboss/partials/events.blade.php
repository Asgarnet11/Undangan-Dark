@php
    $primaryEvent = $invitation->events->first();
    $mapQuery = $primaryEvent?->venue_name || $primaryEvent?->address
        ? trim(($primaryEvent->venue_name ?? '') . ' ' . ($primaryEvent->address ?? ''))
        : null;
@endphp

<section id="section-events" class="iv-section iv-events" aria-label="Rangkaian Acara">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-c.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-c.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-c@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.events.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.events.heading') }}</h2>

        <div class="iv-events-list">
            @foreach($invitation->events as $event)
                <div class="iv-event-item">
                    <x-shared.event-card :event="$event" :invitation="$invitation" />
                </div>
            @endforeach
        </div>

        @if($mapQuery)
            <div class="iv-card iv-map-card" data-lazy-map data-map-src="https://www.google.com/maps?q={{ rawurlencode($mapQuery) }}&output=embed">
                <p class="iv-kicker" style="margin-bottom: 0.75rem;">Peta Lokasi Utama</p>
                <div class="iv-map-frame" data-map-container>
                    <button type="button" class="iv-btn iv-btn--ghost" data-load-map-btn>
                        Muat Peta Lokasi
                    </button>
                </div>
            </div>
        @endif
    </div>
</section>
