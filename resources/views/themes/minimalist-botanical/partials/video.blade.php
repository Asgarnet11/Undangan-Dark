@php
    $youtubeId = $invitation->youtubeVideoId();
@endphp

@if($youtubeId)
    <section id="section-video" class="mb-section mb-section--soft mb-reveal">
        <p class="mb-kicker">{{ __('invitation.video.kicker') }}</p>
        <h2 class="mb-display mb-section-title">{{ __('invitation.video.heading') }}</h2>

        <div style="position: relative; width: 100%; aspect-ratio: 16 / 9; border-radius: 1rem; overflow: hidden; box-shadow: var(--mb-shadow-sm); margin-top: 1.5rem;">
            <iframe
                src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}"
                title="Video Pernikahan {{ $invitation->title }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                loading="lazy"
            ></iframe>
        </div>
    </section>
@endif
