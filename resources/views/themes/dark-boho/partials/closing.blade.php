@php
    $illustrationMode = data_get($themeConfig, 'layout.illustrationMode', 'illustrated');
@endphp
<footer id="section-closing" class="db-section db-section--deep db-closing db-reveal-section">
    @if($illustrationMode !== 'minimal' || !empty($isPreview))
        <div class="db-closing-pampas db-closing-pampas--left" aria-hidden="true" @if($illustrationMode === 'minimal') hidden @endif>
            <x-dark-boho.svg name="pampas-left" class="db-svg-pampas" />
        </div>
        <div class="db-closing-pampas db-closing-pampas--right" aria-hidden="true" @if($illustrationMode === 'minimal') hidden @endif>
            <x-dark-boho.svg name="pampas-right" class="db-svg-pampas" />
        </div>
    @endif

    <div class="db-divider db-divider--top" aria-hidden="true">
        <x-dark-boho.svg name="divider-boho" class="db-svg-divider" />
    </div>

    @if($invitation->closing_text)
        <p class="db-closing-text db-body-soft">{{ $invitation->closing_text }}</p>
    @endif

    <p class="db-script db-closing-names">
        {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
    </p>

    @if($invitation->coInviters->isNotEmpty())
        <div class="db-co-inviters">
            <p class="db-kicker">{{ __('invitation.closing.co_inviters_heading') }}</p>
            <ul class="db-co-inviters-list">
                @foreach($invitation->coInviters as $coInviter)
                    <li>{{ $coInviter->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="db-watermark">
        <p class="db-watermark-prompt">{{ __('invitation.closing.watermark_prompt') }}</p>
        <a
            href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="db-watermark-link"
            aria-label="{{ __('invitation.closing.watermark_prompt') }} {{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}"
        >
            <span>{{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}</span>
            <svg class="db-watermark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                <polyline points="15 3 21 3 21 9"></polyline>
                <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
        </a>
        <p class="db-closing-copyright">{{ __('invitation.closing.copyright_note') }}</p>
    </div>
</footer>
