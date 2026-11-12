@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);

    $brideProfile = [
        'role' => 'bride',
        'name' => $invitation->bride_name,
        'nickname' => $invitation->bride_nickname,
        'order_label' => $invitation->bride_order_label,
        'relation_prefix' => __('invitation.couple.daughter_of'),
        'father' => $invitation->bride_father,
        'mother' => $invitation->bride_mother,
        'origin' => $invitation->bride_origin,
        'instagram' => $invitation->bride_instagram,
        'photo' => $invitation->resolveMediaOrPathUrl('bride_photo', $invitation->bride_photo),
    ];

    $groomProfile = [
        'role' => 'groom',
        'name' => $invitation->groom_name,
        'nickname' => $invitation->groom_nickname,
        'order_label' => $invitation->groom_order_label,
        'relation_prefix' => __('invitation.couple.son_of'),
        'father' => $invitation->groom_father,
        'mother' => $invitation->groom_mother,
        'origin' => $invitation->groom_origin,
        'instagram' => $invitation->groom_instagram,
        'photo' => $invitation->resolveMediaOrPathUrl('groom_photo', $invitation->groom_photo),
    ];

    $profiles = $brideFirst ? [$brideProfile, $groomProfile] : [$groomProfile, $brideProfile];
@endphp

<section id="section-couple" class="db-section db-reveal-section">
    <div class="db-ornament-branch" aria-hidden="true">
        <x-dark-boho.svg name="leaf-branch" class="db-svg-branch" />
    </div>

    <p class="db-kicker">{{ __('invitation.couple.heading') }}</p>
    <h2 class="db-display db-section-title">
        {{ $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname }}
        <span class="db-script db-couple-amp">&amp;</span>
        {{ $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname }}
    </h2>

    <div class="db-couple-grid">
        @foreach($profiles as $person)
            <article class="db-person-card" data-role="{{ $person['role'] }}">
                <div class="db-arch-portrait">
                    <div class="db-arch-portrait-media">
                        @if($person['photo'])
                            <img
                                src="{{ $person['photo'] }}"
                                alt="{{ $person['name'] }}"
                                class="db-person-photo"
                                width="480"
                                height="640"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="db-person-placeholder" aria-hidden="true">
                                <span class="db-script">{{ mb_strtoupper(mb_substr($person['nickname'], 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="db-arch-portrait-frame" aria-hidden="true">
                        <x-dark-boho.svg name="arch-frame" class="db-svg-arch" />
                    </div>
                </div>

                <h3 class="db-display db-person-name">{{ $person['name'] }}</h3>

                @if($person['order_label'] || $person['father'] || $person['mother'])
                    <p class="db-person-parents db-body-soft">
                        @if($person['order_label'])
                            {{ $person['order_label'] }}
                        @else
                            {{ $person['relation_prefix'] }}
                        @endif
                        @if($person['father'] || $person['mother'])
                            <br>
                            {{ collect([$person['father'], $person['mother']])->filter()->implode(' & ') }}
                        @endif
                    </p>
                @endif

                @if($person['origin'])
                    <p class="db-person-origin">{{ $person['origin'] }}</p>
                @endif

                @if($person['instagram'])
                    @php
                        $handle = ltrim((string) $person['instagram'], '@');
                        $igUrl = str_starts_with($handle, 'http') ? $handle : 'https://instagram.com/' . $handle;
                    @endphp
                    <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="db-person-social">
                        &#64;{{ $handle }}
                    </a>
                @endif
            </article>

            @if(!$loop->last)
                <div class="db-couple-separator" aria-hidden="true">
                    <span class="db-script">&amp;</span>
                </div>
            @endif
        @endforeach
    </div>
</section>
