@php
    $galleryItems = $invitation->resolvedGalleryItems(12);
@endphp

@if(count($galleryItems) > 0)
<section id="section-gallery" class="sm-section sm-section-gallery">
    <div class="sm-decor-cloud-center" aria-hidden="true">
        <x-sunny-meadow.svg name="cloud" class="sm-svg-cloud-decor" />
    </div>

    <div class="sm-section-header">
        <p class="sm-section-subtitle">{{ __('invitation.gallery.heading') }}</p>
        <h2 class="sm-section-title">{{ $invitation->gallery_title ?: __('invitation.gallery.heading') }}</h2>
    </div>

    @if($invitation->gallery_quote)
        <p class="sm-gallery-quote">{{ $invitation->gallery_quote }}</p>
    @endif

    <div class="sm-gallery-grid" id="sm-gallery-grid">
        @foreach($galleryItems as $index => $item)
            @php
                $rotations = ['-1.5deg', '1.8deg', '-2.2deg', '1.2deg', '-0.8deg', '2.5deg'];
                $rot = $rotations[$index % count($rotations)];
            @endphp
            <button
                type="button"
                class="sm-gallery-item"
                data-gallery-index="{{ $index }}"
                data-full="{{ $item['full'] }}"
                data-caption="{{ $item['caption'] }}"
                style="--sm-item-rot: {{ $rot }};"
                aria-label="{{ $item['caption'] ?: ('Foto ' . ($index + 1)) }}"
            >
                <img
                    src="{{ $item['thumb'] }}"
                    alt="{{ $item['caption'] ?: ('Galeri pernikahan ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname . ' #' . ($index + 1)) }}"
                    width="{{ $item['width'] }}"
                    height="{{ $item['height'] }}"
                    loading="lazy"
                    decoding="async"
                >
            </button>
        @endforeach
    </div>

    <div
        id="sm-lightbox"
        class="sm-lightbox"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('invitation.gallery.heading') }}"
        hidden
    >
        <div class="sm-lightbox-backdrop" data-lightbox-close></div>
        <div class="sm-lightbox-dialog">
            <button
                type="button"
                id="sm-lightbox-close"
                class="sm-lightbox-close"
                aria-label="{{ __('invitation.gallery.close_lightbox') }}"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                </svg>
            </button>

            <div class="sm-lightbox-stage">
                <button
                    type="button"
                    id="sm-lightbox-prev"
                    class="sm-lightbox-nav sm-lightbox-nav--prev"
                    aria-label="{{ __('invitation.gallery.prev_photo') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22" aria-hidden="true">
                        <path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <figure class="sm-lightbox-figure">
                    <img id="sm-lightbox-img" src="" alt="">
                    <figcaption id="sm-lightbox-caption" class="sm-lightbox-caption"></figcaption>
                </figure>

                <button
                    type="button"
                    id="sm-lightbox-next"
                    class="sm-lightbox-nav sm-lightbox-nav--next"
                    aria-label="{{ __('invitation.gallery.next_photo') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22" aria-hidden="true">
                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <p id="sm-lightbox-counter" class="sm-lightbox-counter"></p>
        </div>
    </div>
</section>
@endif
