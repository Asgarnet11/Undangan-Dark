@php
    $videoId = $invitation->youtubeVideoId();
@endphp

@if($videoId)
<section id="section-video" class="db-section db-section--deep db-reveal-section">
    <p class="db-kicker">{{ __('invitation.video.heading') }}</p>

    <div class="db-video-facade" data-youtube-id="{{ $videoId }}">
        <img
            src="https://i.ytimg.com/vi/{{ $videoId }}/hqdefault.jpg"
            alt="{{ __('invitation.video.heading') }} - {{ $invitation->title }}"
            class="db-video-thumb"
            width="480"
            height="360"
            loading="lazy"
            decoding="async"
        >
        <button
            type="button"
            class="db-video-play"
            aria-label="{{ __('invitation.video.play_label') }}"
        >
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true">
                <path d="M8 5.5v13l10.5-6.5L8 5.5z" />
            </svg>
        </button>
    </div>
</section>
@endif
