@if($invitation->verse_arabic || $invitation->verse_translation)
<section id="section-verse" class="db-section db-section--deep db-reveal-section">
    <div class="db-ornament-sm" aria-hidden="true">
        <x-dark-boho.svg name="sun-moon" class="db-svg-celestial" />
    </div>
    <p class="db-kicker">{{ __('invitation.verse.heading') }}</p>

    @if($invitation->verse_arabic)
        <p class="db-arabic" lang="ar" dir="rtl">{{ $invitation->verse_arabic }}</p>
    @endif

    @if($invitation->verse_translation)
        <blockquote class="db-verse-quote">
            <p class="db-body-soft">{{ $invitation->verse_translation }}</p>
        </blockquote>
    @endif

    @if($invitation->verse_source)
        <p class="db-kicker db-verse-source">{{ $invitation->verse_source }}</p>
    @endif

    <div class="db-divider" aria-hidden="true">
        <x-dark-boho.svg name="divider-boho" class="db-svg-divider" />
    </div>
</section>
@endif
