@php
    $galleryItems = $invitation->resolvedGalleryItems(12);
@endphp

@if(count($galleryItems) > 0)
<section id="section-gallery" class="iv-section iv-gallery" aria-label="Galeri Momen">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-b.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-b.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-b@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-c.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-c.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-c@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.gallery.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ $invitation->gallery_title ?: __('invitation.gallery.heading') }}</h2>

        @if($invitation->gallery_quote)
            <p class="iv-section-subtitle">&ldquo;{{ $invitation->gallery_quote }}&rdquo;</p>
        @endif

        <div class="iv-divider-sprig" aria-hidden="true">
            <img
                src="{{ asset('themes/ivory-emboss/relief/divider-sprig.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/divider-sprig.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/divider-sprig@2x.webp') }} 2x"
                alt=""
                width="640"
                height="120"
                loading="lazy"
                decoding="async"
            >
        </div>

        <div class="iv-gallery-grid">
            @foreach($galleryItems as $index => $item)
                <figure class="iv-gallery-item" data-gallery-index="{{ $index }}">
                    <img
                        src="{{ $item['thumb'] }}"
                        data-full="{{ $item['full'] }}"
                        alt="{{ $item['caption'] ?: 'Foto Galeri ' . $invitation->title }}"
                        class="iv-gallery-img"
                        width="320"
                        height="400"
                        loading="lazy"
                    >
                </figure>
            @endforeach
        </div>
    </div>

    <div id="iv-gallery-lightbox" class="iv-lightbox" role="dialog" aria-modal="true" aria-label="Pratinjau Foto" hidden>
        <div class="iv-lightbox-backdrop" data-lightbox-close></div>
        <div class="iv-lightbox-dialog">
            <button type="button" class="iv-lightbox-close" data-lightbox-close aria-label="{{ __('invitation.gallery.close_lightbox') }}">
                &times;
            </button>
            <button type="button" class="iv-lightbox-nav iv-lightbox-nav--prev" data-lightbox-prev aria-label="{{ __('invitation.gallery.prev_photo') }}">
                &#8249;
            </button>
            <img src="" alt="" class="iv-lightbox-img" id="iv-lightbox-img">
            <button type="button" class="iv-lightbox-nav iv-lightbox-nav--next" data-lightbox-next aria-label="{{ __('invitation.gallery.next_photo') }}">
                &#8250;
            </button>
            <p class="iv-lightbox-caption" id="iv-lightbox-caption"></p>
        </div>
    </div>
</section>
@endif
