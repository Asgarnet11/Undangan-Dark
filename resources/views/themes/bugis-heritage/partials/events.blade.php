@php
    $timezone = $invitation->resolvedTimezone();
@endphp

<section id="section-events" class="bh-section bh-events-section" aria-label="Rangkaian Acara Pernikahan">
    <div class="bh-divider" aria-hidden="true">
        <x-bugis-heritage.svg name="divider-diamond" />
    </div>

    <p class="bh-kicker">Rangkaian Acara</p>
    <h2 class="bh-display bh-section-title">Waktu &amp; Tempat</h2>

    <div class="bh-events-list">
        @foreach($invitation->events as $event)
            @php
                $startAt = $event->startsAtInTimezone($timezone);
                $endAt = $event->ends_at ? $event->ends_at->copy()->timezone($timezone)->locale('id') : null;
            @endphp
            <article class="bh-card bh-card--gold-border bh-event-card" style="margin-bottom: 1.5rem;">
                <h3 class="bh-display" style="font-size: 1.4rem; color: var(--bh-accent);">
                    {{ $event->title }}
                </h3>
                @if($event->starts_at)
                    <p class="bh-body-serif" style="margin: 0.5rem 0;">
                        {{ $startAt->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="bh-kicker" style="font-size: 0.8rem;">
                        {{ $startAt->format('H:i') }} - {{ $endAt ? $endAt->format('H:i') . ' WITA' : 'Selesai' }}
                    </p>
                @endif
                @if($event->venue_name)
                    <p class="bh-body-serif" style="margin-top: 0.75rem; font-weight: 600;">{{ $event->venue_name }}</p>
                @endif
                @if($event->address)
                    <p class="bh-body-serif" style="font-size: 0.85rem; opacity: 0.85;">{{ $event->address }}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
