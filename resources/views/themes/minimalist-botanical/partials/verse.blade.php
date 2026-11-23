@if($invitation->verse_arabic || $invitation->verse_translation)
    <section id="section-verse" class="mb-section mb-reveal">
        <div class="mb-divider-wrap" aria-hidden="true">
            <x-minimalist-botanical.svg name="botanical-divider" />
        </div>

        @if($invitation->verse_arabic)
            <p class="mb-arabic">{{ $invitation->verse_arabic }}</p>
        @endif

        @if($invitation->verse_translation)
            <blockquote class="mb-body-soft" style="font-style: italic; margin-top: 1rem; max-width: 420px; margin-left: auto; margin-right: auto;">
                &ldquo;{{ $invitation->verse_translation }}&rdquo;
            </blockquote>
        @endif

        @if($invitation->verse_source)
            <cite class="mb-kicker" style="display: block; margin-top: 0.75rem; font-style: normal; font-size: 0.8rem;">
                &mdash; {{ $invitation->verse_source }}
            </cite>
        @endif
    </section>
@endif
