@php
    $firstEvent = $invitation->events->first();
    $timezone = $invitation->resolvedTimezone();
    $targetIso = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->toIso8601String() : '';
    $alreadyElapsed = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->isPast() : false;
@endphp

<section id="section-countdown" class="db-section db-reveal-section">
    <p class="db-kicker">{{ __('invitation.countdown.heading') }}</p>

    @if($invitation->opening_text)
        <p class="db-opening-copy db-body-soft">{{ $invitation->opening_text }}</p>
    @endif

    @if($firstEvent)
        <div
            id="db-countdown"
            class="db-countdown"
            data-target="{{ $targetIso }}"
            data-elapsed-text="{{ __('invitation.countdown.elapsed') }}"
        >
            @if($alreadyElapsed)
                <p class="db-countdown-elapsed" id="db-countdown-elapsed">{{ __('invitation.countdown.elapsed') }}</p>
            @else
                <div class="db-countdown-grid" id="db-countdown-grid">
                    <div class="db-countdown-box">
                        <strong class="db-countdown-num" data-unit="days">00</strong>
                        <span class="db-countdown-label">{{ __('invitation.countdown.days') }}</span>
                    </div>
                    <div class="db-countdown-box">
                        <strong class="db-countdown-num" data-unit="hours">00</strong>
                        <span class="db-countdown-label">{{ __('invitation.countdown.hours') }}</span>
                    </div>
                    <div class="db-countdown-box">
                        <strong class="db-countdown-num" data-unit="minutes">00</strong>
                        <span class="db-countdown-label">{{ __('invitation.countdown.minutes') }}</span>
                    </div>
                    <div class="db-countdown-box">
                        <strong class="db-countdown-num" data-unit="seconds">00</strong>
                        <span class="db-countdown-label">{{ __('invitation.countdown.seconds') }}</span>
                    </div>
                </div>
                <p class="db-countdown-elapsed" id="db-countdown-elapsed" hidden>{{ __('invitation.countdown.elapsed') }}</p>
            @endif
        </div>

        <div class="db-actions-row">
            <a
                href="{{ $firstEvent->googleCalendarUrl($invitation) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="db-btn"
            >
                {{ __('invitation.countdown.save_date') }}
            </a>
            <a
                href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $firstEvent->id]) }}"
                class="db-btn db-btn--ghost"
            >
                {{ __('invitation.events.download_ics') }}
            </a>
        </div>
    @endif

    <div class="db-divider" aria-hidden="true">
        <x-dark-boho.svg name="divider-boho" class="db-svg-divider" />
    </div>
</section>
