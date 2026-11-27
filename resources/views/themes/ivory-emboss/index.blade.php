@extends('themes.ivory-emboss.layout')

@php
    $guestName = $guest?->name ?: __('invitation.cover.default_guest');
    $brideFirst = (bool) data_get($themeConfig, 'layout.brideFirst', true);
    $firstName = $brideFirst ? $invitation->bride_nickname : $invitation->groom_nickname;
    $secondName = $brideFirst ? $invitation->groom_nickname : $invitation->bride_nickname;
    $primaryEvent = $invitation->events->first();
    $eventDate = $primaryEvent
        ? $primaryEvent->startsAtInTimezone($invitation->resolvedTimezone())->translatedFormat('l, d F Y')
        : null;
    $showGuestName = (bool) data_get($themeConfig, 'cover.showGuestName', true);
@endphp

@section('content')
    <section id="iv-cover" class="iv-cover-foundation" aria-label="Sampul Undangan">
        <div class="iv-safe-zone">
            <p class="iv-kicker">{{ __('invitation.cover.subtitle') }}</p>
            <h1 class="iv-foundation-names">{{ $firstName }} &amp; {{ $secondName }}</h1>
            @if($eventDate)
                <p class="iv-foundation-date">{{ $eventDate }}</p>
            @endif

            @if($showGuestName)
                <div class="iv-card iv-foundation-guest">
                    <p class="iv-kicker">{{ __('invitation.cover.recipient_prefix') }}</p>
                    <p class="iv-foundation-guest-name">{{ $guestName }}</p>
                </div>
            @endif

            <button type="button" id="iv-open-invitation" class="iv-btn">
                {{ __('invitation.cover.open_button') }}
            </button>
        </div>
    </section>

    <main id="iv-main" class="iv-main">
        <section class="iv-foundation-preview-band">
            <div class="iv-safe-zone">
                <p class="iv-kicker">{{ $invitation->title }}</p>
                <p style="margin: 0.75rem 0 0; color: var(--iv-ink-soft); font-size: 0.92rem;">
                    Fondasi tema Ivory Emboss siap. Aset ornamen melati timbul dan section lengkap akan dibangun pada Fase 2 hingga Fase 4.
                </p>
            </div>
        </section>
    </main>
@endsection
