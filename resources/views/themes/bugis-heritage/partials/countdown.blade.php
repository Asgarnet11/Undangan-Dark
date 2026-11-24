@php
    $primaryEvent = $invitation->events->first();
    $targetTimestamp = $primaryEvent ? $primaryEvent->starts_at?->timestamp ?? 0 : 0;
@endphp

<section id="section-countdown" class="bh-section bh-countdown-section" aria-label="Hitung Mundur Waktu Menuju Acara">
    <h2 class="bh-display bh-section-title">{{ __('invitation.countdown.heading') }}</h2>

    <div id="bh-countdown" class="bh-countdown-grid" data-timestamp="{{ $targetTimestamp }}">
        <div class="bh-countdown-box">
            <span id="bh-days" class="bh-countdown-number" data-unit="days">00</span>
            <span class="bh-countdown-label">{{ mb_strtolower(__('invitation.countdown.days')) }}</span>
        </div>
        <div class="bh-countdown-box">
            <span id="bh-hours" class="bh-countdown-number" data-unit="hours">00</span>
            <span class="bh-countdown-label">{{ mb_strtolower(__('invitation.countdown.hours')) }}</span>
        </div>
        <div class="bh-countdown-box">
            <span id="bh-minutes" class="bh-countdown-number" data-unit="minutes">00</span>
            <span class="bh-countdown-label">{{ mb_strtolower(__('invitation.countdown.minutes')) }}</span>
        </div>
        <div class="bh-countdown-box">
            <span id="bh-seconds" class="bh-countdown-number" data-unit="seconds">00</span>
            <span class="bh-countdown-label">{{ mb_strtolower(__('invitation.countdown.seconds')) }}</span>
        </div>
    </div>

    <div id="bh-countdown-elapsed" class="bh-card bh-countdown-elapsed" hidden>
        <p class="bh-display" style="font-size: 1.25rem; color: var(--bh-accent);">
            Alhamdulillah, rangkaian acara telah dimulai / selesai diselenggarakan.
        </p>
    </div>
</section>
