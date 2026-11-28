<section id="section-gift" class="iv-section iv-gift" aria-label="Tanda Kasih">
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
            src="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-b@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        @include('themes.ivory-emboss.partials.gift-content')
    </div>
</section>
