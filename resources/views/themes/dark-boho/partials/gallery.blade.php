@php
    $galleryItems = $invitation->resolvedGalleryItems(12);
@endphp

@if(count($galleryItems) > 0)
<section id="section-gallery" class="db-section db-reveal-section">
    <div class="db-ornament-branch" aria-hidden="true">
        <x-dark-boho.svg name="leaf-branch" class="db-svg-branch" />
    </div>

    <p class="db-kicker">{{ __('invitation.gallery.kicker') }}</p>
    <h2 class="db-display db-section-title">{{ $invitation->gallery_title ?: __('invitation.gallery.heading') }}</h2>

    @if($invitation->gallery_quote)
        <p class="db-gallery-quote db-body-soft">{{ $invitation->gallery_quote }}</p>
    @endif

    <div class="db-gallery-grid" id="db-gallery-grid">
        @foreach($galleryItems as $index => $item)
            <button
                type="button"
                class="db-gallery-item"
                data-gallery-index="{{ $index }}"
                data-full="{{ $item['full'] }}"
                data-caption="{{ $item['caption'] }}"
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
        id="db-lightbox"
        class="db-lightbox"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('invitation.gallery.heading') }}"
        hidden
    >
        <div class="db-lightbox-backdrop"></div>
        <div class="db-lightbox-dialog">
            <button
                type="button"
                id="db-lightbox-close"
                class="db-lightbox-close"
                aria-label="{{ __('invitation.gallery.close_lightbox') }}"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                </svg>
            </button>

            <div class="db-lightbox-stage">
                <button
                    type="button"
                    id="db-lightbox-prev"
                    class="db-lightbox-nav db-lightbox-nav--prev"
                    aria-label="{{ __('invitation.gallery.prev_photo') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22" aria-hidden="true">
                        <path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <figure class="db-lightbox-figure">
                    <img id="db-lightbox-img" src="" alt="">
                    <figcaption id="db-lightbox-caption" class="db-lightbox-caption"></figcaption>
                </figure>

                <button
                    type="button"
                    id="db-lightbox-next"
                    class="db-lightbox-nav db-lightbox-nav--next"
                    aria-label="{{ __('invitation.gallery.next_photo') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22" aria-hidden="true">
                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <p id="db-lightbox-counter" class="db-lightbox-counter"></p>
        </div>
    </div>
</section>
@endif
