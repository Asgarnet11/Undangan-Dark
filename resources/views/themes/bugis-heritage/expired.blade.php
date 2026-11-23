<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - {{ $invitation->title }}</title>
    <link rel="stylesheet" href="{{ asset('themes/bugis-heritage/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/bugis-heritage/theme.css') }}">
</head>
<body class="theme-bugis-heritage" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; background-color: var(--bh-bg-deep);">
    <main class="bh-card bh-card--gold-border" style="max-width: 520px; margin: 0 auto; text-align: center; padding: 3rem 2rem;">
        <p class="bh-kicker">Masa Undangan Selesai</p>
        <h1 class="bh-display bh-section-title" style="margin-bottom: 1rem; font-size: 2.2rem;">
            {{ $invitation->bride_nickname }} <span class="bh-script">&amp;</span> {{ $invitation->groom_nickname }}
        </h1>

        <div style="width: 48px; height: 1px; background: var(--bh-gold); margin: 1.5rem auto;"></div>

        <p class="bh-body-serif" style="font-size: 1.02rem; line-height: 1.8; margin-bottom: 1.5rem; color: var(--bh-ink-soft);">
            Acara pernikahan adat kami telah selesai diselenggarakan dengan penuh kehangatan dan khidmat.
            Terima kasih yang tak terhingga atas doa restu, kehadiran, serta perhatian yang telah Bapak/Ibu/Saudara/i berikan kepada kami.
        </p>

        @if($invitation->closing_text)
            <p class="bh-body-serif" style="font-size: 0.95rem; font-style: italic; opacity: 0.85; margin-bottom: 2rem; color: var(--bh-ink-soft);">
                &ldquo;{{ $invitation->closing_text }}&rdquo;
            </p>
        @endif

        <p class="bh-script" style="font-size: 1.8rem; color: var(--bh-gold); margin-top: 1.5rem;">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </p>

        <p style="margin-top: 2rem; font-size: 0.78rem; opacity: 0.7; font-family: var(--bh-font-label);">
            Tertarik membuat undangan pernikahan seperti ini?
            <a href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}" target="_blank" rel="noopener noreferrer" style="color: var(--bh-gold); text-decoration: underline;">
                Pesan di a2dev.my.id
            </a>
        </p>
    </main>
</body>
</html>
