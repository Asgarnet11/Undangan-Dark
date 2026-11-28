@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $photoFrame = data_get($themeConfig, 'layout.photoFrame', 'oval');
    $bridePhoto = $invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo);
    $groomPhoto = $invitation->resolveMediaOrPathUrl('groom', $invitation->groom_photo);

    $profiles = [
        [
            'role' => 'bride',
            'photo' => $bridePhoto,
            'name' => $invitation->bride_name,
            'nickname' => $invitation->bride_nickname,
            'order' => $invitation->bride_order_label,
            'parentLabel' => __('invitation.couple.daughter_of'),
            'father' => $invitation->bride_father,
            'mother' => $invitation->bride_mother,
            'origin' => $invitation->bride_origin,
            'ig' => $invitation->bride_instagram,
        ],
        [
            'role' => 'groom',
            'photo' => $groomPhoto,
            'name' => $invitation->groom_name,
            'nickname' => $invitation->groom_nickname,
            'order' => $invitation->groom_order_label,
            'parentLabel' => __('invitation.couple.son_of'),
            'father' => $invitation->groom_father,
            'mother' => $invitation->groom_mother,
            'origin' => $invitation->groom_origin,
            'ig' => $invitation->groom_instagram,
        ],
    ];

    if (!$brideFirst) {
        $profiles = array_reverse($profiles);
    }
@endphp

<section id="section-couple" class="iv-section iv-couple" aria-label="Profil Kedua Mempelai">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-b.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-b.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-b@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-b@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.couple.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.couple.heading') }}</h2>
        <p class="iv-section-subtitle">{{ __('invitation.couple.subtitle') }}</p>

        <div class="iv-divider-sprig" aria-hidden="true">
            <img
                src="{{ asset('themes/ivory-emboss/relief/divider-sprig.webp') }}"
                srcset="{{ asset('themes/ivory-emboss/relief/divider-sprig.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/divider-sprig@2x.webp') }} 2x"
                alt=""
                width="640"
                height="120"
                loading="lazy"
                decoding="async"
            >
        </div>

        @foreach($profiles as $index => $person)
            <article class="iv-couple-panel" data-role="{{ $person['role'] }}">
                <div class="iv-couple-apex" aria-hidden="true">
                    <img
                        src="{{ asset('themes/ivory-emboss/relief/flower-open.webp') }}"
                        srcset="{{ asset('themes/ivory-emboss/relief/flower-open.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/flower-open@2x.webp') }} 2x"
                        alt=""
                        width="240"
                        height="240"
                        loading="lazy"
                        decoding="async"
                    >
                </div>

                <div class="iv-couple-panel-body">
                    @if($person['photo'])
                        <div class="iv-couple-photo-wrap iv-couple-photo-wrap--{{ $photoFrame }}">
                            <img src="{{ $person['photo'] }}" alt="Foto {{ $person['name'] }}" class="iv-couple-photo" width="320" height="400" loading="lazy">
                        </div>
                    @endif

                    <h3 class="iv-display iv-couple-name">{{ $person['name'] }}</h3>
                    @if($person['order'])
                        <p class="iv-kicker iv-couple-order">{{ $person['order'] }}</p>
                    @endif

                    @if($person['father'] || $person['mother'])
                        <p class="iv-couple-parents">
                            {{ $person['parentLabel'] }}<br>
                            <strong>{{ $person['father'] }}</strong> &amp; <strong>{{ $person['mother'] }}</strong>
                        </p>
                    @endif

                    @if($person['origin'])
                        <p class="iv-couple-origin">{{ $person['origin'] }}</p>
                    @endif

                    @if($person['ig'])
                        <a
                            href="https://instagram.com/{{ ltrim($person['ig'], '@') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="iv-btn iv-btn--ghost iv-couple-ig"
                        >
                            <span>{{ '@' . ltrim($person['ig'], '@') }}</span>
                        </a>
                    @endif
                </div>
            </article>

            @if($index === 0)
                <div class="iv-couple-amp" aria-hidden="true">&amp;</div>
            @endif
        @endforeach
    </div>
</section>
