@php
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
    $mapsBelow = (bool) data_get($themeConfig, 'layout.mapsAndRsvpBelowEvents', true);
    $primaryEvent = $invitation->events->first();
@endphp

<section id="section-events" class="db-section db-section--deep db-reveal-section">
    <div class="db-ornament-cluster" aria-hidden="true">
        <x-dark-boho.svg name="dried-flower-cluster" class="db-svg-cluster" />
    </div>

    <p class="db-kicker">{{ __('invitation.events.kicker') }}</p>
    <h2 class="db-display db-section-title">{{ __('invitation.events.heading') }}</h2>

    <div class="db-events-list">
        @foreach($invitation->events as $event)
            @php
                $startAt = $event->startsAtInTimezone($timezone);
                $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;
            @endphp
            <article class="db-card db-card--arch db-event-card">
                <h3 class="db-display db-event-title">{{ $event->title }}</h3>
                <p class="db-event-date">{{ $startAt->translatedFormat('l, d F Y') }}</p>
                <p class="db-event-time">
                    {{ $startAt->format('H.i') }} &ndash; {{ $endAt ? $endAt->format('H.i') : __('invitation.events.until_finished') }} {{ $tzAbbr }}
                </p>

                <div class="db-event-location">
                    <strong class="db-event-venue">{{ $event->venue_name }}</strong>
                    <p class="db-body-soft db-event-address">{{ $event->address }}</p>
                </div>

                <div class="db-actions-row">
                    <a
                        href="{{ $event->googleMapsUrl() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="db-btn db-btn--sm"
                    >
                        {{ __('invitation.events.view_location') }}
                    </a>
                    <a
                        href="{{ $event->googleCalendarUrl($invitation) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="db-btn db-btn--ghost db-btn--sm"
                    >
                        {{ __('invitation.events.save_calendar') }}
                    </a>
                    <a
                        href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]) }}"
                        class="db-btn db-btn--ghost db-btn--sm"
                    >
                        {{ __('invitation.events.download_ics') }}
                    </a>
                </div>

                @if(!$mapsBelow)
                    <div class="db-map-embed">
                        <iframe
                            title="Peta lokasi {{ $event->venue_name }}"
                            src="{{ $event->googleMapsEmbedUrl() }}"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                @endif
            </article>
        @endforeach
    </div>

    @if($mapsBelow && $primaryEvent)
        <div class="db-map-embed db-map-embed--below">
            <iframe
                title="Peta lokasi {{ $primaryEvent->venue_name }}"
                src="{{ $primaryEvent->googleMapsEmbedUrl() }}"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    @endif

    <div class="db-divider" aria-hidden="true">
        <x-dark-boho.svg name="divider-boho" class="db-svg-divider" />
    </div>
</section>
