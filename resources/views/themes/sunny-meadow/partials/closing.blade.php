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

    <p class="sm-closing-copyright">{{ __('invitation.closing.copyright_note') }}</p>
</footer>
