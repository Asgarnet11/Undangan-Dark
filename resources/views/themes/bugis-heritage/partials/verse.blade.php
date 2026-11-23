<section id="section-verse" class="bh-section bh-verse-section" aria-label="Salam Pembuka dan Ayat Suci">
    <div class="bh-divider" aria-hidden="true">
        <x-bugis-heritage.svg name="divider-diamond" />
    </div>

    @if($invitation->opening_text)
        <p class="bh-body-serif bh-verse-intro">
            {{ $invitation->opening_text }}
        </p>
    @endif

    @if($invitation->verse_arabic || $invitation->verse_translation)
        <div class="bh-card bh-card--gold-border bh-verse-card">
            @if($invitation->verse_arabic)
                <p class="bh-arabic" lang="ar" dir="rtl">{{ $invitation->verse_arabic }}</p>
            @endif

            @if($invitation->verse_translation)
                <blockquote class="bh-body-serif bh-verse-translation">
                    &ldquo;{{ $invitation->verse_translation }}&rdquo;
                </blockquote>
            @endif

            @if($invitation->verse_source)
                <cite class="bh-kicker bh-verse-source">
                    &mdash; {{ $invitation->verse_source }}
                </cite>
            @endif
        </div>
    @endif
</section>
