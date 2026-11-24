@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $photoFrame = data_get($themeConfig, 'layout.photoFrame', 'diamond');
    $bridePhoto = $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo);
    $groomPhoto = $invitation->resolveMediaOrPathUrl('groom', $invitation->groom_photo);
@endphp

<section id="section-couple" class="bh-section bh-couple-section" aria-label="Profil Kedua Mempelai">
    <h2 class="bh-display bh-section-title">{{ __('invitation.couple.heading') }}</h2>
    <p class="bh-body-serif bh-section-subtitle">
        {{ __('invitation.couple.subtitle') }}
    </p>

    @if($brideFirst)
        {{-- Mempelai Wanita --}}
        <article class="bh-couple-card bh-couple-card--bride" data-role="bride">
            @if($bridePhoto)
                <div class="bh-photo-frame bh-photo-frame--{{ $photoFrame }}">
                    @if($photoFrame === 'diamond')
                        <div class="bh-photo-frame-ornament" aria-hidden="true">
                            <x-bugis-heritage.svg name="walasuji-frame" />
                        </div>
                    @endif
                    <img src="{{ $bridePhoto }}" alt="Foto Mempelai Wanita {{ $invitation->bride_name }}" class="bh-couple-img" loading="lazy">
                </div>
            @endif

            <h3 class="bh-display bh-couple-name">{{ $invitation->bride_name }}</h3>
            @if($invitation->bride_nickname)
                <p class="bh-script bh-couple-nickname">({{ $invitation->bride_nickname }})</p>
            @endif

            @if($invitation->bride_order_label)
                <p class="bh-couple-order">{{ $invitation->bride_order_label }}</p>
            @endif

            @if($invitation->bride_father || $invitation->bride_mother)
                <p class="bh-body-serif bh-couple-parents">
                    {{ __('invitation.couple.daughter_of') }}
                    <br>
                    <strong>{{ $invitation->bride_father }}</strong> &amp; <strong>{{ $invitation->bride_mother }}</strong>
                </p>
            @endif

            @if($invitation->bride_origin)
                <p class="bh-body-serif bh-couple-origin">{{ $invitation->bride_origin }}</p>
            @endif

            @if($invitation->bride_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="bh-btn bh-couple-ig">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="16" height="16" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    <span>{{ '@' . ltrim($invitation->bride_instagram, '@') }}</span>
                </a>
            @endif
        </article>

        <div class="bh-couple-ampersand" aria-hidden="true">
            <span class="bh-script">&amp;</span>
        </div>

        {{-- Mempelai Pria --}}
        <article class="bh-couple-card bh-couple-card--groom" data-role="groom">
            @if($groomPhoto)
                <div class="bh-photo-frame bh-photo-frame--{{ $photoFrame }}">
                    @if($photoFrame === 'diamond')
                        <div class="bh-photo-frame-ornament" aria-hidden="true">
                            <x-bugis-heritage.svg name="walasuji-frame" />
                        </div>
                    @endif
                    <img src="{{ $groomPhoto }}" alt="Foto Mempelai Pria {{ $invitation->groom_name }}" class="bh-couple-img" loading="lazy">
                </div>
            @endif

            <h3 class="bh-display bh-couple-name">{{ $invitation->groom_name }}</h3>
            @if($invitation->groom_nickname)
                <p class="bh-script bh-couple-nickname">({{ $invitation->groom_nickname }})</p>
            @endif

            @if($invitation->groom_order_label)
                <p class="bh-couple-order">{{ $invitation->groom_order_label }}</p>
            @endif

            @if($invitation->groom_father || $invitation->groom_mother)
                <p class="bh-body-serif bh-couple-parents">
                    {{ __('invitation.couple.son_of') }}
                    <br>
                    <strong>{{ $invitation->groom_father }}</strong> &amp; <strong>{{ $invitation->groom_mother }}</strong>
                </p>
            @endif

            @if($invitation->groom_origin)
                <p class="bh-body-serif bh-couple-origin">{{ $invitation->groom_origin }}</p>
            @endif

            @if($invitation->groom_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="bh-btn bh-couple-ig">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="16" height="16" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    <span>{{ '@' . ltrim($invitation->groom_instagram, '@') }}</span>
                </a>
            @endif
        </article>
    @else
        {{-- Mempelai Pria Terlebih Dahulu --}}
        <article class="bh-couple-card bh-couple-card--groom" data-role="groom">
            @if($groomPhoto)
                <div class="bh-photo-frame bh-photo-frame--{{ $photoFrame }}">
                    @if($photoFrame === 'diamond')
                        <div class="bh-photo-frame-ornament" aria-hidden="true">
                            <x-bugis-heritage.svg name="walasuji-frame" />
                        </div>
                    @endif
                    <img src="{{ $groomPhoto }}" alt="Foto Mempelai Pria {{ $invitation->groom_name }}" class="bh-couple-img" loading="lazy">
                </div>
            @endif

            <h3 class="bh-display bh-couple-name">{{ $invitation->groom_name }}</h3>
            @if($invitation->groom_nickname)
                <p class="bh-script bh-couple-nickname">({{ $invitation->groom_nickname }})</p>
            @endif

            @if($invitation->groom_order_label)
                <p class="bh-couple-order">{{ $invitation->groom_order_label }}</p>
            @endif

            @if($invitation->groom_father || $invitation->groom_mother)
                <p class="bh-body-serif bh-couple-parents">
                    {{ __('invitation.couple.son_of') }}
                    <br>
                    <strong>{{ $invitation->groom_father }}</strong> &amp; <strong>{{ $invitation->groom_mother }}</strong>
                </p>
            @endif

            @if($invitation->groom_origin)
                <p class="bh-body-serif bh-couple-origin">{{ $invitation->groom_origin }}</p>
            @endif

            @if($invitation->groom_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="bh-btn bh-couple-ig">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="16" height="16" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    <span>{{ '@' . ltrim($invitation->groom_instagram, '@') }}</span>
                </a>
            @endif
        </article>

        <div class="bh-couple-ampersand" aria-hidden="true">
            <span class="bh-script">&amp;</span>
        </div>

        {{-- Mempelai Wanita --}}
        <article class="bh-couple-card bh-couple-card--bride" data-role="bride">
            @if($bridePhoto)
                <div class="bh-photo-frame bh-photo-frame--{{ $photoFrame }}">
                    @if($photoFrame === 'diamond')
                        <div class="bh-photo-frame-ornament" aria-hidden="true">
                            <x-bugis-heritage.svg name="walasuji-frame" />
                        </div>
                    @endif
                    <img src="{{ $bridePhoto }}" alt="Foto Mempelai Wanita {{ $invitation->bride_name }}" class="bh-couple-img" loading="lazy">
                </div>
            @endif

            <h3 class="bh-display bh-couple-name">{{ $invitation->bride_name }}</h3>
            @if($invitation->bride_nickname)
                <p class="bh-script bh-couple-nickname">({{ $invitation->bride_nickname }})</p>
            @endif

            @if($invitation->bride_order_label)
                <p class="bh-couple-order">{{ $invitation->bride_order_label }}</p>
            @endif

            @if($invitation->bride_father || $invitation->bride_mother)
                <p class="bh-body-serif bh-couple-parents">
                    {{ __('invitation.couple.daughter_of') }}
                    <br>
                    <strong>{{ $invitation->bride_father }}</strong> &amp; <strong>{{ $invitation->bride_mother }}</strong>
                </p>
            @endif

            @if($invitation->bride_origin)
                <p class="bh-body-serif bh-couple-origin">{{ $invitation->bride_origin }}</p>
            @endif

            @if($invitation->bride_instagram)
                <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="bh-btn bh-couple-ig">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="16" height="16" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    <span>{{ '@' . ltrim($invitation->bride_instagram, '@') }}</span>
                </a>
            @endif
        </article>
    @endif
</section>
