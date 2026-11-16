@php
    $firstEvent = $invitation->events->first();
    $timezone = $invitation->resolvedTimezone();
    $targetIso = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->toIso8601String() : '';
    $alreadyElapsed = $firstEvent ? $firstEvent->startsAtInTimezone($timezone)->isPast() : false;
@endphp

<section id="section-countdown" class="sm-section sm-section-countdown">
    <div class="sm-section-header">
        <div class="sm-decor-flower-center" aria-hidden="true">
            <x-sunny-meadow.svg name="flower-small" class="sm-svg-flower-sm" />
        </div>
        <p class="sm-section-subtitle">{{ __('invitation.countdown.heading') }}</p>
        <h2 class="sm-section-title">{{ $invitation->opening_title ?: 'Menghitung Hari Bahagia' }}</h2>
    </div>

    @if($invitation->opening_text)
        <p class="sm-opening-copy">{{ $invitation->opening_text }}</p>
    @endif

    @if($firstEvent)
        <div
            id="sm-countdown"
            class="sm-countdown"
            data-target="{{ $targetIso }}"
            data-elapsed-text="{{ __('invitation.countdown.elapsed') }}"
        >
            @if($alreadyElapsed)
                <p class="sm-countdown-elapsed" id="sm-countdown-elapsed">{{ __('invitation.countdown.elapsed') }}</p>
            @else
                <div class="sm-countdown-grid" id="sm-countdown-grid">
                    <div class="sm-countdown-box sm-countdown-box--days">
                        <strong class="sm-countdown-num" data-unit="days">00</strong>
                        <span class="sm-countdown-label">{{ __('invitation.countdown.days') }}</span>
                    </div>
                    <div class="sm-countdown-box sm-countdown-box--hours">
                        <strong class="sm-countdown-num" data-unit="hours">00</strong>
                        <span class="sm-countdown-label">{{ __('invitation.countdown.hours') }}</span>
                    </div>
                    <div class="sm-countdown-box sm-countdown-box--minutes">
                        <strong class="sm-countdown-num" data-unit="minutes">00</strong>
                        <span class="sm-countdown-label">{{ __('invitation.countdown.minutes') }}</span>
                    </div>
                    <div class="sm-countdown-box sm-countdown-box--seconds">
                        <strong class="sm-countdown-num" data-unit="seconds">00</strong>
                        <span class="sm-countdown-label">{{ __('invitation.countdown.seconds') }}</span>
                    </div>
                </div>
                <p class="sm-countdown-elapsed" id="sm-countdown-elapsed" hidden>{{ __('invitation.countdown.elapsed') }}</p>
            @endif
        </div>

        <div class="sm-actions-row">
            <a
                href="{{ $firstEvent->googleCalendarUrl($invitation) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="sm-btn sm-btn-primary"
            >
                <x-sunny-meadow.svg name="star-small" class="sm-btn-star" />
                <span>{{ __('invitation.countdown.save_date') }}</span>
            </a>
            <a
                href="{{ route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $firstEvent->id]) }}"
                class="sm-btn sm-btn-outline"
            >
                {{ __('invitation.events.download_ics') }}
            </a>
        </div>
    @endif
</section>

