@extends('themes.ivory-emboss.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @include('themes.ivory-emboss.partials.cover')

    <main id="iv-main" class="iv-main">
        @include('themes.ivory-emboss.partials.hero')

        @if(data_get($themeConfig, 'sections.verse', true) || $previewMode)
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.ivory-emboss.partials.verse')
            </div>
        @endif

        @include('themes.ivory-emboss.partials.couple')

        @include('themes.ivory-emboss.partials.countdown')

        {{-- Anchor placeholder untuk Fase 4 --}}
        <section id="section-events" class="iv-section iv-foundation-preview-band">
            <div class="iv-safe-zone">
                <p class="iv-kicker">{{ __('invitation.events.kicker') }}</p>
                <p style="margin: 0.5rem 0 0; color: var(--iv-ink-soft); font-size: 0.9rem;">
                    Section Acara, Galeri, RSVP, Ucapan, Kado, Kisah Cinta, dan Penutup akan dilengkapi pada Fase 4.
                </p>
            </div>
        </section>
    </main>

    @include('themes.ivory-emboss.partials.bottom-nav')
@endsection
