<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invitation->title }}</title>
    <link rel="stylesheet" href="{{ asset('themes/ivory-emboss/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/ivory-emboss/theme.css') }}">
</head>
<body class="theme-ivory-emboss" data-preset="ivory" style="display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; background-color: var(--iv-paper-deep);">
    <div class="iv-card" style="max-width: 420px; width: 100%; text-align: center; padding: 2rem 1.5rem;">
        <p class="iv-kicker">{{ __('invitation.cover.subtitle') }}</p>
        <h1 class="iv-display" style="font-size: 1.5rem; margin: 0.75rem 0;">{{ $invitation->title }}</h1>
        <p style="color: var(--iv-ink-soft); font-size: 0.94rem; margin: 0;">
            Masa tayang undangan ini telah berakhir.
        </p>
    </div>
</body>
</html>
