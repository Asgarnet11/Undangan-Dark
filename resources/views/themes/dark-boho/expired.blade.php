<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - {{ $invitation->title }}</title>
    <link rel="stylesheet" href="{{ asset('themes/dark-boho/css/theme.css') }}">
</head>
<body class="db-body" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <main class="db-card db-card--arch" style="max-width: 540px; margin: 0 auto; text-align: center; padding: 3rem 2rem;">
        <p class="db-kicker">Masa Undangan Selesai</p>
        <h1 class="db-display db-section-title" style="margin-bottom: 1rem;">
            {{ $invitation->bride_nickname }} <span class="db-script">&amp;</span> {{ $invitation->groom_nickname }}
        </h1>

        <div style="width: 48px; height: 1px; background: var(--db-gold, #c5a059); margin: 1.5rem auto;"></div>

        <p class="db-body-soft" style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 1.5rem;">
            Acara pernikahan kami telah selesai diselenggarakan dengan penuh kehangatan.
            Terima kasih yang tak terhingga atas doa restu, kehadiran, serta perhatian yang telah Bapak/Ibu/Saudara/i berikan kepada kami.
        </p>

        @if($invitation->closing_text)
            <p class="db-body-soft" style="font-size: 0.95rem; font-style: italic; opacity: 0.85; margin-bottom: 2rem;">
                &ldquo;{{ $invitation->closing_text }}&rdquo;
            </p>
        @endif

        <p class="db-script" style="font-size: 1.6rem; color: var(--db-gold, #c5a059); margin-top: 1.5rem;">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </p>

        <p class="db-body-soft" style="margin-top: 2rem; font-size: 0.78rem; opacity: 0.7;">
            Tertarik membuat undangan seperti ini?
            <a href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}" target="_blank" rel="noopener noreferrer" style="color: var(--db-gold, #c5a059); text-decoration: underline;">
                Pesan di a2dev.my.id
            </a>
        </p>
    </main>
</body>
</html>

