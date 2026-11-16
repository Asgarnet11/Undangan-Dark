@if($invitation->verse_arabic || $invitation->verse_translation)
<section id="section-verse" class="sm-section sm-section--soft sm-section-verse">
    <div class="sm-decor-sprig-center" aria-hidden="true">
        <x-sunny-meadow.svg name="leaf-sprig" class="sm-svg-sprig" />
    </div>

    <p class="sm-section-subtitle">{{ __('invitation.verse.heading') }}</p>

    @if($invitation->verse_arabic)
        <p class="sm-text-arabic" lang="ar" dir="rtl">{{ $invitation->verse_arabic }}</p>
    @endif

    @if($invitation->verse_translation)
        <blockquote class="sm-verse-quote">
            <p>{{ $invitation->verse_translation }}</p>
        </blockquote>
    @endif

    @if($invitation->verse_source)
        <p class="sm-verse-source">{{ $invitation->verse_source }}</p>
    @endif
</section>
@endif

