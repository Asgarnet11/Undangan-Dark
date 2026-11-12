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
</footer>
