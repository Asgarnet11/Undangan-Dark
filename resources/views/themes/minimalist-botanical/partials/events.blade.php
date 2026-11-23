@php
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
@endphp

<section id="section-events" class="mb-section mb-section--soft mb-reveal">
    <p class="mb-kicker">{{ __('invitation.events.kicker') }}</p>
    <h2 class="mb-display mb-section-title">{{ __('invitation.events.heading') }}</h2>

    <div class="mb-divider-wrap" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-divider" />
    </div>

    <div class="mb-events-list">
        @foreach($invitation->events as $event)
            @php
                $startAt = $event->startsAtInTimezone($timezone);
                $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;
            @endphp
            <article class="mb-card mb-event-card" style="margin-bottom: 1.5rem;">
                <h3 class="mb-display" style="font-size: 1.5rem; color: var(--mb-text-main); margin-bottom: 0.5rem;">
                    {{ $event->title }}
                </h3>
                <p class="mb-kicker" style="font-size: 0.85rem; color: var(--mb-primary); margin-bottom: 0.25rem;">
                    {{ $startAt->translatedFormat('l, d F Y') }}
                </p>
                <p class="mb-body-soft" style="font-weight: 500; margin-bottom: 1rem;">
                    {{ $startAt->format('H.i') }} &ndash; {{ $endAt ? $endAt->format('H.i') : __('invitation.events.until_finished') }} {{ $tzAbbr }}
                </p>

                <div style="margin-bottom: 1.25rem;">
                    <strong style="display: block; font-size: 1rem; color: var(--mb-text-main); margin-bottom: 0.25rem;">
                        {{ $event->venue_name }}
                    </strong>
                    <p class="mb-body-soft" style="font-size: 0.85rem;">
                        {{ $event->address }}
                    </p>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                    <a
                        href="{{ $event->googleMapsUrl() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mb-btn mb-btn--sm"
                    >
                        {{ __('invitation.events.view_location') }}
                    </a>
                    <a
                        href="{{ $event->googleCalendarUrl($invitation) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mb-btn mb-btn--ghost mb-btn--sm"
                    >
                        {{ __('invitation.events.save_calendar') }}
                    </a>
                    <a
                        href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]) }}"
                        class="mb-btn mb-btn--ghost mb-btn--sm"
                    >
                        .ICS
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>
