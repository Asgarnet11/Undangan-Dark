@php
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $photoFrame = data_get($themeConfig, 'layout.photoFrame', 'flower');

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

<section id="section-couple" class="sm-section sm-section-couple">
    <div class="sm-decor-flower-center" aria-hidden="true">
        <x-sunny-meadow.svg name="flower-small" class="sm-svg-flower-sm" />
    </div>

    <p class="sm-section-subtitle">{{ __('invitation.couple.heading') }}</p>
    <h2 class="sm-section-title">
        {{ $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname }}
        <span class="sm-title-script sm-couple-amp">&amp;</span>
        {{ $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname }}
    </h2>

    <div class="sm-couple-grid">
        @foreach($profiles as $person)
            <article class="sm-card sm-person-card" data-role="{{ $person['role'] }}">
                <div class="sm-photo-frame-wrap sm-photo-frame--{{ $photoFrame }}">
                    <div class="sm-person-media">
                        @if($person['photo'])
                            <img
                                src="{{ $person['photo'] }}"
                                alt="{{ $person['name'] }}"
                                class="sm-person-photo"
                                width="400"
                                height="400"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="sm-person-placeholder" aria-hidden="true">
                                <span class="sm-title-script">{{ mb_strtoupper(mb_substr($person['nickname'], 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="sm-frame-svg-layer" aria-hidden="true">
                        @if($photoFrame === 'blob')
                            <x-sunny-meadow.svg name="photo-frame-blob" class="sm-svg-frame" />
                        @else
                            <x-sunny-meadow.svg name="photo-frame-flower" class="sm-svg-frame" />
                        @endif
                    </div>
                </div>

                <h3 class="sm-title-display sm-person-name">{{ $person['name'] }}</h3>

                @if($person['order_label'] || $person['father'] || $person['mother'])
                    <p class="sm-person-parents">
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
                    <p class="sm-person-origin">{{ $person['origin'] }}</p>
                @endif

                @if($person['instagram'])
                    @php
                        $handle = ltrim((string) $person['instagram'], '@');
                        $igUrl = str_starts_with($handle, 'http') ? $handle : 'https://instagram.com/' . $handle;
                    @endphp
                    <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="sm-btn sm-btn-outline sm-person-social">
                        &#64;{{ $handle }}
                    </a>
                @endif
            </article>
        @endforeach
    </div>
</section>
