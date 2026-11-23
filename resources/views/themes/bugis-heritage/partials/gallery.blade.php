@php
    $galleryItems = $invitation->resolvedGalleryItems(12);
@endphp

@if(count($galleryItems) > 0)
    <section id="section-gallery" class="bh-section bh-gallery-section" aria-label="Galeri Foto Mempelai">
        <div class="bh-divider" aria-hidden="true">
            <x-bugis-heritage.svg name="divider-diamond" />
        </div>

        <p class="bh-kicker">{{ __('invitation.gallery.kicker') }}</p>
        <h2 class="bh-display bh-section-title">{{ $invitation->gallery_title ?: __('invitation.gallery.heading') }}</h2>

        @if($invitation->gallery_quote)
            <p class="bh-body-serif bh-gallery-quote">
                &ldquo;{{ $invitation->gallery_quote }}&rdquo;
            </p>
        @endif

        <div class="bh-gallery-grid">
            @foreach($galleryItems as $index => $item)
                <figure class="bh-gallery-item" data-index="{{ $index }}">
                    <img
                        src="{{ $item['thumb'] }}"
                        data-full="{{ $item['full'] }}"
                        alt="{{ $item['caption'] ?: 'Foto Galeri ' . $invitation->title }}"
                        class="bh-gallery-img"
                        loading="lazy"
                    >
                    @if(!empty($item['caption']))
                        <figcaption class="bh-gallery-caption">{{ $item['caption'] }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>

        {{-- Lightbox modal --}}
        <div id="bh-gallery-lightbox" class="bh-lightbox" aria-hidden="true" role="dialog" aria-modal="true" hidden>
            <div class="bh-lightbox-backdrop"></div>
            <div class="bh-lightbox-container">
                <button type="button" class="bh-lightbox-close" aria-label="Tutup Pratinjau Foto">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <img src="" alt="" class="bh-lightbox-img" id="bh-lightbox-target">
                <p class="bh-lightbox-caption" id="bh-lightbox-caption"></p>
            </div>
        </div>
    </section>
@endif
