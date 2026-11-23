@php
    $galleryItems = $invitation->resolvedGalleryItems(12);
@endphp

@if(count($galleryItems) > 0)
    <section id="section-gallery" class="mb-section mb-reveal">
        <p class="mb-kicker">{{ __('invitation.gallery.kicker') }}</p>
        <h2 class="mb-display mb-section-title">{{ $invitation->gallery_title ?: __('invitation.gallery.heading') }}</h2>

        @if($invitation->gallery_quote)
            <p class="mb-body-soft" style="font-style: italic; max-width: 380px; margin: 0 auto 1.5rem;">
                &ldquo;{{ $invitation->gallery_quote }}&rdquo;
            </p>
        @endif

        <div class="mb-divider-wrap" aria-hidden="true">
            <x-minimalist-botanical.svg name="botanical-divider" />
        </div>

        <div class="mb-gallery-grid">
            @foreach($galleryItems as $item)
                <figure class="mb-gallery-item">
                    <img src="{{ $item['thumb'] }}" alt="{{ $item['caption'] ?: 'Foto Galeri ' . $invitation->title }}" loading="lazy">
                </figure>
            @endforeach
        </div>
    </section>
@endif
