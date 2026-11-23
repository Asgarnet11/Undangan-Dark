<footer id="section-closing" class="bh-section bh-closing-section" aria-label="Penutup dan Doa">
    <div class="bh-closing-card bh-card bh-card--gold-border">
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tl" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tr" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--bl" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--br" />

        <div class="bh-closing-silhouette" aria-hidden="true">
            <x-bugis-heritage.svg name="rumah-panggung" />
        </div>

        @if($invitation->closing_text)
            <p class="bh-body-serif bh-closing-text">
                &ldquo;{{ $invitation->closing_text }}&rdquo;
            </p>
        @else
            <p class="bh-body-serif bh-closing-text">
                Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kedua mempelai.
            </p>
        @endif

        <p class="bh-kicker" style="margin-top: 1.5rem;">Kami yang berbahagia,</p>

        <h3 class="bh-display bh-closing-names">
            {{ $invitation->bride_nickname }} <span class="bh-script">&amp;</span> {{ $invitation->groom_nickname }}
        </h3>

        @if($invitation->coInviters->isNotEmpty())
            <div class="bh-co-inviters">
                <p class="bh-kicker">{{ __('invitation.closing.co_inviters_heading') }}</p>
                <ul class="bh-co-inviters-list">
                    @foreach($invitation->coInviters as $coInviter)
                        <li class="bh-body-serif">{{ $coInviter->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bh-closing-credit">
            <p class="bh-credit-prompt">{{ __('invitation.closing.watermark_prompt') }}</p>
            <a
                href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="bh-credit-link"
            >
                {{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}
            </a>
            <p class="bh-credit-brand">{{ config('app.name', 'Undangan Pernikahan Digital') }}</p>
        </div>
    </div>
</footer>
