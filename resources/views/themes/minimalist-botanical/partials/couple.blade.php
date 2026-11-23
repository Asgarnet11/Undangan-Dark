@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $bridePhoto = $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo);
    $groomPhoto = $invitation->resolveMediaOrPathUrl('groom', $invitation->groom_photo);
@endphp

<section id="section-couple" class="mb-section mb-reveal">
    <p class="mb-kicker">{{ __('invitation.couple.kicker') }}</p>
    <h2 class="mb-display mb-section-title">{{ __('invitation.couple.heading') }}</h2>
    <p class="mb-body-soft mb-section-subtitle">
        {{ $invitation->opening_text ?: __('invitation.couple.subtitle') }}
    </p>

    <div class="mb-divider-wrap" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-divider" />
    </div>

    @if($brideFirst)
        <article class="mb-card mb-profile-card" style="margin-bottom: 2rem;">
            @if($bridePhoto)
                <div style="position: relative; width: 180px; height: 230px; margin: 0 auto 1.25rem;">
                    <div style="position: absolute; inset: 0; color: var(--mb-primary); pointer-events: none;" aria-hidden="true">
                        <x-minimalist-botanical.svg name="geometric-frame" />
                    </div>
                    <img src="{{ $bridePhoto }}" alt="Foto Mempelai Wanita {{ $invitation->bride_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; padding: 10px;" loading="lazy">
                </div>
            @endif

            <h3 class="mb-display" style="font-size: 1.6rem; color: var(--mb-text-main); margin-bottom: 0.25rem;">
                {{ $invitation->bride_name }}
            </h3>
            @if($invitation->bride_order_label)
                <p class="mb-kicker" style="font-size: 0.75rem; color: var(--mb-secondary); margin-bottom: 0.5rem;">
                    {{ $invitation->bride_order_label }}
                </p>
            @endif

            @if($invitation->bride_father || $invitation->bride_mother)
                <p class="mb-body-soft" style="font-size: 0.85rem;">
                    {{ __('invitation.couple.daughter_of') }}
                    <br>
                    <strong>{{ $invitation->bride_father }}</strong> &amp; <strong>{{ $invitation->bride_mother }}</strong>
                </p>
            @endif

            @if($invitation->bride_origin)
                <p class="mb-body-soft" style="font-size: 0.8rem; margin-top: 0.25rem; opacity: 0.8;">
                    {{ $invitation->bride_origin }}
                </p>
            @endif

            @if($invitation->bride_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="mb-btn mb-btn--ghost mb-btn--sm" style="margin-top: 1rem;">
                    <span>{{ '@' . ltrim($invitation->bride_instagram, '@') }}</span>
                </a>
            @endif
        </article>

        <div style="font-family: var(--mb-font-script); font-size: 2.2rem; color: var(--mb-primary); margin: 0.5rem auto 1.5rem;" aria-hidden="true">&amp;</div>

        <article class="mb-card mb-profile-card" style="margin-bottom: 2rem;">
            @if($groomPhoto)
                <div style="position: relative; width: 180px; height: 230px; margin: 0 auto 1.25rem;">
                    <div style="position: absolute; inset: 0; color: var(--mb-primary); pointer-events: none;" aria-hidden="true">
                        <x-minimalist-botanical.svg name="geometric-frame" />
                    </div>
                    <img src="{{ $groomPhoto }}" alt="Foto Mempelai Pria {{ $invitation->groom_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; padding: 10px;" loading="lazy">
                </div>
            @endif

            <h3 class="mb-display" style="font-size: 1.6rem; color: var(--mb-text-main); margin-bottom: 0.25rem;">
                {{ $invitation->groom_name }}
            </h3>
            @if($invitation->groom_order_label)
                <p class="mb-kicker" style="font-size: 0.75rem; color: var(--mb-secondary); margin-bottom: 0.5rem;">
                    {{ $invitation->groom_order_label }}
                </p>
            @endif

            @if($invitation->groom_father || $invitation->groom_mother)
                <p class="mb-body-soft" style="font-size: 0.85rem;">
                    {{ __('invitation.couple.son_of') }}
                    <br>
                    <strong>{{ $invitation->groom_father }}</strong> &amp; <strong>{{ $invitation->groom_mother }}</strong>
                </p>
            @endif

            @if($invitation->groom_origin)
                <p class="mb-body-soft" style="font-size: 0.8rem; margin-top: 0.25rem; opacity: 0.8;">
                    {{ $invitation->groom_origin }}
                </p>
            @endif

            @if($invitation->groom_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="mb-btn mb-btn--ghost mb-btn--sm" style="margin-top: 1rem;">
                    <span>{{ '@' . ltrim($invitation->groom_instagram, '@') }}</span>
                </a>
            @endif
        </article>
    @else
        <article class="mb-card mb-profile-card" style="margin-bottom: 2rem;">
            @if($groomPhoto)
                <div style="position: relative; width: 180px; height: 230px; margin: 0 auto 1.25rem;">
                    <div style="position: absolute; inset: 0; color: var(--mb-primary); pointer-events: none;" aria-hidden="true">
                        <x-minimalist-botanical.svg name="geometric-frame" />
                    </div>
                    <img src="{{ $groomPhoto }}" alt="Foto Mempelai Pria {{ $invitation->groom_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; padding: 10px;" loading="lazy">
                </div>
            @endif

            <h3 class="mb-display" style="font-size: 1.6rem; color: var(--mb-text-main); margin-bottom: 0.25rem;">
                {{ $invitation->groom_name }}
            </h3>
            @if($invitation->groom_order_label)
                <p class="mb-kicker" style="font-size: 0.75rem; color: var(--mb-secondary); margin-bottom: 0.5rem;">
                    {{ $invitation->groom_order_label }}
                </p>
            @endif

            @if($invitation->groom_father || $invitation->groom_mother)
                <p class="mb-body-soft" style="font-size: 0.85rem;">
                    {{ __('invitation.couple.son_of') }}
                    <br>
                    <strong>{{ $invitation->groom_father }}</strong> &amp; <strong>{{ $invitation->groom_mother }}</strong>
                </p>
            @endif

            @if($invitation->groom_origin)
                <p class="mb-body-soft" style="font-size: 0.8rem; margin-top: 0.25rem; opacity: 0.8;">
                    {{ $invitation->groom_origin }}
                </p>
            @endif

            @if($invitation->groom_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="mb-btn mb-btn--ghost mb-btn--sm" style="margin-top: 1rem;">
                    <span>{{ '@' . ltrim($invitation->groom_instagram, '@') }}</span>
                </a>
            @endif
        </article>

        <div style="font-family: var(--mb-font-script); font-size: 2.2rem; color: var(--mb-primary); margin: 0.5rem auto 1.5rem;" aria-hidden="true">&amp;</div>

        <article class="mb-card mb-profile-card" style="margin-bottom: 2rem;">
            @if($bridePhoto)
                <div style="position: relative; width: 180px; height: 230px; margin: 0 auto 1.25rem;">
                    <div style="position: absolute; inset: 0; color: var(--mb-primary); pointer-events: none;" aria-hidden="true">
                        <x-minimalist-botanical.svg name="geometric-frame" />
                    </div>
                    <img src="{{ $bridePhoto }}" alt="Foto Mempelai Wanita {{ $invitation->bride_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; padding: 10px;" loading="lazy">
                </div>
            @endif

            <h3 class="mb-display" style="font-size: 1.6rem; color: var(--mb-text-main); margin-bottom: 0.25rem;">
                {{ $invitation->bride_name }}
            </h3>
            @if($invitation->bride_order_label)
                <p class="mb-kicker" style="font-size: 0.75rem; color: var(--mb-secondary); margin-bottom: 0.5rem;">
                    {{ $invitation->bride_order_label }}
                </p>
            @endif

            @if($invitation->bride_father || $invitation->bride_mother)
                <p class="mb-body-soft" style="font-size: 0.85rem;">
                    {{ __('invitation.couple.daughter_of') }}
                    <br>
                    <strong>{{ $invitation->bride_father }}</strong> &amp; <strong>{{ $invitation->bride_mother }}</strong>
                </p>
            @endif

            @if($invitation->bride_origin)
                <p class="mb-body-soft" style="font-size: 0.8rem; margin-top: 0.25rem; opacity: 0.8;">
                    {{ $invitation->bride_origin }}
                </p>
            @endif

            @if($invitation->bride_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="mb-btn mb-btn--ghost mb-btn--sm" style="margin-top: 1rem;">
                    <span>{{ '@' . ltrim($invitation->bride_instagram, '@') }}</span>
                </a>
            @endif
        </article>
    @endif
</section>
