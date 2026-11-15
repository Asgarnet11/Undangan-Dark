@php
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
    $mapsBelow = (bool) data_get($themeConfig, 'layout.mapsAndRsvpBelowEvents', true);
    $primaryEvent = $invitation->events->first();
@endphp

<section id="section-events" class="sm-section sm-section--soft sm-section-events">
    <div class="sm-wave-divider sm-wave-divider--top" aria-hidden="true">
        <x-sunny-meadow.svg name="wave-divider" class="sm-svg-wave" />
    </div>

    <div class="sm-section-header">
        <p class="sm-section-subtitle">{{ __('invitation.events.heading') }}</p>
        <h2 class="sm-section-title">{{ __('invitation.events.heading') }}</h2>
    </div>

    <div class="sm-events-list">
        @foreach($invitation->events as $event)
            @php
                $startAt = $event->startsAtInTimezone($timezone);
                $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;
            @endphp
            <article class="sm-card sm-event-card">
                <div class="sm-scallop-accent" aria-hidden="true">
                    <x-sunny-meadow.svg name="scallop-edge" class="sm-svg-scallop" />
                </div>

                <h3 class="sm-title-display sm-event-title">{{ $event->title }}</h3>
                <p class="sm-event-date">{{ $startAt->translatedFormat('l, d F Y') }}</p>
                <p class="sm-event-time">
                    {{ $startAt->format('H.i') }} &ndash; {{ $endAt ? $endAt->format('H.i') : __('invitation.events.until_finished') }} {{ $tzAbbr }}
                </p>

                <div class="sm-event-location">
                    <strong class="sm-event-venue">{{ $event->venue_name }}</strong>
                    <p class="sm-event-address">{{ $event->address }}</p>
                </div>

                <div class="sm-actions-row">
                    <a
                        href="{{ $event->googleMapsUrl() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="sm-btn sm-btn-primary sm-btn--sm"
                    >
                        {{ __('invitation.events.view_location') }}
                    </a>
                    <a
                        href="{{ $event->googleCalendarUrl($invitation) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="sm-btn sm-btn-outline sm-btn--sm"
                    >
                        {{ __('invitation.events.save_calendar') }}
                    </a>
                    <a
                        href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]) }}"
                        class="sm-btn sm-btn-outline sm-btn--sm"
                    >
                        {{ __('invitation.events.download_ics') }}
                    </a>
                </div>

                @if(!$mapsBelow)
                    <div class="sm-map-embed">
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
        <div class="sm-map-embed sm-map-embed--below sm-card">
            <iframe
                title="Peta lokasi {{ $primaryEvent->venue_name }}"
                src="{{ $primaryEvent->googleMapsEmbedUrl() }}"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    @endif
</section>
