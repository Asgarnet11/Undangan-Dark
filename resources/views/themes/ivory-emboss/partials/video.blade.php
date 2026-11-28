@php
    $videoId = null;
    if (!empty($invitation->video_url)) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/', $invitation->video_url, $m)) {
            $videoId = $m[1];
        }
    }
@endphp

@if($videoId)
<section id="section-video" class="iv-section iv-video" aria-label="Cuplikan Video">
    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.video.heading') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.video.heading') }}</h2>

        <div class="iv-card iv-video-facade" data-video-id="{{ $videoId }}">
            <button type="button" class="iv-video-trigger" aria-label="{{ __('invitation.video.play_label') }}">
                <span class="iv-btn">{{ __('invitation.video.play_label') }}</span>
            </button>
        </div>
    </div>
</section>
@endif
