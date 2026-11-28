@php
    $enabled = $invitation->isFeatureEnabled('love_story', true);
    $chapters = $invitation->relationLoaded('visibleLoveStoryChapters')
        ? $invitation->visibleLoveStoryChapters
        : ($invitation->relationLoaded('loveStoryChapters')
            ? $invitation->loveStoryChapters->filter(fn ($c) => ! isset($c->is_visible) || (bool) $c->is_visible)->values()
            : $invitation->visibleLoveStoryChapters()->get());
@endphp

@if($enabled && ($chapters->isNotEmpty() || !empty($isPreview)))
<section id="section-love-story" class="iv-section iv-love-story" aria-label="Kisah Cinta">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-a.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-a.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-a@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-a.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-a.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-a@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">Perjalanan Kami</p>
        <h2 class="iv-display iv-section-title">Kisah Cinta</h2>

        <div class="iv-story-arch-wrap">
            <img
                class="iv-arch-panel-bg"
                src="{{ asset('themes/ivory-emboss/relief/arch-panel.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/arch-panel.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/arch-panel@2x.webp') }} 2x"
                alt=""
                aria-hidden="true"
                width="720"
                height="960"
                loading="lazy"
                decoding="async"
            >
            <div class="iv-story-arch-inner">
                <x-shared.love-story :invitation="$invitation" :is-preview="!empty($isPreview)" />
            </div>
        </div>
    </div>
</section>
@endif
