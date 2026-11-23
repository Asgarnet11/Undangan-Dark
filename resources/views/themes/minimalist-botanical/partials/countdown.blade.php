@php
    $primaryEvent = $invitation->events->first();
    $targetTimestamp = $primaryEvent ? $primaryEvent->starts_at?->timestamp ?? 0 : 0;
@endphp

<section id="section-countdown" class="mb-section mb-section--soft mb-reveal">
    <div style="max-width: 32px; margin: 0 auto 0.75rem; color: var(--mb-primary); opacity: 0.8;" aria-hidden="true">
        <x-minimalist-botanical.svg name="leaf-accent" />
    </div>

    <p class="mb-kicker">{{ __('invitation.countdown.kicker') }}</p>
    <h2 class="mb-display mb-section-title">{{ __('invitation.countdown.heading') }}</h2>

    <div id="mb-countdown" class="mb-countdown-grid" data-timestamp="{{ $targetTimestamp }}">
        <div class="mb-countdown-box">
            <span id="mb-days" class="mb-countdown-number">00</span>
            <span class="mb-countdown-label">{{ __('invitation.countdown.days') }}</span>
        </div>
        <div class="mb-countdown-box">
            <span id="mb-hours" class="mb-countdown-number">00</span>
            <span class="mb-countdown-label">{{ __('invitation.countdown.hours') }}</span>
        </div>
        <div class="mb-countdown-box">
            <span id="mb-minutes" class="mb-countdown-number">00</span>
            <span class="mb-countdown-label">{{ __('invitation.countdown.minutes') }}</span>
        </div>
        <div class="mb-countdown-box">
            <span id="mb-seconds" class="mb-countdown-number">00</span>
            <span class="mb-countdown-label">{{ __('invitation.countdown.seconds') }}</span>
        </div>
    </div>
</section>
