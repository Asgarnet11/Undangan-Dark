<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - {{ $invitation->title }}</title>
    <link rel="stylesheet" href="{{ asset('themes/minimalist-botanical/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/minimalist-botanical/theme.css') }}">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; background: #f0f3ee;">
    <main class="mb-card" style="max-width: 500px; margin: 0 auto; text-align: center; padding: 3rem 2rem;">
        <p class="mb-kicker">Masa Undangan Selesai</p>
        <h1 class="mb-display mb-section-title" style="margin-bottom: 1rem;">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </h1>

        <div style="width: 48px; height: 1px; background: var(--mb-primary, #4a5d4e); margin: 1.5rem auto;"></div>

        <p class="mb-body-soft" style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 1.5rem;">
            Acara pernikahan kami telah selesai diselenggarakan dengan penuh kebahagiaan.
            Terima kasih yang tak terhingga atas doa restu, kehadiran, serta perhatian yang telah Bapak/Ibu/Saudara/i berikan kepada kami.
        </p>

        @if($invitation->closing_text)
            <p class="mb-body-soft" style="font-size: 0.95rem; font-style: italic; opacity: 0.85; margin-bottom: 2rem;">
                &ldquo;{{ $invitation->closing_text }}&rdquo;
            </p>
        @endif

        <p class="mb-display" style="font-size: 1.6rem; color: var(--mb-primary, #4a5d4e); margin-top: 1.5rem;">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </p>

        <p class="mb-body-soft" style="margin-top: 2rem; font-size: 0.78rem; opacity: 0.7;">
            Tertarik membuat undangan seperti ini?
            <a href="{{ config('app.vendor_url', 'https://a2dev.my.id') }}" target="_blank" rel="noopener noreferrer" style="color: var(--mb-primary, #4a5d4e); text-decoration: underline;">
                Pesan di a2dev.my.id
            </a>
        </p>
    </main>
</body>
</html>
