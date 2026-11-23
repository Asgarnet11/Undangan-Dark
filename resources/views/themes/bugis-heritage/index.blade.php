@extends('themes.bugis-heritage.layout')

@php
    $previewMode = !empty($isPreview);
    $guestName = !empty($guest) ? $guest->name : 'Tamu Undangan';
    $guestCode = !empty($guest) ? $guest->code : '';
@endphp

@section('content')
    <div id="bh-cover" class="bh-cover bh-section" style="min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative;">
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tl" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--tr" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--bl" />
        <x-bugis-heritage.svg name="corner-ornament" class="bh-corner-ornament bh-corner-ornament--br" />

        <div class="bh-cover-inner" style="width: 100%; max-width: 380px; margin: 0 auto; text-align: center; padding: 2rem 1rem;">
            <div class="bh-cover-badge" style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
                <x-bugis-heritage.svg name="monogram-badge" />
            </div>

            <p class="bh-kicker" style="margin-bottom: 0.75rem;">{{ __('invitation.cover.wedding_invitation') }}</p>

            <h1 class="bh-display bh-cover-names" style="font-size: 2.25rem; line-height: 1.2; margin: 0.5rem 0 1rem;">
                @if(data_get($themeConfig, 'layout.brideFirst', true))
                    {{ $invitation->bride_nickname }} <span class="bh-script" style="font-size: 2rem;">&amp;</span> {{ $invitation->groom_nickname }}
                @else
                    {{ $invitation->groom_nickname }} <span class="bh-script" style="font-size: 2rem;">&amp;</span> {{ $invitation->bride_nickname }}
                @endif
            </h1>

            @if(data_get($themeConfig, 'cover.showGuestName', true))
                <div class="bh-cover-guest" style="margin: 1.75rem 0; padding: 1rem 1.25rem; border: 1px solid var(--bh-line); border-radius: var(--bh-radius); background: var(--bh-bg-elevated);">
                    <p style="font-family: var(--bh-font-label); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.2em; opacity: 0.75; margin-bottom: 0.25rem;">
                        {{ __('invitation.cover.recipient_prefix') }}
                    </p>
                    <p style="font-family: var(--bh-font-display); font-size: 1.25rem; font-weight: 600; color: var(--bh-accent);">
                        {{ $guestName }}
                    </p>
                    @if($guestCode)
                        <span style="display: none;" class="bh-guest-code">{{ $guestCode }}</span>
                    @endif
                </div>
            @endif

            <button type="button" id="bh-open-invitation" class="bh-btn bh-btn--primary">
                {{ __('invitation.cover.open_button') }}
            </button>
        </div>
    </div>

    <main id="bh-main" class="bh-main" style="padding-bottom: 4rem;">
        <div class="bh-divider">
            <x-bugis-heritage.svg name="divider-diamond" />
        </div>

        <section class="bh-section">
            <div class="bh-card bh-card--gold-border">
                <p class="bh-kicker">Pernikahan Adat Bugis</p>
                <h2 class="bh-display bh-section-title">{{ $invitation->title }}</h2>
                <p class="bh-body-serif" style="font-size: 0.95rem; line-height: 1.8;">
                    {{ $invitation->opening_text ?? 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan syukuran pernikahan putra-putri kami.' }}
                </p>
            </div>
        </section>
    </main>
@endsection
