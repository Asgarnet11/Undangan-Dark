@props([
    'event',
    'invitation',
])

@php
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
    $startAt = $event->startsAtInTimezone($timezone);
    $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;
@endphp

<article class="shared-event-card" style="padding: 1.5rem; border-radius: var(--inv-radius, 12px); border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.22)); background: var(--inv-surface, rgba(128, 128, 128, 0.06)); text-align: center; margin-bottom: 1.25rem;">
    <h3 style="font-family: var(--inv-font-display, inherit); font-size: 1.4rem; margin-bottom: 0.5rem;">
        {{ $event->title }}
    </h3>
    <p style="font-size: 0.92rem; font-weight: 600; margin-bottom: 0.2rem;">
        {{ $startAt->translatedFormat('l, d F Y') }}
    </p>
    <p style="font-size: 0.9rem; opacity: 0.85; margin-bottom: 0.9rem;">
        {{ $startAt->format('H.i') }} &ndash; {{ $endAt ? $endAt->format('H.i') : __('invitation.events.until_finished') }} {{ $tzAbbr }}
    </p>
    @if($event->venue_name)
        <p style="font-weight: 600; font-size: 1rem; margin-bottom: 0.2rem;">{{ $event->venue_name }}</p>
    @endif
    @if($event->address)
        <p style="font-size: 0.88rem; opacity: 0.85; margin-bottom: 1.15rem;">{{ $event->address }}</p>
    @endif
    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
        <a
            href="{{ $event->googleMapsUrl() }}"
            target="_blank"
            rel="noopener noreferrer"
            style="display: inline-flex; align-items: center; min-height: 44px; padding: 0.5rem 1.1rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.85rem; color: inherit;"
        >
            {{ __('invitation.events.view_location') }}
        </a>
        <a
            href="{{ $event->googleCalendarUrl($invitation) }}"
            target="_blank"
            rel="noopener noreferrer"
            style="display: inline-flex; align-items: center; min-height: 44px; padding: 0.5rem 1.1rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.85rem; color: inherit;"
        >
            {{ __('invitation.events.save_calendar') }}
        </a>
        <a
            href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]) }}"
            style="display: inline-flex; align-items: center; min-height: 44px; padding: 0.5rem 1.1rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.85rem; color: inherit;"
        >
            .ICS
        </a>
    </div>
</article>
