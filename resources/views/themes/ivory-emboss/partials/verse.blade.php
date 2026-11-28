<section id="section-verse" class="iv-section iv-verse" aria-label="Pembuka dan Ayat">
    <div class="iv-safe-zone">
        @if($invitation->opening_text)
            <p class="iv-verse-intro">{{ $invitation->opening_text }}</p>
        @endif

        @if($invitation->verse_arabic || $invitation->verse_translation)
            <div class="iv-card iv-verse-card">
                @if($invitation->verse_arabic)
                    <p class="iv-arabic iv-verse-arabic" lang="ar" dir="rtl">{{ $invitation->verse_arabic }}</p>
                @endif

                @if($invitation->verse_translation)
                    <blockquote class="iv-verse-translation">
                        &ldquo;{{ $invitation->verse_translation }}&rdquo;
                    </blockquote>
                @endif

                @if($invitation->verse_source)
                    <cite class="iv-kicker iv-verse-source">&mdash; {{ $invitation->verse_source }}</cite>
                @endif
            </div>
        @endif
    </div>
</section>
