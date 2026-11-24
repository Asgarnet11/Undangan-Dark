@php
    $chapters = $invitation->relationLoaded('loveStoryChapters') ? $invitation->loveStoryChapters : collect();
@endphp

@if($chapters->isNotEmpty() || !empty($isPreview))
<section id="section-love-story" class="bh-section bh-love-story-section" aria-label="Kisah Perjalanan Cinta">
    <h2 class="bh-display bh-section-title">Kisah Cinta &amp; Perjalanan Kasih</h2>
    <p class="bh-body-serif bh-section-subtitle">
        Untaian cerita dan langkah kebersamaan kami menuju ikatan suci pernikahan.
    </p>

    <div class="bh-story-timeline">
        <div class="bh-story-line" aria-hidden="true"></div>

        @foreach($chapters as $index => $chapter)
            <div class="bh-story-step">
                <div class="bh-story-marker" aria-hidden="true">
                    <x-bugis-heritage.svg name="divider-diamond" />
                </div>
                <article class="bh-card bh-card--gold-border bh-story-card">
                    @if($chapter->time_label)
                        <span class="bh-kicker bh-story-date">{{ $chapter->time_label }}</span>
                    @endif
                    <h3 class="bh-display bh-story-title" style="font-size: 1.35rem; color: var(--bh-accent); margin: 0.35rem 0 0.75rem;">
                        {{ $chapter->title }}
                    </h3>
                    @if($chapter->photo_path)
                        <div class="bh-story-photo-wrap" style="margin-bottom: 1rem; border-radius: var(--bh-radius); overflow: hidden; border: 1px solid var(--bh-line);">
                            <img
                                src="{{ asset('storage/' . $chapter->photo_path) }}"
                                alt="{{ $chapter->title }}"
                                class="bh-story-photo"
                                style="width: 100%; height: auto; display: block;"
                                loading="lazy"
                            >
                        </div>
                    @endif
                    <p class="bh-body-serif bh-story-body" style="font-size: 0.9rem; line-height: 1.7; color: var(--bh-ink-soft);">
                        {{ $chapter->body }}
                    </p>
                </article>
            </div>
        @endforeach
    </div>
</section>
@endif
