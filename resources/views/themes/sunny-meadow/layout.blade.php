<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#fff8ec">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $invitation->title }}</title>
    <meta name="description" content="{{ $invitation->opening_text ?? $invitation->title }}">
    <meta property="og:title" content="{{ $invitation->title }}">
    <meta property="og:description" content="{{ $invitation->opening_text ?? $invitation->title }}">
    <meta property="og:type" content="website">

    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/fraunces-latin-700.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/dm-sans-latin-400.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/sunny-meadow/fonts/caveat-latin-600.woff2') }}">
    <link rel="stylesheet" href="{{ asset('themes/sunny-meadow/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/sunny-meadow/theme.css') }}">
    @vite(['resources/js/app.js'])
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
    $musicSrc = $invitation->resolveMediaOrPathUrl('music', $invitation->music_file, '') ?: asset('themes/sunny-meadow/audio/sunny-acoustic.mp3');
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

    <script src="{{ asset('themes/sunny-meadow/theme.js') }}" defer></script>
</body>
</html>
