@extends('themes.sunny-meadow.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @if(view()->exists('themes.sunny-meadow.partials.cover'))
        @include('themes.sunny-meadow.partials.cover')
    @else
        <header id="sm-cover" class="sm-cover sm-section">
            <h1 class="sm-title-display">{{ $invitation->title }}</h1>
            <p>{{ $invitation->opening_text ?? 'Undangan Pernikahan' }}</p>
        </header>
    @endif

    <main id="sm-main" class="sm-main">
        @if(view()->exists('themes.sunny-meadow.partials.countdown'))
            @include('themes.sunny-meadow.partials.countdown')
        @endif

        @if((data_get($themeConfig, 'sections.verse', true) || $previewMode) && view()->exists('themes.sunny-meadow.partials.verse'))
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.verse')
            </div>
        @endif

        @if(view()->exists('themes.sunny-meadow.partials.couple'))
            @include('themes.sunny-meadow.partials.couple')
        @endif

        @if((data_get($themeConfig, 'sections.loveStory', true) || $previewMode) && view()->exists('themes.sunny-meadow.partials.love-story'))
            <div data-preview-section="loveStory" @if(!data_get($themeConfig, 'sections.loveStory', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.love-story')
            </div>
        @endif

        @if(view()->exists('themes.sunny-meadow.partials.events'))
            @include('themes.sunny-meadow.partials.events')
        @endif

        @if((data_get($themeConfig, 'sections.gallery', true) || $previewMode) && view()->exists('themes.sunny-meadow.partials.gallery'))
            <div data-preview-section="gallery" @if(!data_get($themeConfig, 'sections.gallery', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.gallery')
            </div>
        @endif

        @if((data_get($themeConfig, 'sections.livestream', false) || $previewMode) && $invitation->livestream_url && view()->exists('themes.sunny-meadow.partials.livestream'))
            <div data-preview-section="livestream" @if(!data_get($themeConfig, 'sections.livestream', false)) hidden @endif>
                @include('themes.sunny-meadow.partials.livestream')
            </div>
        @endif

        @if(view()->exists('themes.sunny-meadow.partials.rsvp'))
            @include('themes.sunny-meadow.partials.rsvp')
        @endif

        @if(data_get($themeConfig, 'sections.gift', true) || $previewMode)
            @if(view()->exists('themes.sunny-meadow.partials.gift'))
                <div data-preview-section="gift" @if(!data_get($themeConfig, 'sections.gift', true)) hidden @endif>
                    @include('themes.sunny-meadow.partials.gift')
                </div>
            @endif
        @endif

        @if((data_get($themeConfig, 'sections.wishes', true) || $previewMode) && view()->exists('themes.sunny-meadow.partials.wishes'))
            <div data-preview-section="wishes" @if(!data_get($themeConfig, 'sections.wishes', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.wishes')
            </div>
        @endif

        @if(view()->exists('themes.sunny-meadow.partials.closing'))
            @include('themes.sunny-meadow.partials.closing')
        @endif
    </main>

    @if(view()->exists('themes.sunny-meadow.partials.bottom-nav'))
        @include('themes.sunny-meadow.partials.bottom-nav')
    @endif
@endsection
