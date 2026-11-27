@php
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
@endphp

<section id="section-events" class="bh-section bh-events-section" aria-label="Rangkaian Acara Pernikahan Adat">
    <h2 class="bh-display bh-section-title">{{ __('invitation.events.heading') }}</h2>
    <p class="bh-body-serif bh-section-subtitle">
        Dengan memohon berkah Allah SWT, berikut merupakan rangkaian prosesi pernikahan kami.
    </p>

    <div class="bh-events-list">
        @foreach($invitation->events as $event)
            @php
                $startAt = $event->startsAtInTimezone($timezone);
                $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;

                // Cultural verification gate for Adat Event Type
                $verifiedAdatType = ($event->adatEventType && $event->adatEventType->status === 'diverifikasi' && $event->adatEventType->is_published)
                    ? $event->adatEventType
                    : null;

                $eventHeading = $verifiedAdatType
                    ? ($event->display_label_override ?: $verifiedAdatType->label_display)
                    : $event->title;

                $isMappacci = $verifiedAdatType && (
                    str_contains(strtolower($verifiedAdatType->key), 'mappacci') ||
                    str_contains(strtolower($verifiedAdatType->label_display), 'mappacci')
                );
            @endphp

            <article class="bh-card bh-card--gold-border bh-event-card" style="margin-bottom: 2rem; position: relative;">
                @if($isMappacci)
                    <div class="bh-event-pacci-accent" aria-hidden="true" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; color: var(--bh-accent); opacity: 0.85;">
                        <x-bugis-heritage.svg name="pacci-leaf" />
                    </div>
                @endif

                <h3 class="bh-display bh-event-title" style="font-size: 1.6rem; color: var(--bh-accent); margin-bottom: 0.5rem;">
                    {{ $eventHeading }}
                </h3>

                <div class="bh-card-border-gold" aria-hidden="true" style="max-width: 220px; margin: 0.25rem 0 0.75rem; color: var(--bh-accent);">
                    <x-bugis-heritage.svg name="border-gold" />
                </div>

                @if($verifiedAdatType && $event->title !== $eventHeading)
                    <p class="bh-body-serif" style="font-size: 0.85rem; color: var(--bh-ink-soft); font-style: italic; margin-bottom: 0.5rem;">
                        {{ $event->title }}
                    </p>
                @endif

                <p class="bh-kicker" style="font-size: 0.85rem; color: var(--bh-gold-soft); margin-bottom: 0.25rem;">
                    {{ $startAt->translatedFormat('l, d F Y') }}
                </p>

                <p class="bh-body-serif" style="font-weight: 500; margin-bottom: 1rem;">
                    {{ $startAt->format('H.i') }} &ndash; {{ $endAt ? $endAt->format('H.i') : __('invitation.events.until_finished') }} {{ $tzAbbr }}
                </p>

                @if($verifiedAdatType && $verifiedAdatType->description_id)
                    <p class="bh-body-serif bh-event-adat-desc" style="font-size: 0.84rem; line-height: 1.6; color: var(--bh-ink-soft); max-width: 380px; margin: 0 auto 1.25rem; padding: 0.5rem 0.75rem; border-left: 2px solid var(--bh-accent); background: rgba(0,0,0,0.2); text-align: left;">
                        {{ $verifiedAdatType->description_id }}
                    </p>
                @endif

                <div class="bh-event-location" style="margin-bottom: 1.5rem;">
                    @if($event->venue_name)
                        <strong class="bh-display" style="display: block; font-size: 1.15rem; color: var(--bh-ink); margin-bottom: 0.25rem;">
                            {{ $event->venue_name }}
                        </strong>
                    @endif
                    @if($event->address)
                        <p class="bh-body-serif" style="font-size: 0.88rem; color: var(--bh-ink-soft);">
                            {{ $event->address }}
                        </p>
                    @endif
                </div>

                <div class="bh-event-actions" style="display: flex; gap: 0.65rem; justify-content: center; flex-wrap: wrap;">
                    @if($event->googleMapsUrl())
                        <a
                            href="{{ $event->googleMapsUrl() }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="bh-btn bh-btn--sm"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="14" height="14" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>{{ __('invitation.events.view_location') }}</span>
                        </a>
                    @endif
                    <a
                        href="{{ $event->googleCalendarUrl($invitation) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bh-btn bh-btn--sm"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="14" height="14" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>{{ __('invitation.events.save_calendar') }}</span>
                    </a>
                    <a
                        href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]) }}"
                        class="bh-btn bh-btn--sm"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="14" height="14" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>.ICS</span>
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    @include('themes.bugis-heritage.partials.glossary')
</section>
