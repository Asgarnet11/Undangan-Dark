<footer id="section-closing" class="mb-section mb-section--soft mb-reveal" style="padding-bottom: 6rem;">
    <div style="max-width: 32px; margin: 0 auto 0.75rem; color: var(--mb-primary); opacity: 0.8;" aria-hidden="true">
        <x-minimalist-botanical.svg name="leaf-accent" />
    </div>

    <div style="position: absolute; top: 0.5rem; left: 0.5rem; width: 80px; height: 80px; color: var(--mb-primary); opacity: 0.5;" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-branch-top" />
    </div>
    <div style="position: absolute; bottom: 0.5rem; right: 0.5rem; width: 80px; height: 80px; color: var(--mb-primary); opacity: 0.5;" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-branch-bottom" />
    </div>

    @if($invitation->closing_text)
        <p class="mb-body-soft" style="max-width: 420px; margin: 0 auto 1.5rem; font-style: italic;">
            &ldquo;{{ $invitation->closing_text }}&rdquo;
        </p>
    @endif

    <p class="mb-display" style="font-size: 2rem; color: var(--mb-text-main); margin-bottom: 1.5rem;">
        {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
    </p>

    @if($invitation->coInviters->isNotEmpty())
        <div style="margin-bottom: 2rem;">
            <p class="mb-kicker">{{ __('invitation.closing.co_inviters_heading') }}</p>
            <ul style="list-style: none; display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem 1rem; margin-top: 0.5rem;">
                @foreach($invitation->coInviters as $coInviter)
                    <li class="mb-body-soft" style="font-size: 0.85rem;">{{ $coInviter->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--mb-border); font-size: 0.78rem;">
        <p class="mb-body-soft" style="margin-bottom: 0.25rem;">{{ __('invitation.closing.watermark_prompt') }}</p>
        <a
            href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}"
            target="_blank"
            rel="noopener noreferrer"
            style="color: var(--mb-primary); font-weight: 500; text-decoration: underline;"
        >
            {{ __('invitation.closing.watermark_cta', ['vendor' => config('app.vendor_name', 'a2dev.my.id')]) }}
        </a>
        <p class="mb-body-soft" style="margin-top: 0.5rem; opacity: 0.6;">{{ config('app.name', 'Undangan Pernikahan Digital') }}</p>
    </div>
</footer>
