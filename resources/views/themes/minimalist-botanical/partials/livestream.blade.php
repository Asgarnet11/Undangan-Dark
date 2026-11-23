@if($invitation->livestream_url)
    <section id="section-livestream" class="mb-section mb-reveal">
        <p class="mb-kicker">{{ __('invitation.livestream.kicker') }}</p>
        <h2 class="mb-display mb-section-title">{{ $invitation->livestream_title ?: __('invitation.livestream.heading') }}</h2>

        <div class="mb-card" style="margin-top: 1.5rem; text-align: center;">
            <p class="mb-body-soft" style="margin-bottom: 1.25rem;">
                {{ $invitation->livestream_note ?: __('invitation.livestream.default_note') }}
            </p>
            <a href="{{ $invitation->livestream_url }}" target="_blank" rel="noopener noreferrer" class="mb-btn mb-btn--sm">
                {{ __('invitation.livestream.watch_button') }}
            </a>
        </div>
    </section>
@endif
