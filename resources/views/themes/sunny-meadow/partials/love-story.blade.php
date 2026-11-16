@php
    $chapters = $invitation->relationLoaded('loveStoryChapters') ? $invitation->loveStoryChapters : collect();
@endphp

@if($chapters->isNotEmpty() || !empty($isPreview))
<section id="section-love-story" class="sm-section sm-section-love-story">
    <div class="sm-section-header">
        <div class="sm-decor-flower-center" aria-hidden="true">
            <x-sunny-meadow.svg name="flower-small" class="sm-svg-flower-sm" />
        </div>
        <p class="sm-section-subtitle">Perjalanan Kisah Kami</p>
        <h2 class="sm-section-title">Kisah Cinta</h2>
    </div>

    <div class="sm-story-timeline">
        @foreach($chapters as $index => $chapter)
            <div class="sm-story-step">
                <div class="sm-story-marker" aria-hidden="true">
                    <x-sunny-meadow.svg name="heart-line" class="sm-svg-story-marker" />
                </div>
                <article class="sm-card sm-story-card">
                    @if($chapter->time_label)
                        <span class="sm-story-date">{{ $chapter->time_label }}</span>
                    @endif
                    <h3 class="sm-title-display sm-story-title">{{ $chapter->title }}</h3>
                    @if($chapter->photo_path)
                        <div class="sm-story-photo-wrap">
                            <img
                                src="{{ asset('storage/' . $chapter->photo_path) }}"
                                alt="{{ $chapter->title }}"
                                class="sm-story-photo"
                                loading="lazy"
                            >
                        </div>
                    @endif
                    <p class="sm-story-body">{{ $chapter->body }}</p>
                </article>
            </div>
        @endforeach
    </div>
</section>
@endif

