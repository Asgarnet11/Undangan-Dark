@props([
    'invitation',
    'sectionId' => 'section-love-story',
    'isPreview' => false,
])

@php
    $enabled = $invitation->isFeatureEnabled('love_story', true);
    $chapters = $invitation->relationLoaded('visibleLoveStoryChapters')
        ? $invitation->visibleLoveStoryChapters
        : ($invitation->relationLoaded('loveStoryChapters')
            ? $invitation->loveStoryChapters->filter(fn ($c) => ! isset($c->is_visible) || (bool) $c->is_visible)->values()
            : $invitation->visibleLoveStoryChapters()->get());
@endphp

@if($enabled && ($chapters->isNotEmpty() || $isPreview))
    <section
        id="{{ $sectionId }}"
        class="shared-love-story"
        aria-label="Kisah Cinta"
        style="padding: 3rem 1.5rem; position: relative;"
    >
        <div style="max-width: 440px; margin: 0 auto; text-align: center;">
            <h2 style="font-family: var(--inv-font-display, inherit); font-size: clamp(1.75rem, 5vw, 2.35rem); margin-bottom: 0.45rem; color: var(--inv-accent, currentColor);">
                {{ $invitation->love_story_title ?: 'Kisah Cinta' }}
            </h2>
            @if($invitation->love_story_intro)
                <p style="font-size: 0.95rem; opacity: 0.85; margin: 0 auto 2rem; line-height: 1.65;">
                    {{ $invitation->love_story_intro }}
                </p>
            @endif
        </div>

        @if($chapters->isNotEmpty())
            <div style="max-width: 440px; margin: 1.5rem auto 0; position: relative; padding-left: 1.5rem; border-left: 2px solid var(--inv-border, rgba(128, 128, 128, 0.3));">
                @foreach($chapters as $chapter)
                    <article style="position: relative; margin-bottom: 1.5rem; padding: 1.25rem; border-radius: var(--inv-radius, 12px); background: var(--inv-surface, rgba(128, 128, 128, 0.08)); border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.2)); text-align: left;">
                        <span aria-hidden="true" style="position: absolute; left: calc(-1.5rem - 6px); top: 1.4rem; width: 10px; height: 10px; border-radius: 9999px; background: var(--inv-accent, currentColor);"></span>
                        @if($chapter->time_label)
                            <p style="font-size: 0.82rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--inv-accent, currentColor); margin-bottom: 0.25rem;">
                                {{ $chapter->time_label }}
                            </p>
                        @endif
                        <h3 style="font-family: var(--inv-font-display, inherit); font-size: 1.25rem; margin-bottom: 0.5rem;">
                            {{ $chapter->title }}
                        </h3>
                        @if($chapter->photoUrl())
                            <div style="margin-bottom: 0.85rem; border-radius: 8px; overflow: hidden;">
                                <img
                                    src="{{ $chapter->photoUrl() }}"
                                    alt="{{ $chapter->title }}"
                                    style="width: 100%; aspect-ratio: 16 / 10; object-fit: cover; display: block;"
                                    loading="lazy"
                                >
                            </div>
                        @endif
                        <p style="font-size: 0.92rem; line-height: 1.7; opacity: 0.9; white-space: pre-line;">{{ $chapter->body }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endif
