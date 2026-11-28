<footer id="section-closing" class="iv-section iv-closing" aria-label="Penutup">
    <div class="iv-relief-corner iv-relief-corner--tl" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-tl.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-tl.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-tl@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-corner iv-relief-corner--br" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/corner-br.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/corner-br.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/corner-br@2x.webp') }} 2x"
            alt=""
            width="600"
            height="600"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">Terima Kasih</p>

        <div class="iv-closing-emblem" aria-hidden="true">
            <img
                src="{{ asset('themes/ivory-emboss/relief/closing-emblem.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/closing-emblem.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/closing-emblem@2x.webp') }} 2x"
                alt=""
                width="520"
                height="460"
                loading="lazy"
                decoding="async"
            >
        </div>

        <p class="iv-closing-text">
            {{ $invitation->closing_text ?: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kedua mempelai.' }}
        </p>

        <p class="iv-kicker" style="margin-top: 1.5rem;">Kami yang berbahagia,</p>
        <h3 class="iv-display iv-closing-names">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </h3>

        @if($invitation->coInviters->isNotEmpty())
            <div class="iv-co-inviters">
                <p class="iv-kicker" style="margin-bottom: 0.5rem;">{{ __('invitation.closing.co_inviters_heading') }}</p>
                <ul class="iv-co-inviters-list">
                    @foreach($invitation->coInviters as $coInviter)
                        <li>{{ $coInviter->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="iv-footer-band">
        <p class="iv-footer-prompt">{{ __('invitation.closing.watermark_prompt') }}</p>
        <a
            href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}?utm_source=invitation&amp;utm_medium=footer&amp;utm_campaign=ivory_emboss"
            target="_blank"
            rel="noopener noreferrer"
            class="iv-footer-link"
        >
            Undangan digital oleh {{ config('app.vendor_name', 'A2 Dev') }}
        </a>
        <div>
            <button type="button" id="iv-credits-btn" class="iv-footer-credits-btn">
                Informasi Kredit &amp; Lisensi
            </button>
        </div>
    </div>
</footer>
