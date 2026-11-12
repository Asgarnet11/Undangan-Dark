@php
    $streamUrl = trim((string) $invitation->livestream_url);
    $isSafeUrl = preg_match('/^https?:\/\//i', $streamUrl) === 1;
@endphp

@if($isSafeUrl)
<section id="section-livestream" class="db-section db-section--deep db-reveal-section">
    <p class="db-kicker">{{ __('invitation.livestream.heading') }}</p>
    <h2 class="db-display db-section-title">
        {{ $invitation->livestream_title ?: __('invitation.livestream.heading') }}
    </h2>

    <div class="db-card db-card--arch db-livestream-card">
        @if($invitation->livestream_note)
            <p class="db-body-soft db-livestream-note">{{ $invitation->livestream_note }}</p>
        @endif

        <a
            href="{{ $streamUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="db-btn"
        >
            {{ __('invitation.livestream.watch_button') }}
        </a>
    </div>
</section>
@endif
