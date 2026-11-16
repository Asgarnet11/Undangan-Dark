@php
    $streamUrl = trim((string) $invitation->livestream_url);
    $isSafeUrl = preg_match('/^https?:\/\//i', $streamUrl) === 1;
@endphp

@if($isSafeUrl)
<section id="section-livestream" class="sm-section sm-section-livestream">
    <p class="sm-section-subtitle">{{ __('invitation.livestream.heading') }}</p>
    <h2 class="sm-section-title">
        {{ $invitation->livestream_title ?: __('invitation.livestream.heading') }}
    </h2>

    <div class="sm-card sm-livestream-card">
        @if($invitation->livestream_note)
            <p class="sm-livestream-note">{{ $invitation->livestream_note }}</p>
        @endif

        <a
            href="{{ $streamUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="sm-btn sm-btn-primary"
        >
            <x-sunny-meadow.svg name="star-small" class="sm-btn-star" />
            <span>{{ __('invitation.livestream.watch_button') }}</span>
        </a>
    </div>
</section>
@endif

