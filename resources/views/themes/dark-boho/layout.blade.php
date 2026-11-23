<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1d1d1b">
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
        ?: asset('themes/dark-boho/img/og-preview.jpg'));

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

    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/dark-boho/fonts/cormorant-garamond-latin-600-normal.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('themes/dark-boho/fonts/jost-latin-400-normal.woff2') }}">
    <link rel="stylesheet" href="{{ asset('themes/dark-boho/fonts/fonts.css') }}?v={{ @filemtime(public_path('themes/dark-boho/fonts/fonts.css')) }}">
    <link rel="stylesheet" href="{{ asset('themes/dark-boho/theme.css') }}?v={{ @filemtime(public_path('themes/dark-boho/theme.css')) }}">
    @vite(['resources/js/app.js'])
    <script>if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }</script>
</head>
@php
    $preset = data_get($themeConfig, 'preset', 'terracotta');
    $coverStyle = data_get($themeConfig, 'cover.style', 'default');
    $coverUsePhoto = data_get($themeConfig, 'cover.usephoto', true);
    $coverOverlayColor = data_get($themeConfig, 'cover.overlayColor', '#1d1d1b');
    $coverOverlayOpacity = data_get($themeConfig, 'cover.overlayOpacity', 0.55);
    $coverTextColor = data_get($themeConfig, 'cover.textColor', '#efe6d8');
    $illustrationMode = data_get($themeConfig, 'layout.illustrationMode', 'illustrated');
    $giftInline = data_get($themeConfig, 'layout.giftInline', true);
    $brideFirst = data_get($themeConfig, 'layout.brideFirst', true);
    $mapsBelow = data_get($themeConfig, 'layout.mapsAndRsvpBelowEvents', true);
    $musicSrc = $invitation->effectiveMusicUrl() ?: asset('themes/dark-boho/audio/boho-serenade.mp3');
@endphp
<body
    class="theme-dark-boho"
    data-theme="dark-boho"
    data-preset="{{ $preset }}"
    data-cover-style="{{ $coverStyle }}"
    data-cover-use-photo="{{ $coverUsePhoto ? 'true' : 'false' }}"
    data-illustration-mode="{{ $illustrationMode }}"
    data-gift-inline="{{ $giftInline ? 'true' : 'false' }}"
    data-bride-first="{{ $brideFirst ? 'true' : 'false' }}"
    data-maps-below="{{ $mapsBelow ? 'true' : 'false' }}"
    data-preview-mode="{{ !empty($isPreview) ? 'true' : 'false' }}"
    style="--db-cover-overlay-color: {{ $coverOverlayColor }}; --db-cover-overlay-opacity: {{ $coverOverlayOpacity }}; --db-cover-text-color: {{ $coverTextColor }};"
>
    <div class="db-grain" aria-hidden="true"></div>
    <div class="db-desktop-frame" aria-hidden="true"></div>

    <audio id="db-bgm" src="{{ $musicSrc }}" preload="none" loop data-title="{{ $invitation->music_title ?: 'Musik Latar' }}"></audio>

    <button
        type="button"
        id="db-music-toggle"
        class="db-music-btn"
        aria-label="{{ __('invitation.nav.music_play') }}"
        aria-pressed="false"
        data-label-play="{{ __('invitation.nav.music_play') }}"
        data-label-pause="{{ __('invitation.nav.music_pause') }}"
        hidden
    >
        <svg class="db-music-icon db-music-icon--play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M9 18V5l10-2v13" />
            <circle cx="6.5" cy="18" r="2.5" />
            <circle cx="16.5" cy="16" r="2.5" />
        </svg>
        <svg class="db-music-icon db-music-icon--muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M9 18V5l10-2v13" opacity="0.4" />
            <circle cx="6.5" cy="18" r="2.5" opacity="0.4" />
            <circle cx="16.5" cy="16" r="2.5" opacity="0.4" />
            <path d="M4 4l16 16" stroke-linecap="round" />
        </svg>
    </button>

    <div class="db-stage">
        <div class="db-canvas">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('themes/dark-boho/theme.js') }}?v={{ @filemtime(public_path('themes/dark-boho/theme.js')) }}" defer></script>
</body>
</html>
