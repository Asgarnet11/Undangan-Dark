<footer id="section-closing" class="sm-section sm-section--deep sm-closing">
    <div class="sm-wave-divider sm-wave-divider--top" aria-hidden="true">
        <x-sunny-meadow.svg name="wave-divider" class="sm-svg-wave" />
    </div>

    <!-- Decorative Corner Flowers in Deep Footer -->
    <div class="sm-closing-ornament sm-closing-ornament--tl" aria-hidden="true">
        <x-sunny-meadow.svg name="corner-tl" class="sm-svg-corner" />
    </div>
    <div class="sm-closing-ornament sm-closing-ornament--br" aria-hidden="true">
        <x-sunny-meadow.svg name="corner-br" class="sm-svg-corner" />
    </div>

    <div class="sm-decor-heart-center" aria-hidden="true">
        <x-sunny-meadow.svg name="heart-line" class="sm-svg-heart-closing" />
    </div>

    @if($invitation->closing_text)
        <p class="sm-closing-text">{{ $invitation->closing_text }}</p>
    @endif

    <p class="sm-title-script sm-closing-names">
        {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
    </p>

    @if($invitation->coInviters->isNotEmpty())
        <div class="sm-co-inviters">
            <p class="sm-co-inviters-heading">{{ __('invitation.closing.co_inviters_heading') }}</p>
            <ul class="sm-co-inviters-list">
                @foreach($invitation->coInviters as $coInviter)
                    <li>{{ $coInviter->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sm-watermark">
        <p class="sm-watermark-prompt">{{ __('invitation.closing.watermark_prompt') }}</p>
        <a
            href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="sm-watermark-link"
            aria-label="{{ __('invitation.closing.watermark_prompt') }} {{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}"
        >
            <span>{{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}</span>
            <svg class="sm-watermark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                <polyline points="15 3 21 3 21 9"></polyline>
                <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
        </a>
        <p class="sm-closing-copyright">{{ __('invitation.closing.copyright_note') }}</p>
    </div>
</footer>

