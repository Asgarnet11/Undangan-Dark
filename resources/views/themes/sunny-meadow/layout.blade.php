<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#fff8ec">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $ogTitle = $invitation->title;
    if (!empty($guest)) {
        $ogDescription = 'Kepada Yth. ' . $guest->name . '. ' . ($invitation->opening_text ?? ('Pernikahan ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname));
    } else {
        $ogDescription = $invitation->opening_text ?? ('Pernikahan ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname);
    }

    $resolvedCover = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo)
        ?: ($invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo)
        ?: asset('themes/sunny-meadow/img/og-preview.jpg'));

    $ogImage = (!str_starts_with($resolvedCover, 'http://') && !str_starts_with($resolvedCover, 'https://'))
        ? url($resolvedCover)
        : $resolvedCover;

    $ogImageSecure = str_starts_with($ogImage, 'https://')
        ? $ogImage
        : (str_starts_with($ogImage, 'http://') ? 'https://' . substr($ogImage, 7) : $ogImage);

    $currentFullUrl = request()->fullUrl();
@endphp
    <title>{{ $ogTitle }}</title>
    <meta name="description" content="{{ $ogDescription }}">
    <link rel="canonical" href="{{ $currentFullUrl }}">
    @if($invitation->no_index)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'Undangan Pernikahan Digital') }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $currentFullUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImageSecure }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/fraunces-latin-700.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/dm-sans-latin-400.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/caveat-latin-600.woff2') }}">
    <link rel="stylesheet" href="{{ asset('themes/sunny-meadow/fonts/fonts.css') }}?v={{ @filemtime(public_path('themes/sunny-meadow/fonts/fonts.css')) }}">
    <link rel="stylesheet" href="{{ asset('themes/sunny-meadow/theme.css') }}?v={{ @filemtime(public_path('themes/sunny-meadow/theme.css')) }}">
    @vite(['resources/js/app.js'])
    <script>if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }</script>
</head>
@php
    $preset = data_get($themeConfig, 'preset', 'sunny');
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $coverUsePhoto = data_get($themeConfig, 'cover.usePhoto', false);
    $coverOverlayColor = data_get($themeConfig, 'cover.overlayColor', '#fff8ec');
    $coverOverlayOpacity = data_get($themeConfig, 'cover.overlayOpacity', 0.35);
    $coverTextColor = data_get($themeConfig, 'cover.textColor', '#3b2f2a');
    $photoFrame = data_get($themeConfig, 'layout.photoFrame', 'flower');
    $brideFirst = data_get($themeConfig, 'layout.brideFirst', true);
    $giftInline = data_get($themeConfig, 'layout.giftInline', true);
    $motionConfetti = data_get($themeConfig, 'motion.confetti', true);
    $motionFloatingDecor = data_get($themeConfig, 'motion.floatingDecor', true);
    $musicSrc = $invitation->effectiveMusicUrl() ?: asset('themes/sunny-meadow/audio/sunny-acoustic.mp3');
@endphp
<body
    class="theme-sunny-meadow is-locked"
    data-theme="sunny-meadow"
    data-preset="{{ $preset }}"
    data-cover-style="{{ $coverStyle }}"
    data-cover-use-photo="{{ $coverUsePhoto ? 'true' : 'false' }}"
    data-photo-frame="{{ $photoFrame }}"
    data-bride-first="{{ $brideFirst ? 'true' : 'false' }}"
    data-gift-inline="{{ $giftInline ? 'true' : 'false' }}"
    data-motion-confetti="{{ $motionConfetti ? 'true' : 'false' }}"
    data-motion-decor="{{ $motionFloatingDecor ? 'true' : 'false' }}"
    data-preview-mode="{{ !empty($isPreview) ? 'true' : 'false' }}"
    style="--sm-cover-overlay-color: {{ $coverOverlayColor }}; --sm-cover-overlay-opacity: {{ $coverOverlayOpacity }}; --sm-cover-text-color: {{ $coverTextColor }};"
>
    <div class="sm-desktop-backdrop" aria-hidden="true"></div>

    <audio id="sm-bgm" src="{{ $musicSrc }}" preload="none" loop data-title="{{ $invitation->music_title ?: 'Sunny Acoustic' }}"></audio>

    <button
        type="button"
        id="sm-music-toggle"
        class="sm-music-btn"
        aria-label="{{ __('invitation.nav.music_play') }}"
        aria-pressed="false"
        data-label-play="{{ __('invitation.nav.music_play') }}"
        data-label-pause="{{ __('invitation.nav.music_pause') }}"
        hidden
    >
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 18V5l12-2v13"></path>
            <circle cx="6" cy="18" r="3"></circle>
            <circle cx="18" cy="16" r="3"></circle>
        </svg>
    </button>

    <div class="sm-stage">
        <div class="sm-canvas">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('themes/sunny-meadow/theme.js') }}?v={{ @filemtime(public_path('themes/sunny-meadow/theme.js')) }}" defer></script>
</body>
</html>

